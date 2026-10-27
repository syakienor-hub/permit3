<?php

namespace App\Http\Controllers\Divisi;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\Permit;
use App\Models\PermitDocument;
use App\Services\PermitMailNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PermitController extends Controller
{
    /** Tampilkan form wizard buat permit */
    public function create()
    {
        return view('divisi.permits.create');
    }

    /** Simpan permit (draft atau submit) */
    public function store(Request $request)
    {
        $request->validate([
            'tipe'             => 'required|in:Internal,Eksternal',
            'site'             => 'required|in:Madiun,Banyuwangi',
            'nama_pekerjaan'   => 'required|string|max:255',
            'kontraktor'       => 'required|string|max:255',
            'lokasi'           => 'required|string|max:255',
            'tanggal_mulai'    => 'nullable|date',
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_mulai',
            'tanda_tangan'     => $request->input('action') === 'submit' ? 'required|string' : 'nullable|string',
        ]);

        // Validasi dokumen: wajib untuk Eksternal, opsional untuk Internal
        if ($request->input('tipe') === 'Eksternal') {
            $request->validate([
                'dokumen'               => 'required|array|min:1',
                'dokumen.*.nama'        => 'required|string|max:255',
                'dokumen.*.deskripsi'   => 'nullable|string|max:500',
                'dokumen.*.file'        => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            ]);
        } elseif ($request->has('dokumen')) {
            $request->validate([
                'dokumen'               => 'array',
                'dokumen.*.nama'        => 'required|string|max:255',
                'dokumen.*.deskripsi'   => 'nullable|string|max:500',
                'dokumen.*.file'        => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            ]);
        }

        // Idempotency check: prevent duplicate submissions
        $idempotencyKey = $request->input('idempotency_key');
        if ($idempotencyKey) {
            $sessionKey = 'permit_idempotency_' . $idempotencyKey;
            if (session()->has($sessionKey)) {
                return redirect('/divisi/dashboard')->with('info', 'Permit sudah berhasil diajukan / diproses.');
            }
            session()->put($sessionKey, true);
        }

        $user = Auth::user();
        $status = $request->input('action') === 'submit' ? Permit::STATUS_WAITING_INSPECTION : 'Draft';

        $permit = DB::transaction(function () use ($request, $user, $status) {
            // Auto-generate nomor permit
            $noPermit = 'WP-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

            $permit = Permit::create([
                'no_permit'             => $noPermit,
                'user_id'               => $user->id,
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
                'status'       => $status,
                'submitted_at' => $status === 'Draft' ? null : now(),
            ])->save();

            $klasifikasiSelected = (array) $request->input('klasifikasi_pekerjaan', []);
            if (!empty($klasifikasiSelected)) {
                $cIds = Classification::whereIn('code', $klasifikasiSelected)->pluck('id');
                $permit->classifications()->sync($cIds);
            }

            // Simpan dokumen pendukung (Internal maupun Eksternal)
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

        $message = $status === 'Draft'
            ? 'Permit berhasil disimpan sebagai Draft.'
            : 'Permit berhasil diajukan. Lengkapi Daftar Periksa Izin Kerja agar permit dapat direview Staff HSE.';

        if ($status === 'Review Staff') {
            PermitMailNotifier::notifyStaff($permit);
        }

        return redirect('/divisi/dashboard')->with('success', $message);
    }

    /** Tampilkan form edit (hanya untuk Draft/Revision) */
    public function edit($id)
    {
        $userId = Auth::id();
        $permit = Permit::with('documents')->where('user_id', $userId)->where('id', $id)->firstOrFail();

        if (!in_array($permit->status, ['Draft', 'Revision'])) {
            return redirect('/divisi/dashboard')->with('error', 'Permit tidak dapat diedit.');
        }

        return view('divisi.permits.edit', compact('permit'));
    }

    /** Simpan update permit */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $permit = Permit::with('documents')->where('user_id', $userId)->where('id', $id)->firstOrFail();

        if (!in_array($permit->status, ['Draft', 'Revision'])) {
            return redirect('/divisi/dashboard')->with('error', 'Permit tidak dapat diedit.');
        }

        $request->validate([
            'tipe'             => 'required|in:Internal,Eksternal',
            'site'             => 'required|in:Madiun,Banyuwangi',
            'nama_pekerjaan'   => 'required|string|max:255',
            'kontraktor'       => 'required|string|max:255',
            'lokasi'           => 'required|string|max:255',
            'tanggal_mulai'    => 'nullable|date',
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_mulai',
            'tanda_tangan'     => $request->input('action') === 'submit' ? 'required|string' : 'nullable|string',
        ]);

        // Validasi dokumen baru: wajib untuk Eksternal, opsional untuk Internal
        $hasNewDocs = !empty($request->file('dokumen'));

        if ($hasNewDocs) {
            $request->validate([
                'dokumen'             => 'array',
                'dokumen.*.nama'      => 'required|string|max:255',
                'dokumen.*.deskripsi' => 'nullable|string|max:500',
                'dokumen.*.file'      => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            ]);
        }

        // Idempotency check: prevent duplicate submissions
        $idempotencyKey = $request->input('idempotency_key');
        if ($idempotencyKey) {
            $sessionKey = 'permit_idempotency_' . $idempotencyKey;
            if (session()->has($sessionKey)) {
                return redirect('/divisi/dashboard')->with('info', 'Permit sudah berhasil diajukan / diproses.');
            }
            session()->put($sessionKey, true);
        }

        $status = $request->input('action') === 'submit' ? Permit::STATUS_WAITING_INSPECTION : 'Draft';

        DB::transaction(function () use ($request, $permit, $status) {
            // Handle perubahan tipe
            $newTipe = $request->input('tipe');

            // Handle hapus dokumen individual
            if ($request->has('hapus_dokumen')) {
                foreach ($request->input('hapus_dokumen') as $docId) {
                    $doc = PermitDocument::where('permit_id', $permit->id)->find($docId);
                    if ($doc) {
                        Storage::disk('local')->delete($doc->file_path);
                        $doc->delete();
                    }
                }
            }

            $permit->update([
                'tipe'                  => $newTipe,
                'site'                  => $request->input('site', $permit->site ?? 'Madiun'),
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
                'tanda_tangan'          => $request->input('tanda_tangan') ?? $permit->tanda_tangan,
            ]);

            $klasifikasiSelected = (array) $request->input('klasifikasi_pekerjaan', []);
            $cIds = Classification::whereIn('code', $klasifikasiSelected)->pluck('id');
            $permit->classifications()->sync($cIds);

            // Simpan dokumen baru (Internal maupun Eksternal)
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

            $permit->forceFill([
                'status'       => $status,
                'submitted_at' => $status === 'Draft' ? $permit->submitted_at : now(),
            ])->save();
        });

        // Validasi minimal 1 dokumen untuk tipe Eksternal (setelah hapus & tambah dokumen)
        if ($request->input('tipe') === 'Eksternal') {
            $finalCount = $permit->documents()->count();
            if ($finalCount === 0) {
                return back()->withErrors(['tipe' => 'Minimal upload 1 dokumen pendukung untuk permit eksternal.']);
            }
        }

        $message = $status === 'Draft'
            ? 'Perubahan permit berhasil disimpan sebagai Draft.'
            : 'Permit berhasil diajukan. Lengkapi Daftar Periksa Izin Kerja agar permit dapat direview Staff HSE.';

        if ($status === 'Review Staff') {
            PermitMailNotifier::notifyStaff($permit->fresh());
        }

        return redirect('/divisi/dashboard')->with('success', $message);
    }

    public function downloadDocument($permitId, $documentId)
    {
        $userId = Auth::id();
        $permit = Permit::where('user_id', $userId)->where('id', $permitId)->firstOrFail();
        $doc = PermitDocument::where('permit_id', $permit->id)->where('id', $documentId)->firstOrFail();

        $path = $doc->file_path;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path, $doc->nama_dokumen . '.' . pathinfo($doc->file_path, PATHINFO_EXTENSION));
    }
}
