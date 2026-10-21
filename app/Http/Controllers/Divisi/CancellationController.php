<?php

namespace App\Http\Controllers\Divisi;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use App\Services\PermitCancellationService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CancellationController extends Controller
{
    /**
     * Menampilkan daftar permit Active milik Divisi yang dapat dibatalkan.
     */
    public function index()
    {
        $permits = Permit::byDivisi(Auth::id())
            ->whereNotIn('status', ['Closed', 'Cancelled'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('divisi.cancellations.index', compact('permits'));
    }

    /**
     * Menampilkan form pembatalan untuk permit tertentu.
     */
    public function show($id)
    {
        $permit = Permit::byDivisi(Auth::id())->findOrFail($id);

        if (in_array($permit->status, ['Closed', 'Cancelled'])) {
            return redirect('/divisi/cancellations')->with('error', 'Permit yang sudah ditutup atau dibatalkan tidak dapat dibatalkan lagi.');
        }

        return view('divisi.cancellations.show', compact('permit'));
    }

    /**
     * Memproses pembatalan permit.
     */
    public function cancel(Request $request, $id, PermitCancellationService $cancellations)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'tanda_tangan' => 'required|string|max:2000000',
        ]);

        $permit = Permit::byDivisi(Auth::id())->findOrFail($id);

        try {
            $cancellations->cancel($permit, $request->cancellation_reason, [
                'role' => 'Pemohon (Divisi)',
                'name' => Auth::user()->name,
                'signature' => $request->tanda_tangan,
            ]);
        } catch (DomainException $e) {
            return redirect('/divisi/cancellations')->with('error', $e->getMessage());
        }

        return redirect('/divisi/history')->with('success', 'Izin kerja berhasil dibatalkan secara langsung.');
    }
}
