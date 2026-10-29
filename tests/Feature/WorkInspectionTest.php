<?php

namespace Tests\Feature;

use App\Models\Permit;
use App\Models\User;
use App\Models\WorkInspection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WorkInspectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_daftar_periksa_routes_are_removed(): void
    {
        $this->get('/daftar-periksa')->assertNotFound();
        $this->get('/cek-daftar-periksa')->assertNotFound();
        $this->post('/daftar-periksa', [])->assertNotFound();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/work-inspections')->assertRedirect('/login');
        $this->get('/admin/work-inspections/create')->assertRedirect('/login');
    }

    public function test_staff_can_render_create_form_for_own_site(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');

        $this->actingAs($staff)
            ->get('/admin/work-inspections/create')
            ->assertOk()
            ->assertSee('Daftar Periksa Izin Pekerjaan Berisiko Tinggi')
            ->assertSee('Madiun')
            ->assertSee('Staff HSE PT INKA')
            ->assertSee('Item Pengecekan')
            ->assertDontSee('Tanda Tangan Manager');
    }

    public function test_staff_submission_is_forwarded_to_manager_with_staff_signature_only(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');

        $response = $this->actingAs($staff)->post('/admin/work-inspections', $this->payload());

        $inspection = WorkInspection::firstOrFail();

        $response->assertRedirect('/admin/work-inspections/' . $inspection->id);

        $this->assertSame('Madiun', $inspection->site);
        $this->assertSame(WorkInspection::STATUS_REVIEW_MANAGER, $inspection->status);
        $this->assertNull($inspection->reviewed_by);
        $this->assertNotNull($inspection->submitted_at);
        $this->assertNull($inspection->reviewed_at);
        $this->assertSame('', $inspection->tanda_tangan);
        $this->assertSame('data:image/png;base64,staff', $inspection->staff_signature);
        $this->assertSame('ok', $inspection->answer('pemutusan_listrik'));
        $this->assertNull($inspection->manager_nama);
        $this->assertSame('HSE PT INKA', $inspection->divisi_pengaju);
        $this->assertSame('Petugas HSE Madiun', $inspection->petugas_pemeriksa);
        $this->assertSame($staff->name, $inspection->safety_officer_nama);
        $this->assertStringStartsWith('001/DPIKBT/INKA/', $inspection->no_periksa);
    }

    public function test_store_always_uses_staff_site_even_when_site_is_tampered(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');

        $this->actingAs($staff)->post('/admin/work-inspections', $this->payload([
            'site' => 'Banyuwangi',
        ]))->assertRedirect();

        $this->assertSame('Madiun', WorkInspection::firstOrFail()->site);
    }

    public function test_store_requires_staff_signature(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');

        $this->actingAs($staff)
            ->post('/admin/work-inspections', $this->payload([
                'staff_signature' => '',
            ]))
            ->assertSessionHasErrors('staff_signature');

        $this->assertSame(0, WorkInspection::count());
    }

    public function test_store_requires_every_official_item_with_valid_option(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');

        $answers = array_fill_keys(array_keys(WorkInspection::ITEMS), 'ok');
        unset($answers['jalur_akses']);

        $this->actingAs($staff)
            ->post('/admin/work-inspections', $this->payload(['answers' => $answers]))
            ->assertSessionHasErrors('answers.jalur_akses');

        $this->assertSame(0, WorkInspection::count());

        $invalid = array_fill_keys(array_keys(WorkInspection::ITEMS), 'ok');
        $invalid['jalur_akses'] = 'ya';

        $this->actingAs($staff)
            ->post('/admin/work-inspections', $this->payload(['answers' => $invalid]))
            ->assertSessionHasErrors('answers.jalur_akses');

        $this->assertSame(0, WorkInspection::count());
    }

    public function test_staff_only_sees_inspections_from_own_site(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');
        $own = $this->makeInspection(['site' => 'Madiun', 'no_periksa' => '001/DPIKBT/INKA/2026']);
        $other = $this->makeInspection(['site' => 'Banyuwangi', 'no_periksa' => '002/DPIKBT/INKA/2026']);

        $this->actingAs($staff)
            ->get('/admin/work-inspections')
            ->assertOk()
            ->assertSee($own->no_periksa)
            ->assertDontSee($other->no_periksa);

        $this->actingAs($staff)
            ->get('/admin/work-inspections/' . $other->id)
            ->assertNotFound();

        $this->actingAs($staff)
            ->get('/admin/work-inspections/' . $other->id . '/pdf')
            ->assertNotFound();
    }

    public function test_manager_can_view_queue_but_cannot_create(): void
    {
        $manager = $this->makeUser('manager', null);
        $inspection = $this->makeInspection([
            'no_periksa' => '001/DPIKBT/INKA/2026',
            'status' => WorkInspection::STATUS_REVIEW_MANAGER,
        ]);

        $this->actingAs($manager)
            ->get('/admin/work-inspections')
            ->assertOk()
            ->assertSee($inspection->no_periksa);

        $this->actingAs($manager)
            ->get('/admin/work-inspections/' . $inspection->id)
            ->assertOk();

        $this->actingAs($manager)
            ->get('/admin/work-inspections/' . $inspection->id . '/pdf')
            ->assertOk();

        $this->actingAs($manager)
            ->get('/admin/work-inspections/create')
            ->assertForbidden();
    }

    public function test_non_site_staff_and_other_roles_cannot_access(): void
    {
        $staffWithoutSite = $this->makeUser('staff', null);
        $staffOtherSite = $this->makeUser('staff', 'Semarang');
        $divisi = $this->makeUser('divisi', null);

        $this->actingAs($staffWithoutSite)->get('/admin/work-inspections')->assertForbidden();
        $this->actingAs($staffOtherSite)->get('/admin/work-inspections/create')->assertForbidden();
        $this->actingAs($divisi)->get('/admin/work-inspections')->assertForbidden();
    }

    public function test_manager_can_approve_inspection_waiting_for_review(): void
    {
        $manager = $this->makeUser('manager', null);
        $inspection = $this->makeInspection([
            'status' => WorkInspection::STATUS_REVIEW_MANAGER,
            'tanda_tangan' => '',
            'manager_nama' => null,
            'reviewed_at' => null,
        ]);

        $this->actingAs($manager)
            ->post('/admin/work-inspections/' . $inspection->id . '/approve', [
                'manager_nama' => 'Manager HSE PT INKA',
                'tanda_tangan' => 'data:image/png;base64,manager',
            ])
            ->assertRedirect('/admin/work-inspections/' . $inspection->id);

        $inspection->refresh();

        $this->assertSame(WorkInspection::STATUS_APPROVED, $inspection->status);
        $this->assertSame('Manager HSE PT INKA', $inspection->manager_nama);
        $this->assertSame('data:image/png;base64,manager', $inspection->tanda_tangan);
        $this->assertSame('data:image/png;base64,staff', $inspection->staff_signature);
        $this->assertSame($manager->id, $inspection->reviewed_by);
        $this->assertNotNull($inspection->reviewed_at);
    }

    public function test_manager_approval_requires_name_and_signature(): void
    {
        $manager = $this->makeUser('manager', null);
        $inspection = $this->makeInspection([
            'status' => WorkInspection::STATUS_REVIEW_MANAGER,
            'tanda_tangan' => '',
            'manager_nama' => null,
        ]);

        $this->actingAs($manager)
            ->post('/admin/work-inspections/' . $inspection->id . '/approve', [])
            ->assertSessionHasErrors(['manager_nama', 'tanda_tangan']);

        $this->assertSame(WorkInspection::STATUS_REVIEW_MANAGER, $inspection->fresh()->status);
    }

    public function test_manager_can_reject_inspection_waiting_for_review(): void
    {
        $manager = $this->makeUser('manager', null);
        $inspection = $this->makeInspection([
            'status' => WorkInspection::STATUS_REVIEW_MANAGER,
            'tanda_tangan' => '',
            'manager_nama' => null,
        ]);

        $this->actingAs($manager)
            ->post('/admin/work-inspections/' . $inspection->id . '/reject', [
                'rejection_reason' => 'Item pengamanan belum lengkap.',
            ])
            ->assertRedirect('/admin/work-inspections/' . $inspection->id);

        $inspection->refresh();

        $this->assertSame(WorkInspection::STATUS_REJECTED, $inspection->status);
        $this->assertSame('Item pengamanan belum lengkap.', $inspection->rejection_reason);
        $this->assertSame('data:image/png;base64,staff', $inspection->staff_signature);
        $this->assertSame($manager->id, $inspection->reviewed_by);
    }

    public function test_history_only_lists_completed_inspections(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');
        $approved = $this->makeInspection([
            'no_periksa' => '001/DPIKBT/INKA/2026',
            'status' => WorkInspection::STATUS_APPROVED,
            'reviewed_at' => now(),
        ]);
        $pending = $this->makeInspection([
            'no_periksa' => '002/DPIKBT/INKA/2026',
            'status' => WorkInspection::STATUS_PENDING,
        ]);

        $this->actingAs($staff)
            ->get('/admin/work-inspections/history')
            ->assertOk()
            ->assertSee($approved->no_periksa)
            ->assertDontSee($pending->no_periksa);
    }

    public function test_legacy_pending_inspection_can_still_be_approved(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');
        $inspection = $this->makeInspection(['status' => WorkInspection::STATUS_PENDING]);

        $this->actingAs($staff)
            ->post('/admin/work-inspections/' . $inspection->id . '/approve', [
                'safety_officer_nama' => 'Safety Officer Madiun',
                'tanda_tangan' => 'data:image/png;base64,staff',
            ])
            ->assertRedirect('/admin/work-inspections/' . $inspection->id);

        $inspection->refresh();

        $this->assertSame(WorkInspection::STATUS_APPROVED, $inspection->status);
        $this->assertSame($staff->id, $inspection->reviewed_by);
        $this->assertSame('data:image/png;base64,staff', $inspection->staff_signature);
    }

    public function test_staff_can_approve_permit_without_any_daftar_periksa(): void
    {
        Mail::fake();

        $staff = $this->makeUser('staff', 'Madiun');
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

        $permit->forceFill([
            'status' => 'Review Staff',
            'submitted_at' => now(),
        ])->save();

        $this->assertSame(0, WorkInspection::count());

        $this->actingAs($staff)
            ->put('/admin/approvals/' . $permit->id, [
                'tanda_tangan' => 'data:image/png;base64,staff',
            ])
            ->assertRedirect('/admin/approvals');

        $this->assertSame('Review Manager', $permit->fresh()->status);
    }

    public function test_staff_can_view_and_download_pdf(): void
    {
        $staff = $this->makeUser('staff', 'Madiun');
        $inspection = $this->makeInspection([
            'no_periksa' => '001/DPIKBT/INKA/2026',
            'status' => WorkInspection::STATUS_APPROVED,
        ]);

        $this->actingAs($staff)
            ->get('/admin/work-inspections/' . $inspection->id . '/pdf')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($staff)
            ->get('/admin/work-inspections/' . $inspection->id . '/pdf/download')
            ->assertOk()
            ->assertDownload('Daftar-Periksa-001-DPIKBT-INKA-2026.pdf');
    }

    private function makeUser(string $role, ?string $site): User
    {
        return User::factory()->create([
            'role' => $role,
            'site' => $site,
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'otorisasi' => 'HSE/PT INKA/2026',
            'nama_pekerjaan' => 'Perbaikan pipa steam',
            'jenis_peralatan' => 'Mesin las',
            'material' => 'Pipa steam',
            'lokasi' => 'Area Produksi',
            'tanggal_mulai' => '2026-10-25',
            'petugas_pemeriksa' => 'Petugas HSE Madiun',
            'answers' => array_fill_keys(array_keys(WorkInspection::ITEMS), 'ok'),
            'staff_signature' => 'data:image/png;base64,staff',
        ], $overrides);
    }

    private function makeInspection(array $attributes = []): WorkInspection
    {
        $inspection = WorkInspection::create([
            'no_periksa' => 'DP-' . strtoupper(bin2hex(random_bytes(4))),
            'site' => 'Madiun',
            'tipe' => 'Internal',
            'divisi_pengaju' => 'Divisi Teknik',
            'nama_pekerjaan' => 'Perbaikan pipa steam',
            'kontraktor' => 'PT Contoh Kontraktor',
            'lokasi' => 'Area Produksi',
            'telepon' => '08123456789',
            'tanggal_mulai' => '2026-10-25',
            'petugas_pemeriksa' => 'Petugas HSE Madiun',
            'answers' => array_fill_keys(array_keys(WorkInspection::ITEMS), 'ok'),
            'tanda_tangan' => 'data:image/png;base64,manager',
            'safety_officer_nama' => 'Safety Officer Madiun',
            'manager_nama' => 'Manager HSE PT INKA',
            'staff_signature' => 'data:image/png;base64,staff',
            'status' => WorkInspection::STATUS_APPROVED,
            'submitted_at' => now(),
            'reviewed_at' => now(),
        ]);

        $inspection->forceFill($attributes)->save();

        return $inspection->fresh();
    }
}
