<?php

namespace Tests\Feature;

use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HsePlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_hse_plan_form_can_be_rendered(): void
    {
        $this->get('/ajukan-hse-plan')
            ->assertOk()
            ->assertSee('Ajukan HSE Plan');
    }

    public function test_public_can_submit_hse_plan_with_documents(): void
    {
        Storage::fake('local');

        $response = $this->post('/ajukan-hse-plan', $this->validPayload());

        $response->assertRedirect();

        $hsePlan = HsePlan::firstOrFail();

        $this->assertSame('PT Contoh Kontraktor', $hsePlan->nama_kontraktor);
        $this->assertSame(HsePlan::STATUS_REVIEW, $hsePlan->status);
        $this->assertNotNull($hsePlan->submitted_at);
        $this->assertCount(1, $hsePlan->personil);
        $this->assertSame('Gerinda', $hsePlan->peralatan[0]['nama']);
        $this->assertSame(
            ['jsa', 'sertifikat_personil', 'sertifikat_peralatan'],
            HsePlanDocument::orderBy('id')->pluck('jenis')->all()
        );

        foreach (HsePlanDocument::all() as $document) {
            Storage::disk('local')->assertExists($document->file_path);
        }
    }

    public function test_jsa_and_certificates_are_required(): void
    {
        $payload = $this->validPayload();
        unset($payload['jsa_file'], $payload['sertifikat_personil'], $payload['sertifikat_peralatan']);

        $this->post('/ajukan-hse-plan', $payload)
            ->assertSessionHasErrors(['jsa_file', 'sertifikat_personil', 'sertifikat_peralatan']);

        $this->assertDatabaseCount('hse_plans', 0);
    }

    public function test_msds_is_required_when_using_b3_material(): void
    {
        $payload = $this->validPayload();
        $payload['bahan_b3'] = '1';

        $this->post('/ajukan-hse-plan', $payload)
            ->assertSessionHasErrors('msds_file');
    }

    public function test_public_can_check_status_with_permit_number_and_phone(): void
    {
        $hsePlan = $this->makeHsePlan(['telepon' => '08123456789']);

        $this->post('/cek-hse-plan', [
            'no_hse_plan' => strtolower($hsePlan->no_hse_plan),
            'telepon' => '+62 812 3456 789',
        ])
            ->assertOk()
            ->assertSee($hsePlan->no_hse_plan)
            ->assertSee('Menunggu Persetujuan Manager SHE');
    }

    public function test_wrong_phone_does_not_reveal_hse_plan(): void
    {
        $hsePlan = $this->makeHsePlan(['telepon' => '08123456789']);

        $this->post('/cek-hse-plan', [
            'no_hse_plan' => $hsePlan->no_hse_plan,
            'telepon' => '08999999999',
        ])->assertSessionHasErrors('no_hse_plan');
    }

    public function test_manager_can_view_hse_plan_list_and_detail(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan();

        $this->actingAs($manager)
            ->get('/admin/hse-plans')
            ->assertOk()
            ->assertSee($hsePlan->no_hse_plan)
            ->assertSee('PT Contoh Kontraktor');

        $this->actingAs($manager)
            ->get('/admin/hse-plans/' . $hsePlan->id)
            ->assertOk()
            ->assertSee($hsePlan->no_hse_plan)
            ->assertSee('Setujui HSE Plan');
    }

    public function test_manager_can_approve_hse_plan(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan();

        $this->actingAs($manager)
            ->post('/admin/hse-plans/' . $hsePlan->id . '/approve', [
                'tanda_tangan' => 'data:image/png;base64,abc',
            ])
            ->assertRedirect('/admin/hse-plans/' . $hsePlan->id);

        $hsePlan->refresh();

        $this->assertSame(HsePlan::STATUS_APPROVED, $hsePlan->status);
        $this->assertSame($manager->id, $hsePlan->reviewed_by);
        $this->assertNotNull($hsePlan->reviewed_at);
        $this->assertSame('data:image/png;base64,abc', $hsePlan->manager_signature);
    }

    public function test_manager_can_reject_hse_plan_with_reason(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan();

        $this->actingAs($manager)
            ->post('/admin/hse-plans/' . $hsePlan->id . '/reject', [
                'rejection_reason' => 'JSA belum memuat identifikasi bahaya.',
            ])
            ->assertRedirect('/admin/hse-plans/' . $hsePlan->id);

        $hsePlan->refresh();

        $this->assertSame(HsePlan::STATUS_REJECTED, $hsePlan->status);
        $this->assertSame('JSA belum memuat identifikasi bahaya.', $hsePlan->rejection_reason);
    }

    public function test_staff_cannot_access_hse_plan_review(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'site' => 'Madiun']);

        $this->actingAs($staff)
            ->get('/admin/hse-plans')
            ->assertForbidden();
    }

    private function validPayload(): array
    {
        return [
            'no_po' => 'PO-001',
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
            'personil' => [
                ['nama' => 'Andi', 'tugas' => 'Teknisi'],
            ],
            'apd' => ['helm', 'sepatu'],
            'peralatan' => [
                ['nama' => 'Gerinda', 'jumlah' => '2'],
            ],
            'tanda_tangan' => 'data:image/png;base64,abc',
            'jsa_file' => UploadedFile::fake()->create('jsa.pdf', 100, 'application/pdf'),
            'sertifikat_personil' => [
                UploadedFile::fake()->create('sertifikat-personil.pdf', 100, 'application/pdf'),
            ],
            'sertifikat_peralatan' => [
                UploadedFile::fake()->create('sertifikat-peralatan.pdf', 100, 'application/pdf'),
            ],
        ];
    }

    private function makeHsePlan(array $attributes = []): HsePlan
    {
        $hsePlan = HsePlan::create([
            'no_hse_plan' => 'HSE-2026-' . strtoupper(bin2hex(random_bytes(4))),
            'no_po' => 'PO-001',
            'site' => 'Madiun',
            'tipe' => 'Internal',
            'divisi_pengaju' => 'Divisi Teknik',
            'nama_kontraktor' => 'PT Contoh Kontraktor',
            'nama_pimpinan_proyek' => 'Budi Santoso',
            'telepon' => '08123456789',
            'deskripsi_pekerjaan' => 'Pekerjaan perbaikan atap gudang.',
            'lama_pekerjaan' => '5 hari kerja',
            'personil' => [['nama' => 'Andi', 'tugas' => 'Teknisi']],
            'apd' => ['helm', 'sepatu'],
            'peralatan' => [['nama' => 'Gerinda', 'jumlah' => '2']],
            'tanda_tangan' => 'data:image/png;base64,abc',
            'status' => HsePlan::STATUS_REVIEW,
            'submitted_at' => now(),
        ]);

        $hsePlan->forceFill($attributes)->save();

        return $hsePlan->fresh();
    }
}
