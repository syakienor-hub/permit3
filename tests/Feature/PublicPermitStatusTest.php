<?php

namespace Tests\Feature;

use App\Models\Permit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPermitStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_check_form_can_be_rendered(): void
    {
        $this->get('/cek-permit')
            ->assertOk()
            ->assertSee('Cek Status Permit');
    }

    public function test_applicant_can_check_status_with_matching_permit_and_phone(): void
    {
        $permit = $this->makePermit(['status' => 'Review Staff', 'telepon' => '08123456789']);

        $this->post('/cek-permit', [
            'no_permit' => $permit->no_permit,
            'telepon' => '08123456789',
        ])
            ->assertOk()
            ->assertSee($permit->no_permit)
            ->assertSee('Menunggu Review Staff HSE')
            ->assertSee($permit->nama_pekerjaan);
    }

    public function test_phone_number_formatting_is_normalized(): void
    {
        $permit = $this->makePermit(['status' => 'Active', 'telepon' => '0812-3456-789']);

        $this->post('/cek-permit', [
            'no_permit' => strtolower($permit->no_permit),
            'telepon' => '+62 812 3456 789',
        ])
            ->assertOk()
            ->assertSee($permit->no_permit)
            ->assertSee('Aktif');
    }

    public function test_wrong_phone_does_not_reveal_permit(): void
    {
        $permit = $this->makePermit(['status' => 'Active', 'telepon' => '08123456789']);

        $this->post('/cek-permit', [
            'no_permit' => $permit->no_permit,
            'telepon' => '08999999999',
        ])
            ->assertSessionHasErrors('no_permit')
            ->assertDontSee($permit->nama_pekerjaan);
    }

    public function test_unknown_permit_number_is_rejected(): void
    {
        $this->post('/cek-permit', [
            'no_permit' => 'WP-2026-DEADBEEF',
            'telepon' => '08123456789',
        ])->assertSessionHasErrors('no_permit');
    }

    public function test_cancelled_permit_shows_reason_and_cannot_be_cancelled_again(): void
    {
        $permit = $this->makePermit([
            'status' => 'Cancelled',
            'telepon' => '08123456789',
            'cancellation_reason' => 'Pekerjaan dihentikan karena cuaca ekstrem.',
            'cancelled_at' => now(),
        ]);

        $this->post('/cek-permit', [
            'no_permit' => $permit->no_permit,
            'telepon' => '08123456789',
        ])
            ->assertOk()
            ->assertSee('Permit Dibatalkan')
            ->assertSee('Pekerjaan dihentikan karena cuaca ekstrem.')
            ->assertDontSee('Batalkan Permit');
    }

    public function test_active_permit_shows_cancel_link(): void
    {
        $permit = $this->makePermit(['status' => 'Active', 'telepon' => '08123456789']);

        $this->post('/cek-permit', [
            'no_permit' => $permit->no_permit,
            'telepon' => '08123456789',
        ])
            ->assertOk()
            ->assertSee('Batalkan Permit');
    }

    public function test_permit_without_phone_number_cannot_be_checked(): void
    {
        $permit = $this->makePermit(['status' => 'Review Staff', 'telepon' => null]);

        $this->post('/cek-permit', [
            'no_permit' => $permit->no_permit,
            'telepon' => '08123456789',
        ])->assertSessionHasErrors('no_permit');
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
}
