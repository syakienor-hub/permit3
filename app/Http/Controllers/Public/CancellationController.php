<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use App\Services\PermitCancellationService;
use App\Services\PermitMailNotifier;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CancellationController extends Controller
{
    /** Form pembatalan untuk pemohon publik (signed URL, tanpa login). */
    public function show(Permit $permit)
    {
        return view('public.permits.cancel', [
            'permit' => $permit,
            'cancelUrl' => URL::signedRoute('public.permits.cancel', ['permit' => $permit->id]),
            'successUrl' => URL::signedRoute('public.permits.success', ['permit' => $permit->id]),
        ]);
    }

    /** Proses pembatalan permit publik. */
    public function cancel(Request $request, Permit $permit, PermitCancellationService $cancellations)
    {
        $successUrl = URL::signedRoute('public.permits.success', ['permit' => $permit->id]);

        if (in_array($permit->status, ['Closed', 'Cancelled'], true)) {
            return redirect($successUrl)->with('error', 'Permit yang sudah ditutup atau dibatalkan tidak dapat dibatalkan lagi.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'nama_pemohon' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:50',
            'tanda_tangan' => 'required|string|max:2000000',
            'konfirmasi' => 'accepted',
        ]);

        try {
            $cancellations->cancel($permit, $validated['cancellation_reason'], [
                'role' => 'Pemohon (Publik)',
                'name' => $validated['nama_pemohon'],
                'signature' => $validated['tanda_tangan'],
                'ip' => $request->ip(),
            ]);
        } catch (DomainException $e) {
            return redirect($successUrl)->with('error', $e->getMessage());
        }

        PermitMailNotifier::notifyCancellation($permit->fresh());

        return redirect($successUrl)->with('success', 'Izin kerja berhasil dibatalkan.');
    }
}
