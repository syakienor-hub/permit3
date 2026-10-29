<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkInspection extends Model
{
    public const STATUS_PENDING = 'Menunggu Verifikasi';
    public const STATUS_APPROVED = 'Disetujui';
    public const STATUS_REJECTED = 'Ditolak';

    public const SITES = ['Madiun', 'Banyuwangi'];

    public const ANSWER_LABELS = [
        'izin_lain' => 'Apakah ada izin lain yang telah dimintakan?',
        'listrik_diputus' => 'Apakah telah dilakukan pemutusan aliran listrik?',
        'keran_ditutup' => 'Apakah keran-keran telah ditutup dan pompa diputus aliran listriknya?',
        'pipa_dikosongkan' => 'Apakah pipa yang masih berisi fluida berbahaya telah dikosongkan?',
        'tanda_bahaya' => 'Apakah telah diberikan tanda "BERBAHAYA ADA PERBAIKAN/ADA PEKERJAAN"?',
        'karyawan_diberitahu' => 'Apakah karyawan di sekitar telah diberitahu?',
        'alat_listrik_diputus' => 'Seluruh alat yang digerakkan listrik telah diputus aliran listrik.',
        'pipa_fluida_diputus' => 'Seluruh pipa masuk fluida yang tersambung telah diputus alirannya.',
        'pipa_dikeringkan' => 'Seluruh pipa telah dikeringkan dan tidak lagi mengandung fluida berbahaya.',
        'energi_dilepas' => 'Apakah energi yang tersisa di peralatan/mesin telah dihilangkan atau dilepaskan?',
        'tes_energi' => 'Apakah dilakukan pengetesan bahwa energi tidak lagi tersisa?',
        'masih_ada_aliran' => 'Apakah masih ada aliran listrik atau saluran lain yang berhubungan dengan mesin/peralatan?',
        'supervisor_siap' => 'Apakah Supervisor siap memeriksa tanda bahaya tetap terpasang?',
        'apd_khusus' => 'Apakah APD khusus untuk pekerjaan ini telah tersedia dan siap dipakai?',
        'perancah_baik' => 'Apakah semua perancah/penyangga/tangga telah terpasang dengan baik?',
        'barikade' => 'Apakah telah terpasang barikade/pagar/police line di area kerja?',
        'apar' => 'Apakah telah disediakan APAR dan peralatan darurat lainnya?',
        'evakuasi_diberitahu' => 'Apakah seluruh tenaga kerja diberitahu jalur evakuasi dan titik kumpul?',
        'safety_briefing' => 'Apakah seluruh tenaga kerja telah dilakukan safety briefing?',
    ];

    protected $fillable = [
        'no_periksa',
        'site',
        'tipe',
        'divisi_pengaju',
        'nama_pekerjaan',
        'kontraktor',
        'jenis_peralatan',
        'lokasi',
        'telepon',
        'tanggal_mulai',
        'answers',
        'loto_nama',
        'loto_tanggal',
        'loto_jam',
        'tanda_tangan',
        'supervisor_nama',
        'supervisor_tanggal',
        'supervisor_jam',
        'safety_officer_nama',
        'staff_signature',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'loto_tanggal' => 'date',
        'supervisor_tanggal' => 'date',
        'answers' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeForSite($query, ?string $site)
    {
        if ($site && in_array($site, self::SITES, true)) {
            return $query->where('site', $site);
        }

        return $query;
    }

    public function scopeVisibleTo($query, $user)
    {
        if (($user->role ?? null) === 'staff') {
            return empty($user->site)
                ? $query->whereRaw('1 = 0')
                : $query->where('site', $user->site);
        }

        return $query;
    }

    public function answer(string $key): ?bool
    {
        $value = $this->answers[$key] ?? null;

        return $value === null ? null : (bool) $value;
    }
}
