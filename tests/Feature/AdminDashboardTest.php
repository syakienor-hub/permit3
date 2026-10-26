<?php

namespace Tests\Feature;

use App\Models\Permit;
use App\Models\User;
use App\Models\WorkInspection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_dashboard_shows_site_scoped_summary_and_queues(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'site' => 'Madiun']);

        $permitMadiun = $this->makePermit([
            'site' => 'Madiun',
            'status' => 'Review Staff',
            'nama_pekerjaan' => 'Pekerjaan Pengelasan Madiun',
        ]);
        $permitBanyuwangi = $this->makePermit([
            'site' => 'Banyuwangi',
            'status' => 'Review Staff',
            'nama_pekerjaan' => 'Pekerjaan Pengelasan Banyuwangi',
        ]);
        $inspection = $this->makeInspection(['site' => 'Madiun']);

        $this->actingAs($staff)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Staff HSE')
            ->assertSee('Madiun')
            ->assertSee('Daftar Periksa')
            ->assertSee('Permit Aktif')
            ->assertSee($permitMadiun->no_permit)
            ->assertDontSee($permitBanyuwangi->no_permit)
            ->assertSee($inspection->no_periksa);
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
