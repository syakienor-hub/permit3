<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\HsePlan;
use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class StatusCheckController extends Controller
{
    /** Jenis pengajuan yang bisa dicek beserta nama field nomornya. */
    private const TYPES = [
        'permit' => 'no_permit',
        'hse-plan' => 'no_hse_plan',
    ];

    /** Form cek status terpadu (permit, HSE Plan). */
    public function create(Request $request)
    {
        return view('public.status-check', ['jenis' => $this->resolveType($request->query('jenis'))]);
    }

    /** Cari data berdasarkan jenis pengajuan + nomor + nomor telepon. */
    public function show(Request $request)
    {
        $jenis = $this->resolveType($request->input('jenis'));
        $field = self::TYPES[$jenis];

        $validated = $request->validate([
            'jenis' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            $field => 'required|string|max:100',
            'telepon' => 'required|string|max:50',
        ]);

        $nomor = trim($validated[$field]);
        $telepon = $validated['telepon'];

        return match ($jenis) {
            'permit' => $this->checkPermit($field, $nomor, $telepon),
            'hse-plan' => $this->checkHsePlan($field, $nomor, $telepon),
        };
    }

    private function checkPermit(string $field, string $nomor, string $telepon)
    {
        $noPermit = strtoupper($nomor);
        $phone = $this->normalizePhone($telepon);

        $permit = Permit::where('no_permit', $noPermit)->first();

        if (! $permit || $phone === '' || $this->normalizePhone((string) $permit->telepon) !== $phone) {
            return $this->notFound('permit', $field, $nomor, $telepon, 'Data permit tidak ditemukan. Periksa kembali nomor permit dan nomor telepon yang dipakai saat mengajukan.');
        }

        return view('public.status-check', [
            'jenis' => 'permit',
            'permit' => $permit,
            'successUrl' => URL::signedRoute('public.permits.success', ['permit' => $permit->id]),
            'cancelUrl' => URL::signedRoute('public.permits.cancel.show', ['permit' => $permit->id]),
        ]);
    }

    private function checkHsePlan(string $field, string $nomor, string $telepon)
    {
        $noHsePlan = strtoupper($nomor);
        $phone = $this->normalizePhone($telepon);

        $hsePlan = HsePlan::where('no_hse_plan', $noHsePlan)->first();

        if (! $hsePlan || $phone === '' || $this->normalizePhone((string) $hsePlan->telepon) !== $phone) {
            return $this->notFound('hse-plan', $field, $nomor, $telepon, 'Data HSE Plan tidak ditemukan. Periksa kembali nomor HSE Plan dan nomor telepon yang dipakai saat mengajukan.');
        }

        return view('public.status-check', ['jenis' => 'hse-plan', 'hsePlan' => $hsePlan]);
    }

    private function notFound(string $jenis, string $field, string $nomor, string $telepon, string $message)
    {
        return redirect('/cek-status?jenis=' . urlencode($jenis))
            ->withInput([$field => $nomor, 'telepon' => $telepon])
            ->withErrors([$field => $message]);
    }

    private function resolveType(mixed $jenis): string
    {
        return is_string($jenis) && array_key_exists($jenis, self::TYPES) ? $jenis : 'permit';
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
