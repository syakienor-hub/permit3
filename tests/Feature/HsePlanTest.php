<?php

namespace Tests\Feature;

use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use App\Models\User;
use Database\Seeders\HsePlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class HsePlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_hse_plan_form_can_be_rendered(): void
    {
        $this->get('/ajukan-hse-plan')
            ->assertOk()
            ->assertSee('Form HSE Plan')
            ->assertSee('Simpan File HSE Plan');
    }

    public function test_public_can_submit_hse_plan_with_documents(): void
    {
        Storage::fake('local');

        $response = $this->post('/ajukan-hse-plan', $this->validPayload());

        $response->assertRedirect();

        $hsePlan = HsePlan::firstOrFail();

        $this->assertSame('PT Contoh Kontraktor', $hsePlan->nama_kontraktor);
        $this->assertMatchesRegularExpression('#^\d{3}/HSEPLAN/HSE/\d{4}$#', $hsePlan->no_hse_plan);
        $this->assertSame(HsePlan::STATUS_FINAL, $hsePlan->status);
        $this->assertNotNull($hsePlan->submitted_at);
        $this->assertNotNull($hsePlan->manager_signature);
        $this->assertCount(1, $hsePlan->personil);
        $this->assertSame('Gerinda', $hsePlan->peralatan[0]['nama']);
        $this->assertSame(
            ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'hse_plan_generated'],
            HsePlanDocument::orderBy('id')->pluck('jenis')->all()
        );

        foreach (HsePlanDocument::all() as $document) {
            Storage::disk('local')->assertExists($document->file_path);
        }

        $this->get(URL::signedRoute('public.hse-plans.file', ['hsePlan' => $hsePlan->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_hse_plan_can_be_submitted_without_documents_and_unanswered_questions(): void
    {
        Storage::fake('local');

        $payload = $this->validPayload();
        unset(
            $payload['jsa_file'],
            $payload['sertifikat_personil'],
            $payload['sertifikat_peralatan'],
            $payload['bahan_b3'],
            $payload['limbah_b3'],
            $payload['prosedur_kerja'],
            $payload['rencana_inspeksi'],
            $payload['tanggap_darurat'],
            $payload['struktur_organisasi'],
        );

        $this->post('/ajukan-hse-plan', $payload)->assertRedirect();

        $this->assertDatabaseCount('hse_plans', 1);
        $this->assertDatabaseCount('hse_plan_documents', 1);
        $this->assertDatabaseHas('hse_plan_documents', ['jenis' => 'hse_plan_generated']);

        $document = HsePlanDocument::where('jenis', 'hse_plan_generated')->firstOrFail();
        Storage::disk('local')->assertExists($document->file_path);
    }

    public function test_final_hse_plan_does_not_enter_manager_review_queue(): void
    {
        Storage::fake('local');

        $manager = User::factory()->create(['role' => 'manager']);

        $this->post('/ajukan-hse-plan', $this->validPayload())->assertRedirect();

        $hsePlan = HsePlan::firstOrFail();

        $this->actingAs($manager)
            ->get('/admin/hse-plans')
            ->assertOk()
            ->assertDontSee($hsePlan->no_hse_plan);
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
            ->assertSee('Menunggu Persetujuan Manager HSE');
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

    public function test_manager_can_view_hse_plan_pdf_without_downloading(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan();

        $this->actingAs($manager)
            ->get('/admin/hse-plans/' . $hsePlan->id . '/pdf')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_manager_can_view_supporting_document_inline(): void
    {
        Storage::fake('local');

        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan();
        Storage::disk('local')->put('hse-plans/' . $hsePlan->id . '/dokumen.pdf', '%PDF-1.4 test');

        $document = HsePlanDocument::create([
            'hse_plan_id' => $hsePlan->id,
            'jenis' => 'dokumen_lainnya',
            'nama_dokumen' => 'Dokumen Pendukung',
            'file_path' => 'hse-plans/' . $hsePlan->id . '/dokumen.pdf',
            'file_type' => 'pdf',
            'file_size' => 13,
        ]);

        $response = $this->actingAs($manager)
            ->get('/admin/hse-plans/' . $hsePlan->id . '/documents/' . $document->id . '/view');

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('inline', $response->headers->get('content-disposition'));
    }

    public function test_manager_can_download_hse_plan_pdf(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan();

        $this->actingAs($manager)
            ->get('/admin/hse-plans/' . $hsePlan->id . '/pdf/download')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('HSE-Plan-' . str_replace('/', '-', $hsePlan->no_hse_plan) . '.pdf');
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

    public function test_manager_dashboard_combines_safety_permit_and_hse_plan(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan();

        $this->actingAs($manager)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Safety Permit')
            ->assertSee('HSE Plan')
            ->assertSee('Review HSE')
            ->assertSee($hsePlan->no_hse_plan)
            ->assertSee($hsePlan->nama_kontraktor);
    }

    public function test_manager_can_view_hse_plan_history(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $approved = $this->makeHsePlan([
            'status' => HsePlan::STATUS_APPROVED,
            'reviewed_by' => $manager->id,
            'reviewed_at' => now(),
        ]);
        $rejected = $this->makeHsePlan([
            'status' => HsePlan::STATUS_REJECTED,
            'reviewed_by' => $manager->id,
            'reviewed_at' => now(),
            'rejection_reason' => 'Dokumen JSA belum lengkap.',
        ]);
        $pending = $this->makeHsePlan();

        $this->actingAs($manager)
            ->get('/admin/hse-plans/history')
            ->assertOk()
            ->assertSee('History HSE Plan')
            ->assertSee($approved->no_hse_plan)
            ->assertSee($rejected->no_hse_plan)
            ->assertSee('Disetujui')
            ->assertSee('Ditolak')
            ->assertDontSee($pending->no_hse_plan);
    }

    public function test_manager_can_edit_hse_plan_and_refresh_generated_pdf(): void
    {
        Storage::fake('local');

        $manager = User::factory()->create(['role' => 'manager']);
        $hsePlan = $this->makeHsePlan([
            'status' => HsePlan::STATUS_FINAL,
            'manager_signature' => 'data:image/png;base64,abc',
            'reviewed_at' => now(),
        ]);

        $path = 'hse-plans/' . $hsePlan->id . '/HSE-Plan-' . str_replace('/', '-', $hsePlan->no_hse_plan) . '.pdf';
        Storage::disk('local')->put($path, 'old');
        HsePlanDocument::create([
            'hse_plan_id' => $hsePlan->id,
            'jenis' => 'hse_plan_generated',
            'nama_dokumen' => 'HSE Plan Final',
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => 3,
        ]);

        $this->actingAs($manager)
            ->get('/admin/hse-plans/' . $hsePlan->id . '/edit')
            ->assertOk()
            ->assertSee('Edit HSE Plan')
            ->assertSee('Simpan Perubahan');

        $payload = $this->validPayload();
        unset(
            $payload['jsa_file'],
            $payload['sertifikat_personil'],
            $payload['sertifikat_peralatan'],
            $payload['tanda_tangan'],
        );
        $payload['nama_kontraktor'] = 'PT Kontraktor Diperbarui';
        $payload['deskripsi_pekerjaan'] = 'Pekerjaan diperbarui oleh Manager HSE.';
        $payload['personil'] = [['nama' => 'Andi Wijaya', 'tugas' => 'Pengawas']];
        $payload['peralatan'] = [['nama' => 'Bor', 'jumlah' => '3']];

        $this->actingAs($manager)
            ->put('/admin/hse-plans/' . $hsePlan->id, $payload)
            ->assertRedirect('/admin/hse-plans/' . $hsePlan->id)
            ->assertSessionHas('success', 'HSE Plan berhasil diperbarui.');

        $hsePlan->refresh();

        $this->assertSame('PT Kontraktor Diperbarui', $hsePlan->nama_kontraktor);
        $this->assertSame('Pekerjaan diperbarui oleh Manager HSE.', $hsePlan->deskripsi_pekerjaan);
        $this->assertSame('Andi Wijaya', $hsePlan->personil[0]['nama']);
        $this->assertSame('Bor', $hsePlan->peralatan[0]['nama']);
        $this->assertSame(HsePlan::STATUS_FINAL, $hsePlan->status);
        $this->assertStringStartsWith('%PDF', Storage::disk('local')->get($path));
    }

    public function test_hse_plan_seeder_creates_five_varied_examples(): void
    {
        Storage::fake('local');
        User::factory()->create(['role' => 'manager']);

        $this->seed(HsePlanSeeder::class);

        $this->assertSame(5, HsePlan::count());
        $this->assertEqualsCanonicalizing(
            [
                HsePlan::STATUS_REVIEW,
                HsePlan::STATUS_REVIEW,
                HsePlan::STATUS_APPROVED,
                HsePlan::STATUS_REJECTED,
                HsePlan::STATUS_APPROVED,
            ],
            HsePlan::pluck('status')->all()
        );
        $this->assertSame(5, HsePlan::has('documents')->count());
        $this->assertDatabaseHas('hse_plans', ['no_hse_plan' => '001/HSEPLAN/HSE/' . date('Y')]);
        $this->assertDatabaseHas('hse_plans', ['no_hse_plan' => '005/HSEPLAN/HSE/' . date('Y')]);
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
            'jam_mulai' => '08:00',
            'jam_selesai' => '17:00',
            'personil' => [
                ['nama' => 'Andi', 'tugas' => 'Teknisi'],
            ],
            'apd' => ['helm', 'sepatu'],
            'peralatan' => [
                ['nama' => 'Gerinda', 'jumlah' => '2'],
            ],
            'bahan_b3' => '0',
            'limbah_b3' => '0',
            'prosedur_kerja' => '0',
            'rencana_inspeksi' => '0',
            'tanggap_darurat' => '0',
            'struktur_organisasi' => '0',
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
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '17:00',
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
