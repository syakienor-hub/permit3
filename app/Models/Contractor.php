<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    // Status proses HSE Plan (tetap dipertahankan)
    public const STATUS_WAITING = 'Menunggu HSE Plan';
    public const STATUS_REVIEW = 'Review Manager';
    public const STATUS_FORM_SUBMITTED = 'HSE Plan Diajukan';
    public const STATUS_FINAL = 'HSE Plan Final';

    // Status approval Data Kontraktor oleh Manager HSE
    public const APPROVAL_PENDING = 'pending';
    public const APPROVAL_APPROVED = 'approved';
    public const APPROVAL_ON_HOLD = 'on_hold';
    public const APPROVAL_INACTIVE = 'inactive';

    protected $fillable = [
        'token',
        'vendor',
        'pic',
        'jenis_pekerjaan',
        'telepon',
        'nama_pekerjaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_pekerja',
        'lokasi',
        'site',
        'status',
        'approval_status',
        'hse_plan_id',
    ];

    // Status awal kontraktor sebelum disetujui Manager HSE
    protected $attributes = [
        'approval_status' => self::APPROVAL_PENDING,
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function documents()
    {
        return $this->hasMany(ContractorDocument::class);
    }

    public function hsePlan()
    {
        return $this->belongsTo(HsePlan::class);
    }
}
