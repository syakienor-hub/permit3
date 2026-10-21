<?php

namespace Tests\Feature;

use App\Mail\PermitDibatalkanMail;
use App\Models\Permit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PermitCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_page_requires_valid_signature(): void
    {
        $permit = $this->makePermit(['status' => 'Review Staff']);

        $this->get("/ajukan-permit/sukses/{$permit->id}")->assertForbidden();
        $this->get("/ajukan-permit/{$permit->id}/batalkan")->assertForbidden();

        $this->get(URL::signedRoute('public.permits.success', ['permit' => $permit->id]))
            ->assertOk()
            ->assertSee($permit->no_permit);

        $this->get(URL::signedRoute('public.permits.cancel.show', ['permit' => $permit->id]))
            ->assertOk()
            ->assertSee('Batalkan Izin Kerja');
    }

    public function test_public_applicant_can_cancel_permit_and_hse_is_notified(): void
    {
        Mail::fake();

        $staff = User::factory()->create([
            'role' => 'staff',
            'site' => 'Madiun',
            'is_active' => true,
        ]);

        $permit = $this->makePermit([
            'status' => 'Active',
            'site' => 'Madiun',
            'user_id' => null,
            'divisi_pengaju' => 'Divisi Teknik',
        ]);

        $response = $this->post(URL::signedRoute('public.permits.cancel', ['permit' => $permit->id]), [
            'cancellation_reason' => 'Pekerjaan dihentikan karena cuaca ekstrem.',
            'nama_pemohon' => 'Budi Santoso',
            'telepon' => '08123456789',
            'tanda_tangan' => 'data:image/png;base64,AAAA',
            'konfirmasi' => '1',
        ]);

        $response->assertRedirect(URL::signedRoute('public.permits.success', ['permit' => $permit->id]));

        $permit->refresh();
        $this->assertSame('Cancelled', $permit->status);
        $this->assertSame('Pekerjaan dihentikan karena cuaca ekstrem.', $permit->cancellation_reason);
        $this->assertNotNull($permit->cancelled_at);
        $this->assertSame('Pemohon (Publik)', $permit->cancellation_signatures[0]['role']);
        $this->assertSame('Budi Santoso', $permit->cancellation_signatures[0]['name']);

        Mail::assertSent(PermitDibatalkanMail::class, function (PermitDibatalkanMail $mail) use ($permit, $staff) {
            return $mail->permit->id === $permit->id
                && $mail->hasTo($staff->email);
        });
    }

    public function test_closed_permit_cannot_be_cancelled_by_public(): void
    {
        $permit = $this->makePermit(['status' => 'Closed', 'user_id' => null]);

        $this->post(URL::signedRoute('public.permits.cancel', ['permit' => $permit->id]), [
            'cancellation_reason' => 'Tetap ingin membatalkan.',
            'nama_pemohon' => 'Budi Santoso',
            'tanda_tangan' => 'data:image/png;base64,AAAA',
            'konfirmasi' => '1',
        ])->assertRedirect();

        $this->assertSame('Closed', $permit->fresh()->status);
    }

    public function test_staff_cannot_cancel_permit_from_another_site(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'site' => 'Madiun',
            'is_active' => true,
        ]);

        $permit = $this->makePermit(['status' => 'Active', 'site' => 'Banyuwangi']);

        $this->actingAs($staff)->get("/admin/cancellations/{$permit->id}")->assertNotFound();
        $this->actingAs($staff)->post("/admin/cancellations/{$permit->id}", [
            'cancellation_reason' => 'Pelanggaran K3.',
            'tanda_tangan' => 'data:image/png;base64,AAAA',
        ])->assertNotFound();

        $this->assertSame('Active', $permit->fresh()->status);
    }

    public function test_divisi_can_cancel_own_permit(): void
    {
        $divisi = User::factory()->create([
            'role' => 'divisi',
            'is_active' => true,
        ]);

        $permit = $this->makePermit([
            'status' => 'Active',
            'user_id' => $divisi->id,
        ]);

        $this->actingAs($divisi)->post("/divisi/cancellations/{$permit->id}", [
            'cancellation_reason' => 'Pekerjaan dibatalkan oleh divisi.',
            'tanda_tangan' => 'data:image/png;base64,AAAA',
        ])->assertRedirect('/divisi/history');

        $permit->refresh();
        $this->assertSame('Cancelled', $permit->status);
        $this->assertSame('Pemohon (Divisi)', $permit->cancellation_signatures[0]['role']);
        $this->assertSame($divisi->name, $permit->cancellation_signatures[0]['name']);
    }

    public function test_manager_can_cancel_active_permit(): void
    {        $manager = User::factory()->create([
            'role' => 'manager',
            'site' => null,
            'is_active' => true,
        ]);

        $permit = $this->makePermit(['status' => 'Active', 'site' => 'Banyuwangi']);

        $this->actingAs($manager)->post("/admin/cancellations/{$permit->id}", [
            'cancellation_reason' => 'Ditemukan pelanggaran APD di lapangan.',
            'tanda_tangan' => 'data:image/png;base64,AAAA',
        ])->assertRedirect('/admin/history?status=Cancelled');

        $permit->refresh();
        $this->assertSame('Cancelled', $permit->status);
        $this->assertSame('HSE (Manager)', $permit->cancellation_signatures[0]['role']);
        $this->assertSame($manager->name, $permit->cancellation_signatures[0]['name']);
    }

    public function test_admin_can_download_pdf_for_cancelled_permit(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'is_active' => true,
        ]);

        $permit = $this->makePermit([
            'status' => 'Cancelled',
            'cancellation_reason' => 'Pekerjaan dihentikan.',
            'cancelled_at' => now(),
            'cancellation_signatures' => [[
                'role' => 'HSE (Manager)',
                'name' => $manager->name,
                'signature' => 'data:image/png;base64,AAAA',
                'date' => now()->toDateTimeString(),
            ]],
        ]);

        $this->actingAs($manager)
            ->get("/admin/permits/{$permit->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
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
