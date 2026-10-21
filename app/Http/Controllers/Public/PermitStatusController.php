<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PermitStatusController extends Controller
{
    /** Form cek status permit publik. */
    public function create()
    {
        return view('public.permits.status');
    }

    /** Cari permit berdasarkan nomor permit + nomor telepon pemohon. */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'no_permit' => 'required|string|max:50',
            'telepon'   => 'required|string|max:50',
        ]);

        $noPermit = strtoupper(trim($validated['no_permit']));
        $phone = $this->normalizePhone($validated['telepon']);

        $permit = Permit::where('no_permit', $noPermit)->first();

        if (! $permit || $phone === '' || $this->normalizePhone((string) $permit->telepon) !== $phone) {
            return back()
                ->withInput($request->only('no_permit'))
                ->withErrors([
                    'no_permit' => 'Data permit tidak ditemukan. Periksa kembali nomor permit dan nomor telepon yang dipakai saat mengajukan.',
                ]);
        }

        return view('public.permits.status', [
            'permit' => $permit,
            'successUrl' => URL::signedRoute('public.permits.success', ['permit' => $permit->id]),
            'cancelUrl' => URL::signedRoute('public.permits.cancel.show', ['permit' => $permit->id]),
        ]);
    }

    /** Samakan format nomor: buang non-digit, ubah +62 menjadi 0. */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '0' . $digits;
        }

        return $digits;
    }
}
