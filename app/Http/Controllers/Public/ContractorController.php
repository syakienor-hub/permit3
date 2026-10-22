<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\Contractor;
use App\Models\ContractorDocument;
use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContractorController extends Controller
{
    private const FILE_RULES = 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png';

    public function create()
    {
        return view('public.contractors.create', [
            'classifications' => Classification::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'jenis_pekerjaan' => ['required', 'string', Rule::exists('classifications', 'code')],
            'telepon' => 'required|string|max:50',
            'nama_pekerjaan' => 'required|string|max:2000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jumlah_pekerja' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'site' => 'required|in:Madiun,Banyuwangi',
            'action' => 'nullable|in:simpan,isi_form',
            'hse_plan_file' => 'nullable|' . self::FILE_RULES,
            'dokumen_lainnya' => 'nullable|array',
            'dokumen_lainnya.*' => self::FILE_RULES,
        ]);

        $contractor = DB::transaction(function () use ($request) {
            $contractor = Contractor::create([
                'token' => Str::random(40),
                'vendor' => $request->vendor,
                'pic' => $request->pic,
                'jenis_pekerjaan' => $request->jenis_pekerjaan,
                'telepon' => $request->telepon,
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'jumlah_pekerja' => $request->jumlah_pekerja,
                'lokasi' => $request->lokasi,
                'site' => $request->site,
                'status' => Contractor::STATUS_WAITING,
            ]);

            $this->uploadDocuments($request, $contractor, 'hse_plan_file', 'hse_plan_existing');
            $this->uploadDocuments($request, $contractor, 'dokumen_lainnya', 'dokumen_lainnya');

            return $contractor;
        });

        if ($request->input('action') === 'isi_form') {
            return redirect('/ajukan-hse-plan?ref=' . $contractor->token);
        }

        if ($request->hasFile('hse_plan_file')) {
            $hsePlan = $this->createHsePlan($contractor);

            return redirect(URL::signedRoute('public.hse-plans.success', ['hsePlan' => $hsePlan->id]));
        }

        return redirect(URL::signedRoute('public.contractors.success', ['contractor' => $contractor->id]));
    }

    public function success(Contractor $contractor)
    {
        return view('public.contractors.success', [
            'contractor' => $contractor,
            'hsePlanFormUrl' => url('/ajukan-hse-plan?ref=' . $contractor->token),
            'hsePlanStatusUrl' => url('/cek-hse-plan'),
        ]);
    }

    private function createHsePlan(Contractor $contractor): HsePlan
    {
        $year = date('Y');
        $sequence = HsePlan::where('no_hse_plan', 'like', '%/HSEPLAN/HSE/' . $year)->count() + 1;

        $hsePlan = HsePlan::create([
            'no_hse_plan' => sprintf('%03d/HSEPLAN/HSE/%d', $sequence, $year),
            'site' => $contractor->site,
            'tipe' => 'Eksternal',
            'divisi_pengaju' => 'Kontraktor',
            'nama_kontraktor' => $contractor->vendor,
            'nama_pimpinan_proyek' => $contractor->pic,
            'telepon' => $contractor->telepon,
            'deskripsi_pekerjaan' => $contractor->nama_pekerjaan,
            'lama_pekerjaan' => $contractor->tanggal_mulai->format('d/m/Y') . ' s/d ' . $contractor->tanggal_selesai->format('d/m/Y'),
            'tanggal_mulai' => $contractor->tanggal_mulai,
            'tanggal_selesai' => $contractor->tanggal_selesai,
            'personil' => [],
            'apd' => [],
            'peralatan' => [],
            'bahan_b3' => false,
            'limbah_b3' => false,
            'prosedur_kerja' => false,
            'rencana_inspeksi' => false,
            'tanggap_darurat' => false,
            'struktur_organisasi' => false,
            'tanda_tangan' => '',
            'status' => HsePlan::STATUS_REVIEW,
            'submitted_at' => now(),
        ]);

        foreach ($contractor->documents as $document) {
            HsePlanDocument::create([
                'hse_plan_id' => $hsePlan->id,
                'jenis' => $document->jenis,
                'nama_dokumen' => $document->nama_dokumen,
                'file_path' => $document->file_path,
                'file_type' => $document->file_type,
                'file_size' => $document->file_size,
            ]);
        }

        $contractor->forceFill([
            'hse_plan_id' => $hsePlan->id,
            'status' => Contractor::STATUS_REVIEW,
        ])->save();

        return $hsePlan;
    }

    private function uploadDocuments(Request $request, Contractor $contractor, string $field, string $jenis): void
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
            $path = $file->storeAs('contractors/' . $contractor->id, $filename);

            ContractorDocument::create([
                'contractor_id' => $contractor->id,
                'jenis' => $jenis,
                'nama_dokumen' => $jenis === 'hse_plan_existing' ? 'Dokumen HSE Plan' : $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $ext,
                'file_size' => $file->getSize(),
            ]);
        }
    }
}
