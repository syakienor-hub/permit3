<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    /** Daftar site yang didukung (multi-site: Madiun & Banyuwangi) */
    public const SITES = ['Madiun', 'Banyuwangi'];

    public const STATUS_WAITING_INSPECTION = 'Menunggu Daftar Periksa';

    protected $fillable = [
        'no_permit',
        'user_id',
        'work_inspection_id',
        'divisi_pengaju',
        'tipe',
        'site',
        'nama_pekerjaan',
        'kontraktor',
        'lokasi',
        'penanggung_jawab',
        'telepon',
        'perusahaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'klasifikasi_pekerjaan',
        'daftar_pekerja',
        'peralatan_kerja',
        'bahaya_pekerjaan',
        'bahaya_lainnya',
        'tindakan_pencegahan',
        'pencegahan_lainnya',
        'apd',
        'apd_lainnya',
        'tanda_tangan',
    ];

    protected $casts = [
        'tanggal_mulai'         => 'date',
        'tanggal_selesai'       => 'date',
        'submitted_at'          => 'datetime',
        'closed_at'             => 'datetime',
        'klasifikasi_pekerjaan' => 'array',
        'daftar_pekerja'        => 'array',
        'peralatan_kerja'       => 'array',
        'bahaya_pekerjaan'      => 'array',
        'tindakan_pencegahan'   => 'array',
        'apd'                   => 'array',
        'cancelled_at'          => 'datetime',
        'cancellation_signatures' => 'array',
        'approval_signatures'   => 'array',
        'edit_history'          => 'array',
        'tipe'                  => 'string',
    ];

    /** Relasi ke User (pemilik / divisi) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi ke Daftar Periksa Izin Pekerjaan Berisiko Tinggi */
    public function workInspection()
    {
        return $this->belongsTo(WorkInspection::class);
    }

    /** Daftar periksa yang diajukan khusus untuk permit ini (sebelum disetujui). */
    public function requestedInspections()
    {
        return $this->hasMany(WorkInspection::class, 'requested_permit_id');
    }

    public function needsWorkInspection(): bool
    {
        return $this->status === self::STATUS_WAITING_INSPECTION && ! $this->work_inspection_id;
    }

    /** Relasi ke dokumen pendukung */
    public function documents()
    {
        return $this->hasMany(PermitDocument::class);
    }

    /** Relasi ke master klasifikasi pekerjaan */
    public function classifications()
    {
        return $this->belongsToMany(Classification::class);
    }

    /** Scope: permit milik user tertentu */
    public function scopeByDivisi($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /** Scope: filter site tertentu (null/'' = semua site) */
    public function scopeForSite($query, ?string $site)
    {
        if ($site && in_array($site, self::SITES, true)) {
            return $query->where('site', $site);
        }

        return $query;
    }

    /**
     * Scope visibilitas per role:
     * - staff  -> hanya permit sesuai site user (IDOR guard antar-site)
     * - lainnya (manager, superadmin) -> semua site
     */
    public function scopeVisibleTo($query, $user)
    {
        if (($user->role ?? null) === 'staff' && ! empty($user->site)) {
            return $query->where('site', $user->site);
        }

        return $query;
    }
}
