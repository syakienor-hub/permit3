<?php

namespace Tests\Feature;

use App\Models\Permit;
use App\Models\User;
use App\Models\WorkInspection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkInspectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_form_can_be_rendered(): void
    {
        $this->get('/daftar-periksa')
            ->assertOk()
            ->assertSee('Daftar Periksa Izin Pekerjaan Berisiko Tinggi')
            ->assertSee('Kirim Daftar Periksa');
    }

    public function test_public_can_submit_daftar_periksa(): void
    {
        $response = $this->post('/daftar-periksa', $this->payload());

        $response->assertRedirect();

        $inspection = WorkInspection::firstOrFail();

        $this->assertSame('PT Contoh Kontraktor', $inspection->kontraktor);
        $this->assertMatchesRegularExpression('#^\d{3}/DPIKBT/INKA/\d{4}$#', $inspection->no_periksa);
        $this->assertSame(WorkInspection::STATUS_PENDING, $inspection->status);
        $this->assertNotNull($inspection->submitted_at);
        $this->assertTrue($inspection->answer('listrik_diputus'));
        $this->assertFalse($inspection->answer('masih_ada_aliran'));

        $this->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('Daftar Periksa Berhasil Dikirim')
            ->assertSee($inspection->no_periksa);
    }

    public function test_public_can_check_status_with_number_and_phone(): void
    {
        $this->post('/daftar-periksa', $this->payload());
        $inspection = WorkInspection::firstOrFail();

        $this->post('/cek-daftar-periksa', [
            'no_periksa' => $inspection->no_periksa,
            'telepon' => '08123456789',
        ])
            ->assertOk()
            ->assertSee($inspection->no_periksa)
            ->assertSee('Menunggu Verifikasi Staff HSE');
    }

    public function test_wrong_phone_does_not_reveal_daftar_periksa(): void
    {
        $this->post('/daftar-periksa', $this->payload());
        $inspection = WorkInspection::firstOrFail();

        $this->post('/cek-daftar-periksa', [
            'no_periksa' => $inspection->no_periksa,
            'telepon' => '08999999999',
        ])
            ->assertSessionHasErrors('no_periksa')
            ->assertDontSee($inspection->nama_pekerjaan);
    }

    public function test_staff_can_approve_daftar_periksa_with_signature(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'site' => 'Madiun']);
        $inspection = $this->makeInspection();

        $this->actingAs($staff)
            ->post('/admin/work-inspections/' . $inspection->id . '/approve', [
                'safety_officer_nama' => 'Staff HSE Madiun',
                'tanda_tangan' => 'data:image/png;base64,abc',
            ])
            ->assertRedirect('/admin/work-inspections/' . $inspection->id);

        $inspection->refresh();

        $this->assertSame(WorkInspection::STATUS_APPROVED, $inspection->status);
        $this->assertSame($staff->id, $inspection->reviewed_by);
        $this->assertSame('Staff HSE Madiun', $inspection->safety_officer_nama);
        $this->assertSame('data:image/png;base64,abc', $inspection->staff_signature);
        $this->assertNotNull($inspection->reviewed_at);
    }

    public function test_staff_can_reject_daftar_periksa_with_reason(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'site' => 'Madiun']);
        $inspection = $this->makeInspection();

        $this->actingAs($staff)
            ->post('/admin/work-inspections/' . $inspection->id . '/reject', [
                'rejection_reason' => 'APAR belum tersedia di lokasi.',
            ])
            ->assertRedirect('/admin/work-inspections/' . $inspection->id);

        $inspection->refresh();

        $this->assertSame(WorkInspection::STATUS_REJECTED, $inspection->status);
        $this->assertSame('APAR belum tersedia di lokasi.', $inspection->rejection_reason);
        $this->assertNull($inspection->staff_signature);
    }

    public function test_manager_cannot_approve_daftar_periksa(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $inspection = $this->makeInspection();

        $this->actingAs($manager)
            ->post('/admin/work-inspections/' . $inspection->id . '/approve', [
                'safety_officer_nama' => 'Manager HSE',
                'tanda_tangan' => 'data:image/png;base64,abc',
            ])
            ->assertForbidden();

        $this->assertSame(WorkInspection::STATUS_PENDING, $inspection->fresh()->status);
    }

    public function test_staff_index_only_shows_own_site(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'site' => 'Madiun']);
        $own = $this->makeInspection('Menunggu Verifikasi', null, '001/DPIKBT/INKA/2026');

        $other = $this->makeInspection('Menunggu Verifikasi', null, '002/DPIKBT/INKA/2026');
        $other->update(['site' => 'Banyuwangi']);

        $this->actingAs($staff)
            ->get('/admin/work-inspections?status=Semua')
            ->assertOk()
            ->assertSee($own->no_periksa)
            ->assertDontSee($other->no_periksa);
    }

    public function test_staff_from_another_site_cannot_view_daftar_periksa(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'site' => 'Banyuwangi']);
        $inspection = $this->makeInspection();

        $this->actingAs($staff)
            ->get('/admin/work-inspections/' . $inspection->id)
            ->assertNotFound();
    }

    public function test_permit_form_lists_approved_daftar_periksa(): void
    {
        $inspection = $this->makeInspection(WorkInspection::STATUS_APPROVED);

        $this->get('/ajukan-permit')
            ->assertOk()
            ->assertSee('Daftar Periksa Izin Kerja')
            ->assertSee($inspection->no_periksa);
    }

    public function test_permit_form_warns_when_no_approved_daftar_periksa(): void
    {
        $this->get('/ajukan-permit')
            ->assertOk()
            ->assertSee('Belum ada daftar periksa yang disetujui');
    }

    public function test_permit_requires_approved_daftar_periksa(): void
    {
        $this->post('/ajukan-permit', $this->permitPayload(null))
            ->assertSessionHasErrors('work_inspection_id');

        $this->assertDatabaseCount('permits', 0);
    }

    public function test_pending_daftar_periksa_cannot_be_used_for_permit(): void
    {
        $inspection = $this->makeInspection();

        $this->post('/ajukan-permit', $this->permitPayload($inspection->id))
            ->assertSessionHasErrors('work_inspection_id');

        $this->assertDatabaseCount('permits', 0);
    }

    public function test_approved_daftar_periksa_can_be_used_for_permit(): void
    {
        $inspection = $this->makeInspection(WorkInspection::STATUS_APPROVED);

        $this->post('/ajukan-permit', $this->permitPayload($inspection->id))
            ->assertRedirect();

        $permit = Permit::firstOrFail();

        $this->assertSame($inspection->id, $permit->work_inspection_id);
        $this->assertSame('Review Staff', $permit->status);
    }

    public function test_used_daftar_periksa_cannot_be_used_for_second_permit(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'site' => 'Madiun']);
        $inspection = $this->makeInspection(WorkInspection::STATUS_APPROVED, $staff->id);

        Permit::create([
            'no_permit' => 'WP-2026-ABCDEF12',
            'work_inspection_id' => $inspection->id,
            'divisi_pengaju' => 'Divisi Teknik',
            'tipe' => 'Internal',
            'site' => 'Madiun',
            'nama_pekerjaan' => 'Pekerjaan pertama',
            'kontraktor' => 'PT Contoh Kontraktor',
            'lokasi' => 'Area Produksi',
            'status' => 'Review Staff',
        ]);

        $this->post('/ajukan-permit', $this->permitPayload($inspection->id))
            ->assertSessionHasErrors('work_inspection_id');

        $this->assertDatabaseCount('permits', 1);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'divisi_pengaju' => 'Divisi Teknik',
            'tipe' => 'Internal',
            'site' => 'Madiun',
            'nama_pekerjaan' => 'Perbaikan pipa steam',
            'kontraktor' => 'PT Contoh Kontraktor',
            'jenis_peralatan' => 'Mesin las, pipa steam',
            'lokasi' => 'Area Produksi',
            'telepon' => '08123456789',
            'tanggal_mulai' => '2026-10-25',
            'answers' => [
                'izin_lain' => '1',
                'izin_lain_ket' => '',
                'listrik_diputus' => '1',
                'keran_ditutup' => '1',
                'pipa_dikosongkan' => '1',
                'tanda_bahaya' => '1',
                'karyawan_diberitahu' => '1',
                'alat_listrik_diputus' => '1',
                'pipa_fluida_diputus' => '1',
                'pipa_dikeringkan' => '1',
                'energi_dilepas' => '1',
                'tes_energi' => '1',
                'masih_ada_aliran' => '0',
                'supervisor_siap' => '1',
                'apd_khusus' => '1',
                'apd_khusus_ket' => 'Full body harness',
                'perancah_baik' => '1',
                'barikade' => '1',
                'apar' => '1',
                'evakuasi_diberitahu' => '1',
                'safety_briefing' => '1',
            ],
            'loto_nama' => 'Budi',
            'loto_tanggal' => '2026-10-25',
            'loto_jam' => '08:00',
            'tanda_tangan' => 'data:image/png;base64,abc',
            'supervisor_nama' => 'Supervisor A',
            'supervisor_tanggal' => '2026-10-25',
            'supervisor_jam' => '08:30',
        ], $overrides);
    }

    private function makeInspection(
        string $status = WorkInspection::STATUS_PENDING,
        ?int $reviewedBy = null,
        string $noPeriksa = '001/DPIKBT/INKA/2026'
    ): WorkInspection {
        return WorkInspection::create([
            'no_periksa' => $noPeriksa,
            'site' => 'Madiun',
            'tipe' => 'Internal',
            'divisi_pengaju' => 'Divisi Teknik',
            'nama_pekerjaan' => 'Perbaikan pipa steam',
            'kontraktor' => 'PT Contoh Kontraktor',
            'jenis_peralatan' => 'Mesin las',
            'lokasi' => 'Area Produksi',
            'telepon' => '08123456789',
            'tanggal_mulai' => '2026-10-25',
            'answers' => array_fill_keys(array_keys(WorkInspection::ANSWER_LABELS), true),
            'loto_nama' => 'Budi',
            'loto_tanggal' => '2026-10-25',
            'loto_jam' => '08:00',
            'tanda_tangan' => 'data:image/png;base64,abc',
            'status' => $status,
            'submitted_at' => now(),
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => $status === WorkInspection::STATUS_PENDING ? null : now(),
        ]);
    }

    private function permitPayload(?int $inspectionId): array
    {
        return [
            'work_inspection_id' => $inspectionId,
            'divisi_pengaju' => 'Divisi Teknik',
            'tipe' => 'Internal',
            'site' => 'Madiun',
            'nama_pekerjaan' => 'Perbaikan pipa steam',
            'kontraktor' => 'PT Contoh Kontraktor',
            'lokasi' => 'Area Produksi',
            'penanggung_jawab' => 'Supervisor A',
            'telepon' => '08123456789',
            'tanggal_mulai' => '2026-10-25',
            'tanggal_selesai' => '2026-10-26',
            'klasifikasi_pekerjaan' => ['panas'],
            'tanda_tangan' => 'data:image/png;base64,abc',
        ];
    }
}
