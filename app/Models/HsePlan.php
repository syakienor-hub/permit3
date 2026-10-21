<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsePlan extends Model
{
    public const STATUS_REVIEW = 'Review Manager';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';

    public const SITES = ['Madiun', 'Banyuwangi'];

    protected $fillable = [
        'no_hse_plan',
        'no_po',
        'site',
        'tipe',
        'divisi_pengaju',
        'nama_kontraktor',
        'nama_pimpinan_proyek',
        'telepon',
        'deskripsi_pekerjaan',
        'lama_pekerjaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'personil',
        'apd',
        'apd_lainnya',
        'peralatan',
        'bahan_b3',
        'limbah_b3',
        'prosedur_kerja',
        'rencana_inspeksi',
        'tanggap_darurat',
        'struktur_organisasi',
        'tanda_tangan',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'manager_signature',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'personil' => 'array',
        'apd' => 'array',
        'peralatan' => 'array',
        'bahan_b3' => 'boolean',
        'limbah_b3' => 'boolean',
        'prosedur_kerja' => 'boolean',
        'rencana_inspeksi' => 'boolean',
        'tanggap_darurat' => 'boolean',
        'struktur_organisasi' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function documents()
    {
        return $this->hasMany(HsePlanDocument::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeForSite($query, ?string $site)
    {
        if ($site && in_array($site, self::SITES, true)) {
            return $query->where('site', $site);
        }

        return $query;
    }
}
