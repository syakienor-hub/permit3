<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WorkInspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class WorkInspectionController extends Controller
{
    public function create()
    {
        return view('public.work-inspections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'divisi_pengaju' => 'required|string|max:255',
            'tipe' => 'required|in:Internal,Eksternal',
            'site' => 'required|in:Madiun,Banyuwangi',
            'nama_pekerjaan' => 'required|string|max:255',
            'kontraktor' => 'required|string|max:255',
            'jenis_peralatan' => 'nullable|string|max:255',
            'lokasi' => 'required|string|max:255',
            'telepon' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'answers' => 'required|array',
            'answers.izin_lain' => 'required|boolean',
            'answers.izin_lain_ket' => 'nullable|string|max:500',
            'answers.listrik_diputus' => 'required|boolean',
            'answers.keran_ditutup' => 'required|boolean',
            'answers.pipa_dikosongkan' => 'required|boolean',
            'answers.tanda_bahaya' => 'required|boolean',
            'answers.karyawan_diberitahu' => 'required|boolean',
            'answers.alat_listrik_diputus' => 'required|boolean',
            'answers.pipa_fluida_diputus' => 'required|boolean',
            'answers.pipa_dikeringkan' => 'required|boolean',
            'answers.energi_dilepas' => 'required|boolean',
            'answers.tes_energi' => 'required|boolean',
            'answers.masih_ada_aliran' => 'required|boolean',
            'answers.supervisor_siap' => 'required|boolean',
            'answers.apd_khusus' => 'required|boolean',
            'answers.apd_khusus_ket' => 'nullable|string|max:500',
            'answers.perancah_baik' => 'required|boolean',
            'answers.barikade' => 'required|boolean',
            'answers.apar' => 'required|boolean',
            'answers.evakuasi_diberitahu' => 'required|boolean',
            'answers.safety_briefing' => 'required|boolean',
            'loto_nama' => 'required|string|max:255',
            'loto_tanggal' => 'required|date',
            'loto_jam' => 'required|date_format:H:i',
            'tanda_tangan' => 'required|string',
            'supervisor_nama' => 'nullable|string|max:255',
            'supervisor_tanggal' => 'nullable|date',
            'supervisor_jam' => 'nullable|date_format:H:i',
        ]);

        $answers = [];

        foreach (array_keys(WorkInspection::ANSWER_LABELS) as $key) {
            $answers[$key] = $request->boolean('answers.' . $key);
        }

        $answers['izin_lain_ket'] = $validated['answers']['izin_lain_ket'] ?? null;
        $answers['apd_khusus_ket'] = $validated['answers']['apd_khusus_ket'] ?? null;

        $inspection = DB::transaction(function () use ($validated, $answers) {
            $year = date('Y');
            $sequence = WorkInspection::whereYear('created_at', $year)->count() + 1;

            return WorkInspection::create([
                'no_periksa' => sprintf('%03d/DPIKBT/INKA/%d', $sequence, $year),
                'site' => $validated['site'],
                'tipe' => $validated['tipe'],
                'divisi_pengaju' => $validated['divisi_pengaju'],
                'nama_pekerjaan' => $validated['nama_pekerjaan'],
                'kontraktor' => $validated['kontraktor'],
                'jenis_peralatan' => $validated['jenis_peralatan'] ?? null,
                'lokasi' => $validated['lokasi'],
                'telepon' => $validated['telepon'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'answers' => $answers,
                'loto_nama' => $validated['loto_nama'],
                'loto_tanggal' => $validated['loto_tanggal'],
                'loto_jam' => $validated['loto_jam'],
                'tanda_tangan' => $validated['tanda_tangan'],
                'supervisor_nama' => $validated['supervisor_nama'] ?? null,
                'supervisor_tanggal' => $validated['supervisor_tanggal'] ?? null,
                'supervisor_jam' => $validated['supervisor_jam'] ?? null,
                'status' => WorkInspection::STATUS_PENDING,
                'submitted_at' => now(),
            ]);
        });

        return redirect(URL::signedRoute('public.work-inspections.success', ['workInspection' => $inspection->id]));
    }

    public function success(WorkInspection $workInspection)
    {
        return view('public.work-inspections.success', [
            'inspection' => $workInspection,
            'statusUrl' => URL::signedRoute('public.work-inspections.status.form'),
            'successUrl' => URL::signedRoute('public.work-inspections.success', ['workInspection' => $workInspection->id]),
        ]);
    }

    public function statusForm()
    {
        return view('public.work-inspections.status');
    }

    public function statusShow(Request $request)
    {
        $validated = $request->validate([
            'no_periksa' => 'required|string|max:100',
            'telepon' => 'required|string|max:50',
        ]);

        $inspection = WorkInspection::where('no_periksa', $validated['no_periksa'])
            ->where('telepon', $validated['telepon'])
            ->first();

        if (! $inspection) {
            return back()
                ->withInput()
                ->withErrors([
                    'no_periksa' => 'Data daftar periksa tidak ditemukan. Periksa kembali nomor daftar periksa dan nomor telepon yang dipakai saat mengajukan.',
                ]);
        }

        return view('public.work-inspections.status', compact('inspection'));
    }
}
