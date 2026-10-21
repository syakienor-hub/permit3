<?php

namespace Tests\Feature;

use App\Models\Permit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_table_lists_published_permits(): void
    {
        $alpha = $this->makePermit('WP-2026-AAA11111', 'Pengecoran Struktur', 'PT Alpha', 'Madiun');
        $beta = $this->makePermit('WP-2026-BBB22222', 'Pengelasan Pipa', 'PT Beta', 'Banyuwangi');

        $this->get('/')
            ->assertOk()
            ->assertSee($alpha->no_permit)
            ->assertSee($beta->no_permit);
    }

    public function test_can_search_by_permit_number(): void
    {
        $alpha = $this->makePermit('WP-2026-AAA11111', 'Pengecoran Struktur', 'PT Alpha', 'Madiun');
        $beta = $this->makePermit('WP-2026-BBB22222', 'Pengelasan Pipa', 'PT Beta', 'Banyuwangi');

        $this->get('/?search=AAA11111#monitoring')
            ->assertOk()
            ->assertSee($alpha->no_permit)
            ->assertDontSee($beta->no_permit);
    }

    public function test_can_search_by_job_name_contractor_or_location(): void
    {
        $alpha = $this->makePermit('WP-2026-AAA11111', 'Pengecoran Struktur', 'PT Alpha', 'Madiun');
        $beta = $this->makePermit('WP-2026-BBB22222', 'Pengelasan Pipa', 'PT Beta', 'Banyuwangi');

        $this->get('/?search=pengelasan')->assertSee($beta->no_permit)->assertDontSee($alpha->no_permit);
        $this->get('/?search=Alpha')->assertSee($alpha->no_permit)->assertDontSee($beta->no_permit);
        $this->get('/?search=Koridor')->assertSee($alpha->no_permit)->assertSee($beta->no_permit);
    }

    public function test_search_can_be_combined_with_site_filter(): void
    {
        $alpha = $this->makePermit('WP-2026-AAA11111', 'Pengecoran Struktur', 'PT Alpha', 'Madiun');

        $this->get('/?search=Alpha&site=Banyuwangi')
            ->assertOk()
            ->assertDontSee($alpha->no_permit);

        $this->get('/?search=Alpha&site=Madiun')
            ->assertOk()
            ->assertSee($alpha->no_permit);
    }

    public function test_search_input_keeps_submitted_value_and_shows_empty_state(): void
    {
        $this->get('/?search=tidak-ada-permit-ini')
            ->assertOk()
            ->assertSee('value="tidak-ada-permit-ini"', false)
            ->assertSee('Tidak ada permit yang cocok dengan pencarian');
    }

    public function test_draft_permits_are_not_listed(): void
    {
        $draft = $this->makePermit('WP-2026-CCC33333', 'Pekerjaan Draft', 'PT Gamma', 'Madiun', 'Draft');

        $this->get('/')->assertOk()->assertDontSee($draft->no_permit);
    }

    private function makePermit(
        string $noPermit,
        string $namaPekerjaan,
        string $kontraktor,
        string $site,
        string $status = 'Review Staff'
    ): Permit {
        $permit = Permit::create([
            'no_permit' => $noPermit,
            'nama_pekerjaan' => $namaPekerjaan,
            'kontraktor' => $kontraktor,
            'lokasi' => 'Koridor A',
            'tipe' => 'Internal',
            'site' => $site,
            'divisi_pengaju' => 'Divisi Produksi',
            'telepon' => '08123456789',
        ]);

        $permit->forceFill([
            'status' => $status,
            'submitted_at' => now(),
        ])->save();

        return $permit->fresh();
    }
}
