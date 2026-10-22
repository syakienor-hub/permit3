<?php

namespace Tests\Feature;

use App\Models\Classification;
use App\Models\Contractor;
use App\Models\ContractorDocument;
use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use Database\Seeders\ClassificationSeeder;
use Database\Seeders\ContractorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContractorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ClassificationSeeder::class);
        Storage::fake('local');
    }

    public function test_public_contractor_form_can_be_rendered(): void
    {
        $this->get('/data-kontraktor')
            ->assertOk()
            ->assertSee('Tambah Data Kontraktor')
            ->assertSee('Isi Form HSE Plan');
    }

    public function test_contractor_with_existing_hse_plan_is_final_without_manager_review(): void
    {
        $response = $this->post('/data-kontraktor', $this->validPayload([
            'hse_plan_file' => UploadedFile::fake()->create('hse-plan.pdf', 100, 'application/pdf'),
            'dokumen_lainnya' => [
                UploadedFile::fake()->create('npwp.pdf', 100, 'application/pdf'),
            ],
        ]));

        $response->assertRedirect();

        $contractor = Contractor::firstOrFail();

        $this->assertSame(Contractor::STATUS_FINAL, $contractor->status);
        $this->assertNotNull($contractor->hse_plan_id);

        $hsePlan = HsePlan::findOrFail($contractor->hse_plan_id);

        $this->assertSame(HsePlan::STATUS_FINAL, $hsePlan->status);
        $this->assertNotNull($hsePlan->manager_signature);
        $this->assertSame('Eksternal', $hsePlan->tipe);
        $this->assertSame('PT Contoh Kontraktor', $hsePlan->nama_kontraktor);
        $this->assertSame('Budi Santoso', $hsePlan->nama_pimpinan_proyek);
        $this->assertSame('Madiun', $hsePlan->site);
        $this->assertEqualsCanonicalizing(
            ['hse_plan_existing', 'dokumen_lainnya'],
            HsePlanDocument::pluck('jenis')->all()
        );

        foreach (HsePlanDocument::all() as $document) {
            Storage::disk('local')->assertExists($document->file_path);
        }

        $this->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('File HSE Plan Berhasil Disimpan')
            ->assertSee('Lihat File HSE Plan');
    }

    public function test_contractor_without_hse_plan_is_asked_to_fill_the_form(): void
    {
        $response = $this->post('/data-kontraktor', $this->validPayload());

        $response->assertRedirect();

        $contractor = Contractor::firstOrFail();

        $this->assertSame(Contractor::STATUS_WAITING, $contractor->status);
        $this->assertNull($contractor->hse_plan_id);
        $this->assertDatabaseCount('hse_plans', 0);

        $this->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('Data Kontraktor Tersimpan')
            ->assertSee('Isi Form HSE Plan')
            ->assertSee('/ajukan-hse-plan?ref=' . $contractor->token, false);
    }

    public function test_hse_plan_form_is_prefilled_from_contractor_reference(): void
    {
        $contractor = Contractor::create($this->contractorAttributes());

        $this->get('/ajukan-hse-plan?ref=' . $contractor->token)
            ->assertOk()
            ->assertSee('Data kontraktor PT Contoh Kontraktor berhasil dimuat.')
            ->assertSee('name="contractor_ref" value="' . $contractor->token . '"', false)
            ->assertSee('value="PT Contoh Kontraktor"', false)
            ->assertSee('value="Budi Santoso"', false)
            ->assertSee('Banyuwangi');
    }

    public function test_submitting_hse_plan_links_and_copies_contractor_documents(): void
    {
        $contractor = Contractor::create($this->contractorAttributes());
        $contractor->documents()->create([
            'jenis' => 'dokumen_lainnya',
            'nama_dokumen' => 'NPWP.pdf',
            'file_path' => 'contractors/1/npwp.pdf',
            'file_type' => 'pdf',
            'file_size' => 100,
        ]);

        $payload = $this->hsePlanPayload();
        $payload['contractor_ref'] = $contractor->token;

        $response = $this->post('/ajukan-hse-plan', $payload);
        $response->assertRedirect();

        $this->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('File HSE Plan sudah tersimpan')
            ->assertSee('Lihat File HSE Plan');

        $contractor->refresh();
        $hsePlan = HsePlan::firstOrFail();

        $this->assertSame($hsePlan->id, $contractor->hse_plan_id);
        $this->assertSame(Contractor::STATUS_FINAL, $contractor->status);
        $this->assertSame(HsePlan::STATUS_FINAL, $hsePlan->status);
        $this->assertDatabaseHas('hse_plan_documents', [
            'hse_plan_id' => $hsePlan->id,
            'jenis' => 'dokumen_lainnya',
            'file_path' => 'contractors/1/npwp.pdf',
        ]);
        $this->assertDatabaseHas('hse_plan_documents', [
            'hse_plan_id' => $hsePlan->id,
            'jenis' => 'hse_plan_generated',
        ]);
        $this->assertDatabaseHas('contractor_documents', [
            'contractor_id' => $contractor->id,
            'jenis' => 'hse_plan_generated',
        ]);

        $generated = HsePlanDocument::where('jenis', 'hse_plan_generated')->firstOrFail();
        Storage::disk('local')->assertExists($generated->file_path);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'vendor' => 'PT Contoh Kontraktor',
            'pic' => 'Budi Santoso',
            'jenis_pekerjaan' => Classification::firstOrFail()->code,
            'telepon' => '08123456789',
            'nama_pekerjaan' => 'Pekerjaan perbaikan atap gudang.',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'jumlah_pekerja' => 5,
            'lokasi' => 'Gudang Madiun',
            'site' => 'Madiun',
            'action' => 'simpan',
        ], $overrides);
    }

    public function test_contractor_seeder_provides_various_demo_situations(): void
    {
        $this->seed(ContractorSeeder::class);

        $this->assertSame(8, Contractor::count());
        $this->assertSame(3, Contractor::where('status', Contractor::STATUS_WAITING)->count());
        $this->assertSame(5, Contractor::where('status', Contractor::STATUS_FINAL)->count());
        $this->assertSame(5, Contractor::whereNotNull('hse_plan_id')->count());
        $this->assertSame(2, Contractor::whereHas('documents', fn ($q) => $q->where('jenis', 'hse_plan_existing'))->count());
        $this->assertSame(3, Contractor::whereHas('documents', fn ($q) => $q->where('jenis', 'hse_plan_generated'))->count());
        $this->assertSame(6, Contractor::query()->distinct()->count('jenis_pekerjaan'));
        $this->assertEqualsCanonicalizing(['Madiun', 'Banyuwangi'], Contractor::query()->distinct()->pluck('site')->all());
        $this->assertSame(5, HsePlan::where('status', HsePlan::STATUS_FINAL)->count());
        $this->assertGreaterThan(0, ContractorDocument::count());
    }

    private function contractorAttributes(): array
    {
        return [
            'token' => 'contractor-token',
            'vendor' => 'PT Contoh Kontraktor',
            'pic' => 'Budi Santoso',
            'jenis_pekerjaan' => Classification::firstOrFail()->code,
            'telepon' => '08123456789',
            'nama_pekerjaan' => 'Pekerjaan perbaikan atap gudang.',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'jumlah_pekerja' => 5,
            'lokasi' => 'Gudang Madiun',
            'site' => 'Banyuwangi',
            'status' => Contractor::STATUS_WAITING,
        ];
    }

    private function hsePlanPayload(): array
    {
        return [
            'site' => 'Madiun',
            'tipe' => 'Eksternal',
            'divisi_pengaju' => 'Kontraktor',
            'nama_kontraktor' => 'PT Contoh Kontraktor',
            'nama_pimpinan_proyek' => 'Budi Santoso',
            'telepon' => '08123456789',
            'deskripsi_pekerjaan' => 'Pekerjaan perbaikan atap gudang.',
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
}
