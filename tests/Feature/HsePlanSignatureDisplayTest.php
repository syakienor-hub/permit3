<?php

namespace Tests\Feature;

use App\Models\HsePlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HsePlanSignatureDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_contractor_signature_shows_placeholder_instead_of_broken_image(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan(['tanda_tangan' => '']);

        $this->actingAs($manager)
            ->get('/admin/hse-plans/' . $hsePlan->id)
            ->assertOk()
            ->assertSee('Tanda tangan kontraktor tidak tersedia.')
            ->assertDontSee('alt="Tanda tangan kontraktor"', false);
    }

    public function test_contractor_signature_is_rendered_when_available(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan([
            'tanda_tangan' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
        ]);

        $this->actingAs($manager)
            ->get('/admin/hse-plans/' . $hsePlan->id)
            ->assertOk()
            ->assertSee('alt="Tanda tangan kontraktor"', false)
            ->assertDontSee('Tanda tangan kontraktor tidak tersedia.');
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
}
