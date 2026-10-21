<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class HsePlanController extends Controller
{
    private const FILE_RULES = 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png';

    public function create()
    {
        return view('public.hse-plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_po' => 'nullable|string|max:100',
            'site' => 'required|in:Madiun,Banyuwangi',
            'tipe' => 'required|in:Internal,Eksternal',
            'divisi_pengaju' => 'required|string|max:255',
            'nama_kontraktor' => 'required|string|max:255',
            'nama_pimpinan_proyek' => 'required|string|max:255',
            'telepon' => 'required|string|max:50',
            'deskripsi_pekerjaan' => 'required|string|max:2000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'personil' => 'required|array|min:1',
            'personil.*.nama' => 'required|string|max:255',
            'personil.*.tugas' => 'required|string|max:255',
            'apd' => 'required|array|min:1',
            'apd.*' => 'string|max:100',
            'apd_lainnya' => 'nullable|string|max:255',
            'peralatan' => 'required|array|min:1',
            'peralatan.*.nama' => 'required|string|max:255',
            'peralatan.*.jumlah' => 'required|string|max:50',
            'bahan_b3' => 'required|boolean',
            'limbah_b3' => 'required|boolean',
            'prosedur_kerja' => 'required|boolean',
            'rencana_inspeksi' => 'required|boolean',
            'tanggap_darurat' => 'required|boolean',
            'struktur_organisasi' => 'required|boolean',
            'tanda_tangan' => 'required|string',
            'jsa_file' => 'required|' . self::FILE_RULES,
            'sertifikat_personil' => 'required|array|min:1',
            'sertifikat_personil.*' => self::FILE_RULES,
            'sertifikat_peralatan' => 'required|array|min:1',
            'sertifikat_peralatan.*' => self::FILE_RULES,
            'msds_file' => [Rule::requiredIf($request->boolean('bahan_b3')), self::FILE_RULES],
            'limbah_b3_file' => [Rule::requiredIf($request->boolean('limbah_b3')), self::FILE_RULES],
            'prosedur_kerja_file' => [Rule::requiredIf($request->boolean('prosedur_kerja')), self::FILE_RULES],
            'inspeksi_file' => [Rule::requiredIf($request->boolean('rencana_inspeksi')), self::FILE_RULES],
            'struktur_file' => [Rule::requiredIf($request->boolean('struktur_organisasi')), self::FILE_RULES],
        ]);

        $hsePlan = DB::transaction(function () use ($request) {
            $year = date('Y');
            $sequence = HsePlan::where('no_hse_plan', 'like', '%/HSEPLAN/HSE/' . $year)->count() + 1;

            $hsePlan = HsePlan::create([
                'no_hse_plan' => sprintf('%03d/HSEPLAN/HSE/%d', $sequence, $year),
                'no_po' => $request->no_po,
                'site' => $request->site,
                'tipe' => $request->tipe,
                'divisi_pengaju' => $request->divisi_pengaju,
                'nama_kontraktor' => $request->nama_kontraktor,
                'nama_pimpinan_proyek' => $request->nama_pimpinan_proyek,
                'telepon' => $request->telepon,
                'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
                'lama_pekerjaan' => $request->tanggal_mulai . ' s/d ' . $request->tanggal_selesai
                    . ' (' . $request->jam_mulai . ' - ' . $request->jam_selesai . ')',
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'personil' => $this->cleanPersonil($request->input('personil', [])),
                'apd' => array_values((array) $request->input('apd', [])),
                'apd_lainnya' => $request->apd_lainnya,
                'peralatan' => $this->cleanPeralatan($request->input('peralatan', [])),
                'bahan_b3' => $request->boolean('bahan_b3'),
                'limbah_b3' => $request->boolean('limbah_b3'),
                'prosedur_kerja' => $request->boolean('prosedur_kerja'),
                'rencana_inspeksi' => $request->boolean('rencana_inspeksi'),
                'tanggap_darurat' => $request->boolean('tanggap_darurat'),
                'struktur_organisasi' => $request->boolean('struktur_organisasi'),
                'tanda_tangan' => $request->tanda_tangan,
                'status' => HsePlan::STATUS_REVIEW,
                'submitted_at' => now(),
            ]);

            $this->uploadDocuments($request, $hsePlan, 'jsa_file', 'jsa', 'Job Safety Analysis');
            $this->uploadDocuments($request, $hsePlan, 'sertifikat_personil', 'sertifikat_personil');
            $this->uploadDocuments($request, $hsePlan, 'sertifikat_peralatan', 'sertifikat_peralatan');
            $this->uploadDocuments($request, $hsePlan, 'msds_file', 'msds', 'MSDS Bahan B3');
            $this->uploadDocuments($request, $hsePlan, 'limbah_b3_file', 'limbah_b3', 'Rencana Pengelolaan Limbah B3');
            $this->uploadDocuments($request, $hsePlan, 'prosedur_kerja_file', 'prosedur_kerja', 'Prosedur / Instruksi Kerja');
            $this->uploadDocuments($request, $hsePlan, 'inspeksi_file', 'inspeksi', 'Rencana Pemantauan / Inspeksi');
            $this->uploadDocuments($request, $hsePlan, 'struktur_file', 'struktur_organisasi', 'Struktur Organisasi Proyek');

            return $hsePlan;
        });

        return redirect(URL::signedRoute('public.hse-plans.success', ['hsePlan' => $hsePlan->id]));
    }

    public function success(HsePlan $hsePlan)
    {
        return view('public.hse-plans.success', [
            'hsePlan' => $hsePlan,
            'statusUrl' => url('/cek-hse-plan'),
        ]);
    }

    public function statusForm()
    {
        return view('public.hse-plans.status');
    }

    public function statusShow(Request $request)
    {
        $validated = $request->validate([
            'no_hse_plan' => 'required|string|max:50',
            'telepon' => 'required|string|max:50',
        ]);

        $noHsePlan = strtoupper(trim($validated['no_hse_plan']));
        $phone = $this->normalizePhone($validated['telepon']);

        $hsePlan = HsePlan::where('no_hse_plan', $noHsePlan)->first();

        if (! $hsePlan || $phone === '' || $this->normalizePhone((string) $hsePlan->telepon) !== $phone) {
            return back()
                ->withInput($request->only('no_hse_plan'))
                ->withErrors([
                    'no_hse_plan' => 'Data HSE Plan tidak ditemukan. Periksa kembali nomor HSE Plan dan nomor telepon yang dipakai saat mengajukan.',
                ]);
        }

        return view('public.hse-plans.status', ['hsePlan' => $hsePlan]);
    }

    private function cleanPersonil(array $rows): array
    {
        return array_values(array_filter($rows, function ($row) {
            return is_array($row) && trim((string) ($row['nama'] ?? '')) !== '';
        }));
    }

    private function cleanPeralatan(array $rows): array
    {
        return array_values(array_filter($rows, function ($row) {
            return is_array($row) && trim((string) ($row['nama'] ?? '')) !== '';
        }));
    }

    private function uploadDocuments(Request $request, HsePlan $hsePlan, string $field, string $jenis, ?string $defaultName = null): void
    {
        $files = $request->file($field);

        if (! $files) {
            return;
        }

        if (! is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension());
            $filename = time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
            $path = $file->storeAs('hse-plans/' . $hsePlan->id, $filename);

            HsePlanDocument::create([
                'hse_plan_id' => $hsePlan->id,
                'jenis' => $jenis,
                'nama_dokumen' => $defaultName ?? $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $ext,
                'file_size' => $file->getSize(),
            ]);
        }
    }

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
