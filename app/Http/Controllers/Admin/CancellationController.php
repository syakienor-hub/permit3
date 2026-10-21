<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use App\Services\PermitCancellationService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CancellationController extends Controller
{
    /** Daftar permit aktif yang dapat dibatalkan oleh HSE. */
    public function index(Request $request)
    {
        $query = Permit::with('user')
            ->visibleTo(Auth::user())
            ->where('status', 'Active');

        if (Auth::user()->role === 'manager' && $request->filled('site')) {
            $query->forSite($request->site);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_permit', 'like', "%{$q}%")
                    ->orWhere('nama_pekerjaan', 'like', "%{$q}%")
                    ->orWhere('kontraktor', 'like', "%{$q}%")
                    ->orWhere('divisi_pengaju', 'like', "%{$q}%");
            });
        }

        $permits = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.cancellations.index', compact('permits'));
    }

    /** Form pembatalan permit oleh HSE. */
    public function show($id)
    {
        $permit = Permit::with('user')->visibleTo(Auth::user())->findOrFail($id);

        if ($permit->status !== 'Active') {
            return redirect('/admin/cancellations')->with('error', 'Hanya permit berstatus Active yang dapat dibatalkan.');
        }

        return view('admin.cancellations.show', compact('permit'));
    }

    /** Proses pembatalan permit oleh HSE. */
    public function cancel(Request $request, $id, PermitCancellationService $cancellations)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'tanda_tangan' => 'required|string|max:2000000',
        ]);

        $permit = Permit::visibleTo(Auth::user())->findOrFail($id);

        if ($permit->status !== 'Active') {
            return redirect('/admin/cancellations')->with('error', 'Hanya permit berstatus Active yang dapat dibatalkan.');
        }

        $roleLabel = Auth::user()->role === 'manager' ? 'HSE (Manager)' : 'HSE (Staff)';

        try {
            $cancellations->cancel($permit, $request->cancellation_reason, [
                'role' => $roleLabel,
                'name' => Auth::user()->name,
                'signature' => $request->tanda_tangan,
                'ip' => $request->ip(),
            ]);
        } catch (DomainException $e) {
            return redirect('/admin/cancellations')->with('error', $e->getMessage());
        }

        return redirect('/admin/history?status=Cancelled')->with('success', 'Izin kerja berhasil dibatalkan.');
    }
}
