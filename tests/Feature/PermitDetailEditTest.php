<?php

namespace Tests\Feature;

use App\Models\Permit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermitDetailEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_form_is_accessible_for_staff_on_owned_site(): void
    {
        $staff = $this->makeStaff('Madiun');
        $permit = $this->makePermit(['status' => 'Review Staff']);

        $this->actingAs($staff)
            ->get("/admin/approvals/{$permit->id}/edit")
            ->assertOk()
            ->assertSee($permit->no_permit)
            ->assertSee('Koreksi Detail');
    }

    public function test_staff_can_edit_detail_and_change_history_is_recorded(): void
    {
        $staff = $this->makeStaff('Madiun');
        $permit = $this->makePermit(['status' => 'Review Staff', 'submitted_at' => now()]);

        $response = $this->actingAs($staff)->put("/admin/approvals/{$permit->id}/edit", [
            'nama_pekerjaan' => 'Pekerjaan Pengelasan Revisi',
            'kontraktor' => 'PT Contoh Kontraktor',
            'lokasi' => 'Area Produksi Blok C',
            'penanggung_jawab' => 'Budi',
            'telepon' => '08123456789',
            'tanggal_mulai' => '2026-10-22',
            'tanggal_selesai' => '2026-10-25',
            'klasifikasi_pekerjaan' => ['panas', 'ketinggian'],
            'daftar_pekerja' => ['welder' => 2],
            'peralatan_kerja' => [
                ['alat' => 'Gerinda', 'jumlah_alat' => '1', 'material' => 'Baja', 'jumlah_material' => '5 kg'],
                ['alat' => '', 'jumlah_alat' => '', 'material' => '', 'jumlah_material' => ''],
            ],
            'bahaya_pekerjaan' => ['percikan_panas'],
            'bahaya_lainnya' => '',
            'tindakan_pencegahan' => ['media_penghalang_api'],
            'pencegahan_lainnya' => '',
            'apd' => ['helm', 'sepatu'],
            'apd_lainnya' => '',
        ]);

        $response->assertRedirect("/admin/approvals/{$permit->id}");

        $permit->refresh();

        $this->assertSame('Pekerjaan Pengelasan Revisi', $permit->nama_pekerjaan);
        $this->assertSame('Area Produksi Blok C', $permit->lokasi);
        $this->assertSame(['helm', 'sepatu'], $permit->apd);
        $this->assertCount(1, $permit->peralatan_kerja);
        $this->assertSame('Review Staff', $permit->status);
        $this->assertNotNull($permit->submitted_at);

        $this->assertCount(1, $permit->edit_history);
        $entry = $permit->edit_history[0];
        $this->assertSame($staff->name, $entry['by']);
        $this->assertSame('Staff HSE', $entry['role']);

        $labels = collect($entry['changes'])->pluck('label')->all();
        $this->assertContains('Nama Pekerjaan', $labels);
        $this->assertContains('Lokasi', $labels);
        $this->assertContains('APD', $labels);
        $this->assertNotContains('Perusahaan', $labels);

        $namaChange = collect($entry['changes'])->firstWhere('label', 'Nama Pekerjaan');
        $this->assertSame('Pekerjaan Pengelasan', $namaChange['from']);
        $this->assertSame('Pekerjaan Pengelasan Revisi', $namaChange['to']);
    }

    public function test_no_history_entry_when_nothing_changed(): void
    {
        $staff = $this->makeStaff('Madiun');
        $permit = $this->makePermit(['status' => 'Review Staff']);

        $this->actingAs($staff)->put("/admin/approvals/{$permit->id}/edit", [
            'nama_pekerjaan' => $permit->nama_pekerjaan,
            'kontraktor' => $permit->kontraktor,
            'lokasi' => $permit->lokasi,
            'penanggung_jawab' => null,
            'telepon' => null,
            'tanggal_mulai' => null,
            'tanggal_selesai' => null,
        ])->assertRedirect("/admin/approvals/{$permit->id}");

        $permit->refresh();

        $this->assertNull($permit->edit_history);
    }

    public function test_manager_cannot_edit_permit_detail(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'site' => 'Madiun', 'is_active' => true]);
        $permit = $this->makePermit(['status' => 'Review Staff']);

        $this->actingAs($manager)
            ->get("/admin/approvals/{$permit->id}/edit")
            ->assertForbidden();
    }

    public function test_staff_cannot_edit_permit_from_another_site(): void
    {
        $staff = $this->makeStaff('Madiun');
        $permit = $this->makePermit(['status' => 'Review Staff', 'site' => 'Banyuwangi']);

        $this->actingAs($staff)
            ->get("/admin/approvals/{$permit->id}/edit")
            ->assertForbidden();
    }

    public function test_detail_cannot_be_edited_after_going_to_manager(): void
    {
        $staff = $this->makeStaff('Madiun');
        $permit = $this->makePermit(['status' => 'Review Manager']);

        $this->actingAs($staff)
            ->get("/admin/approvals/{$permit->id}/edit")
            ->assertForbidden();
    }

    public function test_edit_requires_valid_payload(): void
    {
        $staff = $this->makeStaff('Madiun');
        $permit = $this->makePermit(['status' => 'Review Staff']);

        $this->actingAs($staff)
            ->put("/admin/approvals/{$permit->id}/edit", [
                'nama_pekerjaan' => '',
                'kontraktor' => 'PT Contoh Kontraktor',
                'lokasi' => 'Area Produksi',
            ])
            ->assertSessionHasErrors('nama_pekerjaan');

        $permit->refresh();
        $this->assertSame('Pekerjaan Pengelasan', $permit->nama_pekerjaan);
    }

    private function makeStaff(string $site): User
    {
        return User::factory()->create([
            'role' => 'staff',
            'site' => $site,
            'is_active' => true,
        ]);
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
        ]);

        $permit->forceFill($attributes)->save();

        return $permit->fresh();
    }
}
