<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\Permit;
use App\Models\PermitDocument;
use App\Models\WorkInspection;
use App\Services\PermitMailNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class PermitController extends Controller
{
    /** Tampilkan form wizard publik (tanpa login) */
    public function create()
    {
        $inspections = WorkInspection::query()
            ->approved()
            ->whereDoesntHave('permit')
            ->orderByDesc('reviewed_at')
            ->get();

        return view('public.permits.create', compact('inspections'));
    }

    /** Simpan permit publik — langsung Review Staff, user_id = null */
    public function store(Request $request)
    {
        $request->validate([
            'work_inspection_id' => 'nullable|integer|exists:work_inspections,id',
            'divisi_pengaju'   => 'required|string|max:255',
            'tipe'             => 'required|in:Internal,Eksternal',
            'site'             => 'required|in:Madiun,Banyuwangi',
            'nama_pekerjaan'   => 'required|string|max:255',
            'kontraktor'       => 'required|string|max:255',
            'lokasi'           => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'telepon'          => 'nullable|string|max:50',
            'tanggal_mulai'    => 'nullable|date',
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_mulai',
            'tanda_tangan'     => 'required|string',
        ], [
            'work_inspection_id.exists' => 'Daftar Periksa Izin Kerja tidak ditemukan.',
        ]);

        $inspection = null;

        if ($request->filled('work_inspection_id')) {
            $inspection = WorkInspection::query()
                ->approved()
                ->whereDoesntHave('permit')
                ->find($request->input('work_inspection_id'));

            if (! $inspection) {
                throw ValidationException::withMessages([
                    'work_inspection_id' => 'Daftar Periksa Izin Kerja harus sudah disetujui Staff HSE dan belum dipakai permit lain.',
                ]);
            }

            if ($inspection->site !== $request->input('site') || $inspection->tipe !== $request->input('tipe')) {
                throw ValidationException::withMessages([
                    'work_inspection_id' => 'Site dan tipe permit harus sama dengan Daftar Periksa Izin Kerja yang dipilih.',
                ]);
            }
        }

        if ($request->input('tipe') === 'Eksternal') {
            $request->validate([
                'dokumen'             => 'required|array|min:1',
                'dokumen.*.nama'      => 'required|string|max:255',
                'dokumen.*.deskripsi' => 'nullable|string|max:500',
                'dokumen.*.file'      => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            ]);
        } elseif ($request->has('dokumen')) {
            $request->validate([
                'dokumen'             => 'array',
                'dokumen.*.nama'      => 'required|string|max:255',
                'dokumen.*.deskripsi' => 'nullable|string|max:500',
                'dokumen.*.file'      => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            ]);
        }

        $permit = DB::transaction(function () use ($request, $inspection) {
            $noPermit = 'WP-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

            $permit = Permit::create([
                'no_permit'             => $noPermit,
                'user_id'               => null,
                'work_inspection_id'    => $inspection?->id,
                'divisi_pengaju'        => $request->divisi_pengaju,
                'tipe'                  => $request->input('tipe', 'Internal'),
                'site'                  => $request->input('site', 'Madiun'),
                'nama_pekerjaan'        => $request->nama_pekerjaan,
                'kontraktor'            => $request->kontraktor,
                'lokasi'                => $request->lokasi,
                'penanggung_jawab'      => $request->penanggung_jawab,
                'telepon'               => $request->telepon,
                'tanggal_mulai'         => $request->tanggal_mulai,
                'tanggal_selesai'       => $request->tanggal_selesai,
                'klasifikasi_pekerjaan' => $request->input('klasifikasi_pekerjaan', []),
                'daftar_pekerja'        => $request->input('daftar_pekerja', []),
                'peralatan_kerja'       => $request->input('peralatan_kerja', []),
                'bahaya_pekerjaan'      => $request->input('bahaya_pekerjaan', []),
                'bahaya_lainnya'        => $request->bahaya_lainnya,
                'tindakan_pencegahan'   => $request->input('tindakan_pencegahan', []),
                'pencegahan_lainnya'    => $request->pencegahan_lainnya,
                'apd'                   => $request->input('apd', []),
                'apd_lainnya'           => $request->apd_lainnya,
                'tanda_tangan'          => $request->input('tanda_tangan'),
            ]);

            $permit->forceFill([
                'status'       => $inspection ? 'Review Staff' : Permit::STATUS_WAITING_INSPECTION,
                'submitted_at' => now(),
            ])->save();

            $klasifikasiSelected = (array) $request->input('klasifikasi_pekerjaan', []);
            if (!empty($klasifikasiSelected)) {
                $cIds = Classification::whereIn('code', $klasifikasiSelected)->pluck('id');
                $permit->classifications()->sync($cIds);
            }

            $dokumenInput = $request->input('dokumen', []);
            $dokumenFiles = $request->file('dokumen', []);

            foreach ($dokumenInput as $i => $doc) {
                if (!isset($dokumenFiles[$i]['file'])) {
                    continue;
                }
                $file = $dokumenFiles[$i]['file'];
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
                $path = $file->storeAs('permits/' . $permit->id, $filename);

                PermitDocument::create([
                    'permit_id'    => $permit->id,
                    'nama_dokumen' => $doc['nama'] ?? 'Dokumen',
                    'deskripsi'    => $doc['deskripsi'] ?? null,
                    'file_path'    => $path,
                    'file_type'    => $ext,
                    'file_size'    => $file->getSize(),
                ]);
            }

            return $permit;
        });

        if ($permit->status === 'Review Staff') {
            PermitMailNotifier::notifyStaff($permit);
        }

        return redirect(URL::signedRoute('public.permits.success', ['permit' => $permit->id]));
    }

    /** Halaman sukses setelah submit publik (signed URL, dapat dibuka ulang) */
    public function success(Permit $permit)
    {
        return view('public.permits.success', [
            'permit' => $permit,
            'checklistUrl' => $permit->needsWorkInspection()
                ? URL::signedRoute('public.work-inspections.create-for-permit', ['permit' => $permit->id])
                : null,
            'cancelUrl' => URL::signedRoute('public.permits.cancel.show', ['permit' => $permit->id]),
            'successUrl' => URL::signedRoute('public.permits.success', ['permit' => $permit->id]),
        ]);
    }
}
