<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use App\Models\PermitDocument;
use App\Services\PermitMailNotifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
    /** Field detail yang boleh dikoreksi Staff HSE sebelum approval ke Manager. */
    private const EDITABLE_DETAIL_FIELDS = [
        'nama_pekerjaan'       => 'Nama Pekerjaan',
        'kontraktor'           => 'Perusahaan',
        'lokasi'               => 'Lokasi',
        'penanggung_jawab'     => 'Penanggung Jawab',
        'telepon'              => 'No. Telepon',
        'tanggal_mulai'        => 'Tanggal Mulai',
        'tanggal_selesai'      => 'Tanggal Selesai',
        'klasifikasi_pekerjaan' => 'Klasifikasi Pekerjaan',
        'daftar_pekerja'       => 'Daftar Pekerja',
        'peralatan_kerja'      => 'Peralatan Kerja',
        'bahaya_pekerjaan'     => 'Bahaya Pekerjaan',
        'bahaya_lainnya'       => 'Bahaya Lainnya',
        'tindakan_pencegahan'  => 'Tindakan Pencegahan',
        'pencegahan_lainnya'   => 'Pencegahan Lainnya',
        'apd'                  => 'APD',
        'apd_lainnya'          => 'APD Lainnya',
    ];

    private function getRoleConfig()
    {
        $role = Auth::user()->role;

        if ($role === 'staff') {
            return [
                'roleName' => 'Staff',
                'expectedStatus' => 'Review Staff',
                'nextStatus' => 'Review Manager',
                'nextRoleName' => 'Manager',
                'sortCol' => 'submitted_at',
                'dateColumnLabel' => 'Tgl. Submit'
            ];
        } elseif ($role === 'manager') {
            return [
                'roleName' => 'Manager',
                'expectedStatus' => 'Review Manager',
                'nextStatus' => 'Active',
                'nextRoleName' => 'Aktif',
                'sortCol' => 'updated_at',
                'dateColumnLabel' => 'Tgl. Masuk Manager'
            ];
        }

        abort(403);
    }

    public function index(Request $request)
    {
        $config = $this->getRoleConfig();
        $query = Permit::with(['user', 'classifications'])->visibleTo(Auth::user());

        // Manager melihat semua site + boleh filter per site.
        // Staff otomatis ter-scope ke site-nya via visibleTo().
        if (Auth::user()->role === 'manager' && $request->filled('site')) {
            $query->forSite($request->site);
        }

        if ($request->date === 'today') {
            $query->whereDate('updated_at', today())->where('status', $config['expectedStatus']);
        } elseif ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', $config['expectedStatus']);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_permit', 'like', "%{$q}%")
                    ->orWhere('nama_pekerjaan', 'like', "%{$q}%")
                    ->orWhere('kontraktor', 'like', "%{$q}%");
            });
        }

        $permits = $query->orderBy($config['sortCol'], 'asc')->paginate(15)->withQueryString();

        return view('admin.approvals.index', compact('permits', 'config'));
    }

    public function show($id)
    {
        $config = $this->getRoleConfig();
        $permit = Permit::with(['user', 'documents'])->findOrFail($id);

        // Staff hanya boleh membuka permit dari site-nya sendiri.
        if (Auth::user()->role === 'staff' && $permit->site !== Auth::user()->site) {
            return redirect('/admin/approvals')->with('error', 'Permit site ' . ($permit->site ?? '—') . ' bukan wewenang Anda (site ' . (Auth::user()->site ?? '—') . ').');
        }

        $canReview = $permit->status === $config['expectedStatus'];
        $canEdit = $this->canEditDetail($permit);

        return view('admin.approvals.show', compact('permit', 'canReview', 'canEdit', 'config'));
    }

    /** Hanya Staff HSE site terkait yang boleh mengoreksi detail, dan hanya sebelum approval ke Manager. */
    private function canEditDetail(Permit $permit): bool
    {
        $user = Auth::user();

        return ($user->role ?? null) === 'staff'
            && $permit->status === 'Review Staff'
            && $permit->site === $user->site;
    }

    private function findEditablePermit($id): Permit
    {
        $permit = Permit::with('documents')->findOrFail($id);

        if (! $this->canEditDetail($permit)) {
            abort(403, 'Detail permit hanya dapat diedit oleh Staff HSE site terkait sebelum approval ke Manager.');
        }

        return $permit;
    }

    /** Form koreksi detail permit (Staff HSE, status Review Staff). */
    public function edit($id)
    {
        $permit = $this->findEditablePermit($id);

        return view('admin.approvals.edit', compact('permit'));
    }

    /** Simpan koreksi detail permit + catat riwayat perubahan. */
    public function updateDetail(Request $request, $id)
    {
        $permit = $this->findEditablePermit($id);

        $request->validate([
            'nama_pekerjaan'     => 'required|string|max:255',
            'kontraktor'         => 'required|string|max:255',
            'lokasi'             => 'required|string|max:255',
            'penanggung_jawab'   => 'nullable|string|max:255',
            'telepon'            => 'nullable|string|max:50',
            'tanggal_mulai'      => 'nullable|date',
            'tanggal_selesai'    => 'nullable|date|after_or_equal:tanggal_mulai',
            'bahaya_lainnya'     => 'nullable|string|max:255',
            'pencegahan_lainnya' => 'nullable|string|max:255',
            'apd_lainnya'        => 'nullable|string|max:255',
        ]);

        // Dokumen pendukung boleh ditambahkan / dihapus saat koreksi.
        if (! empty((array) $request->input('dokumen', []))) {
            $request->validate([
                'dokumen'             => 'array',
                'dokumen.*.nama'      => 'required|string|max:255',
                'dokumen.*.deskripsi' => 'nullable|string|max:500',
                'dokumen.*.file'      => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            ]);
        }

        $before = $this->detailSnapshot($permit);

        $peralatan = array_values(array_filter((array) $request->input('peralatan_kerja', []), function ($row) {
            return is_array($row) && collect($row)->contains(fn ($v) => $v !== null && $v !== '');
        }));

        $permit->forceFill([
            'nama_pekerjaan'        => $request->input('nama_pekerjaan'),
            'kontraktor'            => $request->input('kontraktor'),
            'lokasi'                => $request->input('lokasi'),
            'penanggung_jawab'      => $request->input('penanggung_jawab'),
            'telepon'               => $request->input('telepon'),
            'tanggal_mulai'         => $request->input('tanggal_mulai'),
            'tanggal_selesai'       => $request->input('tanggal_selesai'),
            'klasifikasi_pekerjaan' => array_values((array) $request->input('klasifikasi_pekerjaan', [])),
            'daftar_pekerja'        => (array) $request->input('daftar_pekerja', []),
            'peralatan_kerja'       => $peralatan,
            'bahaya_pekerjaan'      => array_values((array) $request->input('bahaya_pekerjaan', [])),
            'bahaya_lainnya'        => $request->input('bahaya_lainnya'),
            'tindakan_pencegahan'   => array_values((array) $request->input('tindakan_pencegahan', [])),
            'pencegahan_lainnya'    => $request->input('pencegahan_lainnya'),
            'apd'                   => array_values((array) $request->input('apd', [])),
            'apd_lainnya'           => $request->input('apd_lainnya'),
        ])->save();

        $after = $this->detailSnapshot($permit);

        $documentNotes = $this->syncDocuments($request, $permit);

        $changes = [];
        foreach (self::EDITABLE_DETAIL_FIELDS as $field => $label) {
            if (($before[$field] ?? '') !== ($after[$field] ?? '')) {
                $changes[] = [
                    'label' => $label,
                    'from'  => $before[$field] ?? '',
                    'to'    => $after[$field] ?? '',
                ];
            }
        }

        foreach ($documentNotes as $note) {
            $changes[] = [
                'label' => 'Dokumen Pendukung',
                'from'  => '',
                'to'    => $note,
            ];
        }

        if (! empty($changes)) {
            $history = $permit->edit_history ?? [];
            $history[] = [
                'at'      => now()->toDateTimeString(),
                'by'      => Auth::user()->name,
                'role'    => 'Staff HSE',
                'changes' => $changes,
            ];
            $permit->forceFill(['edit_history' => $history])->save();
        }

        $message = empty($changes)
            ? 'Tidak ada perubahan pada detail permit.'
            : 'Detail permit berhasil diperbarui (' . count($changes) . ' perubahan) dan tercatat di riwayat.';

        return redirect('/admin/approvals/' . $permit->id)->with('success', $message);
    }

    /**
     * Simpan / hapus dokumen pendukung saat koreksi detail.
     *
     * @return array<int, string> Catatan perubahan dokumen untuk riwayat.
     */
    private function syncDocuments(Request $request, Permit $permit): array
    {
        $notes = [];

        $hapus = (array) $request->input('hapus_dokumen', []);
        foreach ($hapus as $docId) {
            $doc = PermitDocument::where('permit_id', $permit->id)->find($docId);
            if ($doc) {
                Storage::disk('local')->delete($doc->file_path);
                $notes[] = 'Hapus: ' . $doc->nama_dokumen;
                $doc->delete();
            }
        }

        $dokumenInput = (array) $request->input('dokumen', []);
        $dokumenFiles = $request->file('dokumen', []);

        foreach ($dokumenInput as $i => $doc) {
            if (! isset($dokumenFiles[$i]['file'])) {
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

            $notes[] = 'Tambah: ' . ($doc['nama'] ?? 'Dokumen');
        }

        return $notes;
    }

    /** Snapshot nilai tampilan tiap field detail, dipakai untuk diff riwayat. */
    private function detailSnapshot(Permit $permit): array
    {
        $snapshot = [];
        foreach (self::EDITABLE_DETAIL_FIELDS as $field => $label) {
            $snapshot[$field] = $this->formatDetailValue($field, $permit->{$field});
        }

        return $snapshot;
    }

    private function formatDetailValue(string $field, $value): string
    {
        if (in_array($field, ['tanggal_mulai', 'tanggal_selesai'], true)) {
            return $value ? \Illuminate\Support\Carbon::parse($value)->format('d/m/Y') : '';
        }

        if (is_array($value)) {
            if ($field === 'daftar_pekerja') {
                $parts = [];
                foreach ($value as $key => $count) {
                    if ((int) $count > 0) {
                        $parts[] = str_replace('_', ' ', (string) $key) . ': ' . $count;
                    }
                }

                return implode(', ', $parts);
            }

            if ($field === 'peralatan_kerja') {
                $parts = [];
                foreach ($value as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $alat = trim(($row['alat'] ?? '') . (($row['jumlah_alat'] ?? '') !== '' ? ' (' . $row['jumlah_alat'] . ')' : ''));
                    $material = trim(($row['material'] ?? '') . ' ' . ($row['jumlah_material'] ?? ''));
                    if ($alat !== '') {
                        $parts[] = $alat;
                    }
                    if ($material !== '') {
                        $parts[] = $material;
                    }
                }

                return implode(', ', $parts);
            }

            return implode(', ', array_map(fn ($item) => str_replace('_', ' ', (string) $item), $value));
        }

        return (string) ($value ?? '');
    }

    public function update(Request $request, $id)
    {
        $config = $this->getRoleConfig();
        $permit = Permit::findOrFail($id);

        // Staff hanya boleh me-review permit dari site-nya sendiri.
        if (Auth::user()->role === 'staff' && $permit->site !== Auth::user()->site) {
            return redirect('/admin/approvals')->with('error', 'Permit site ' . ($permit->site ?? '—') . ' bukan wewenang Anda (site ' . (Auth::user()->site ?? '—') . ').');
        }

        if ($permit->status !== $config['expectedStatus']) {
            return redirect('/admin/approvals')->with('error', 'Permit tidak valid untuk direview.');
        }

        // Tanpa akun pengaju, tidak ada alur revisi — hanya setujui.
        $request->validate([
            'tanda_tangan' => 'required|string',
        ]);

        $signatures = $permit->approval_signatures ?? [];
        $signatures[] = [
            'role' => $config['roleName'],
            'name' => Auth::user()->name,
            'signature' => $request->tanda_tangan,
            'date' => now()->toDateTimeString(),
        ];

        $permit->forceFill([
            'status' => $config['nextStatus'],
            'catatan_revisi' => null,
            'approval_signatures' => $signatures,
        ])->save();

        if ($config['nextStatus'] === 'Review Manager') {
            PermitMailNotifier::notifyManager($permit->fresh());
        }

        if ($config['nextStatus'] === 'Active') {
            $message = 'Permit berhasil disetujui dan kini berstatus ACTIVE.';
        } else {
            $message = 'Permit berhasil disetujui dan diteruskan ke ' . $config['nextRoleName'] . '.';
        }

        return redirect('/admin/approvals')->with('success', $message);
    }

    public function downloadPdf($id)
    {
        $permit = Permit::findOrFail($id);

        // Staff hanya boleh mengunduh PDF dari site-nya sendiri.
        if (Auth::user()->role === 'staff' && $permit->site !== Auth::user()->site) {
            abort(403, 'Permit bukan wewenang site Anda.');
        }

        $allowedStatuses = ['Review Staff', 'Review Manager', 'Revision', 'Active', 'Closed', 'Cancelled'];
        if (!in_array($permit->status, $allowedStatuses)) {
            abort(403, 'Permit tidak tersedia untuk diakses.');
        }

        $pdf = Pdf::loadView('divisi.permits.pdf', compact('permit'))
            ->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download('Permit-' . str_replace('/', '-', $permit->no_permit) . '.pdf');
    }

    public function downloadDocument($permitId, $documentId)
    {
        $config = $this->getRoleConfig();
        $permit = Permit::findOrFail($permitId);

        // Staff hanya boleh mengunduh dokumen dari site-nya sendiri.
        if (Auth::user()->role === 'staff' && $permit->site !== Auth::user()->site) {
            abort(403, 'Permit bukan wewenang site Anda.');
        }

        // Hanya izinkan download dari permit yang sedang dalam status review
        $allowedStatuses = ['Review Staff', 'Review Manager', 'Revision', 'Active', 'Closed'];
        if (!in_array($permit->status, $allowedStatuses)) {
            abort(403, 'Permit tidak tersedia untuk diakses.');
        }

        $doc = PermitDocument::where('permit_id', $permit->id)->where('id', $documentId)->firstOrFail();

        $path = $doc->file_path;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path, $doc->nama_dokumen . '.' . pathinfo($doc->file_path, PATHINFO_EXTENSION));
    }
}
