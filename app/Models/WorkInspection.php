<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkInspection extends Model
{
    public const STATUS_PENDING = 'Menunggu Verifikasi';
    public const STATUS_REVIEW_MANAGER = 'Review Manager';
    public const STATUS_APPROVED = 'Disetujui';
    public const STATUS_REJECTED = 'Ditolak';

    public const SITES = ['Madiun', 'Banyuwangi'];

    /** Item resmi Form K3LH IV - Rev.0 (HSE PT INKA). */
    public const ITEMS = [
        'jalur_akses' => 'Terdapat jalur akses aman dan bebas halangan ke lokasi pekerjaan.',
        'jalur_evakuasi' => 'Jalur evakuasi dan akses keluar aman dan tidak terhalang.',
        'pencahayaan' => 'Lokasi memiliki pencahayaan yang cukup untuk pekerjaan.',
        'barikade' => 'Telah terpasang barikade/pagar/safety line di area kerja.',
        'tanda_bahaya' => 'Area kerja ditandai dengan jelas (BERBAHAYA ADA PERBAIKAN/ADA PEKERJAAN).',
        'apd' => 'Semua personel memiliki akses ke alat pelindung diri (APD) yang sesuai.',
        'safety_briefing' => 'Semua tenaga kerja sudah menerima safety briefing.',
        'area_bersih' => 'Area kerja bersih dari material atau objek yang dapat menimbulkan kebakaran.',
        'penutup_tahan_api' => 'Tersedia penutup tahan api untuk bahan mudah terbakar yang tidak dapat dipindahkan.',
        'apar_p3k' => 'APAR, P3K dan peralatan keadaan darurat lainnya telah tersedia di lokasi kerja.',
        'ventilasi' => 'Sistem ventilasi diaktifkan jika bekerja di ruang tertutup untuk mencegah penumpukan gas berbahaya.',
        'alat_potong' => 'Alat pemotong atau pengelasan telah diperiksa dan berfungsi dengan baik.',
        'pengosongan_pipa' => 'Dilakukan pengosongan pipa yang mungkin masih berisi fluida/gas berbahaya.',
        'utilitas_bawah_tanah' => 'Pemetaan / penandaan keberadaan utilitas bawah tanah (kabel listrik, pipa gas, dsb.).',
        'tanah_stabil' => 'Tanah di lokasi kerja stabil dan tidak menunjukkan risiko longsor.',
        'penopang_galian' => 'Sistem penopang atau pengaman dinding galian.',
        'alat_berat' => 'Alat berat yang digunakan telah diperiksa dan dalam kondisi baik.',
        'tanda_radiasi' => 'Terdapat tanda bahaya radiasi yang jelas.',
        'monitor_radiasi' => 'Tersedia peralatan monitor radiasi yang berfungsi dengan baik dan telah dikalibrasi.',
        'perisai_radiasi' => 'Perisai radiasi (shielding) dipasang untuk meminimalkan eksposur.',
        'pengujian_atmosfer' => 'Pengujian atmosfer dilakukan untuk memeriksa kadar oksigen, gas beracun, dan gas mudah terbakar.',
        'pengawas_luar' => 'Terdapat pengawas luar ditugaskan dan siaga sepanjang pekerjaan berlangsung.',
        'komunikasi_dua_arah' => 'Sistem komunikasi dua arah antara pekerja dan pengawas berfungsi.',
        'pemutusan_listrik' => 'Telah dilakukan pemutusan aliran listrik.',
        'verifikasi_bebas_tegangan' => 'Verifikasi bebas tegangan dilakukan (pada mesin, alat, dan instalasi kelistrikan) sebelum pekerjaan dimulai.',
        'zona_tegangan_tinggi' => 'Area kerja diberi tanda "Zona Tegangan Tinggi" atau serupa.',
        'isolasi_listrik' => 'Peralatan listrik yang digunakan memiliki isolasi standar.',
        'lockout_tagout' => 'Sistem lockout/tagout diterapkan.',
    ];

    public const ANSWER_OPTIONS = [
        'ok' => 'OK',
        'nok' => 'NOK',
        'na' => 'N/A',
    ];

    protected $fillable = [
        'no_periksa',
        'tanggal_terbit',
        'site',
        'otorisasi',
        'tipe',
        'divisi_pengaju',
        'nama_pekerjaan',
        'kontraktor',
        'jenis_peralatan',
        'material',
        'lokasi',
        'telepon',
        'tanggal_mulai',
        'petugas_pemeriksa',
        'answers',
        'loto_nama',
        'loto_tanggal',
        'loto_jam',
        'tanda_tangan',
        'supervisor_nama',
        'supervisor_tanggal',
        'supervisor_jam',
        'safety_officer_nama',
        'manager_nama',
        'staff_signature',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
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

    public function answer(string $key): ?string
    {
        $value = $this->answers[$key] ?? null;

        if (is_bool($value)) {
            return $value ? 'ok' : 'nok';
        }

        return in_array($value, array_keys(self::ANSWER_OPTIONS), true) ? $value : null;
    }

    public function answerLabel(string $key): ?string
    {
        $value = $this->answer($key);

        return $value ? self::ANSWER_OPTIONS[$value] : null;
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_REVIEW_MANAGER => 'Menunggu Manager HSE',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => $status ?: '—',
        };
    }
}
