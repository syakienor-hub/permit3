<?php

namespace Tests\Feature;

use App\Models\HsePlan;
use App\Models\Permit;
use App\Models\WorkInspection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicStatusCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_unified_status_check_form_can_be_rendered(): void
    {
        $this->get('/cek-status')
            ->assertOk()
            ->assertSee('Cek Status Pengajuan')
            ->assertSee('HSE Plan')
            ->assertSee('Daftar Periksa');
    }

    public function test_permit_can_be_checked_from_unified_page(): void
    {
        $permit = $this->makePermit(['status' => 'Active', 'telepon' => '08123456789']);

        $this->post('/cek-status', [
            'jenis' => 'permit',
            'no_permit' => strtolower($permit->no_permit),
            'telepon' => '+62 812 3456 789',
        ])
            ->assertOk()
            ->assertSee($permit->no_permit)
            ->assertSee('Aktif');
    }

    public function test_hse_plan_can_be_checked_from_unified_page(): void
    {
        $hsePlan = $this->makeHsePlan(['status' => HsePlan::STATUS_APPROVED, 'telepon' => '08123456789']);

        $this->post('/cek-status', [
            'jenis' => 'hse-plan',
            'no_hse_plan' => strtolower($hsePlan->no_hse_plan),
            'telepon' => '0812-3456-789',
        ])
            ->assertOk()
            ->assertSee($hsePlan->no_hse_plan)
            ->assertSee('HSE Plan Disetujui');
    }

    public function test_work_inspection_can_be_checked_from_unified_page(): void
    {
        $inspection = $this->makeInspection([
            'status' => WorkInspection::STATUS_APPROVED,
            'telepon' => '08123456789',
        ]);

        $this->post('/cek-status', [
            'jenis' => 'daftar-periksa',
            'no_periksa' => $inspection->no_periksa,
            'telepon' => '08123456789',
        ])
            ->assertOk()
            ->assertSee($inspection->no_periksa)
            ->assertSee('Disetujui');
    }

    public function test_mismatched_phone_redirects_back_with_error(): void
    {
        $permit = $this->makePermit(['telepon' => '08123456789']);

        $this->post('/cek-status', [
            'jenis' => 'permit',
            'no_permit' => $permit->no_permit,
            'telepon' => '08999999999',
        ])
            ->assertRedirect('/cek-status?jenis=permit')
            ->assertSessionHasErrors('no_permit')
            ->assertSessionHasInput('no_permit', $permit->no_permit);
    }

    private function makePermit(array $attributes = []): Permit
    {
        $permit = Permit::create([
            'no_permit' => 'WP-2026-' . strtoupper(bin2hex(random_bytes(4))),
            'nama_pekerjaan' => 'Pekerjaan Pengelasan',
            'kontraktor' => 'PT Contoh Kontraktor',
            'lokasi' => 'Area Produksi',
            'tipe' => 'Internal',
            'site' => 'Madiun',
            'divisi_pengaju' => 'Divisi Produksi',
            'telepon' => '08123456789',
        ]);

        $permit->forceFill($attributes)->save();

        return $permit->fresh();
    }

    private function makeHsePlan(array $attributes = []): HsePlan
    {
        $hsePlan = HsePlan::create([
            'no_hse_plan' => 'HSE-2026-' . strtoupper(bin2hex(random_bytes(4))),
            'site' => 'Madiun',
            'tipe' => 'Internal',
            'divisi_pengaju' => 'Divisi Teknik',
            'nama_kontraktor' => 'PT Contoh Kontraktor',
            'nama_pimpinan_proyek' => 'Budi Santoso',
            'telepon' => '08123456789',
            'deskripsi_pekerjaan' => 'Pekerjaan perbaikan atap gudang.',
            'lama_pekerjaan' => '5 hari kerja',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '17:00',
            'tanda_tangan' => 'data:image/png;base64,abc',
            'status' => HsePlan::STATUS_REVIEW,
            'submitted_at' => now(),
        ]);

        $hsePlan->forceFill($attributes)->save();

        return $hsePlan->fresh();
    }

    private function makeInspection(array $attributes = []): WorkInspection
    {
        $inspection = WorkInspection::create([
            'no_periksa' => '001/DPIKBT/INKA/2026',
            'site' => 'Madiun',
            'tipe' => 'Internal',
            'divisi_pengaju' => 'Divisi Teknik',
            'nama_pekerjaan' => 'Perbaikan pipa steam',
            'kontraktor' => 'PT Contoh Kontraktor',
            'lokasi' => 'Area Produksi',
            'telepon' => '08123456789',
            'tanggal_mulai' => '2026-10-25',
            'answers' => array_fill_keys(array_keys(WorkInspection::ANSWER_LABELS), true),
            'tanda_tangan' => 'data:image/png;base64,abc',
            'status' => WorkInspection::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $inspection->forceFill($attributes)->save();

        return $inspection->fresh();
    }
}
