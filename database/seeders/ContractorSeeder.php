<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\ContractorDocument;
use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use App\Services\HsePlanPdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ContractorSeeder extends Seeder
{
    private const DOC_LABELS = [
        'jsa' => 'Job Safety Analysis (JSA)',
        'sertifikat_personil' => 'Sertifikat Keahlian Personil',
        'sertifikat_peralatan' => 'Sertifikat Peralatan',
        'msds' => 'MSDS Bahan B3',
        'limbah_b3' => 'Rencana Pengelolaan Limbah B3',
        'prosedur_kerja' => 'Prosedur / Instruksi Kerja',
        'inspeksi' => 'Rencana Pemantauan / Inspeksi',
        'struktur_organisasi' => 'Struktur Organisasi Proyek',
        'hse_plan_existing' => 'Dokumen HSE Plan (Upload Kontraktor)',
    ];

    public function run(): void
    {
        foreach ($this->contractors() as $data) {
            $documents = $data['documents'];
            $planData = $data['plan'];

            unset($data['documents'], $data['plan']);

            $data['token'] = substr(hash('sha256', 'contractor-demo-' . $data['key']), 0, 40);
            unset($data['key']);

            $contractor = Contractor::updateOrCreate(
                ['token' => $data['token']],
                $data
            );

            $hsePlan = null;

            if ($planData) {
                $hsePlan = $this->createApprovedPlan($contractor, $planData);

                $contractor->forceFill([
                    'hse_plan_id' => $hsePlan->id,
                    'status' => Contractor::STATUS_FINAL,
                ])->save();
            }

            if ($hsePlan && $planData['mode'] === 'existing') {
                $this->attachDocument($contractor, $hsePlan, 'hse_plan_existing');
            }

            foreach ($documents as $jenis) {
                $this->attachDocument($contractor, $hsePlan, $jenis);
            }

            if ($hsePlan && $planData['mode'] === 'generated') {
                app(HsePlanPdfService::class)->storeFinal($hsePlan, $contractor);
            }
        }

        $this->command->info('ContractorSeeder: 8 contoh kontraktor berhasil dibuat.');
    }

    private function createApprovedPlan(Contractor $contractor, array $data): HsePlan
    {
        $documents = $data['documents'];

        $hsePlan = HsePlan::updateOrCreate(
            ['no_hse_plan' => $data['no']],
            [
                'no_po' => $data['no_po'],
                'site' => $contractor->site,
                'tipe' => 'Eksternal',
                'divisi_pengaju' => $data['divisi'],
                'nama_kontraktor' => $contractor->vendor,
                'nama_pimpinan_proyek' => $contractor->pic,
                'telepon' => $contractor->telepon,
                'deskripsi_pekerjaan' => $contractor->nama_pekerjaan,
                'lama_pekerjaan' => $data['lama'],
                'tanggal_mulai' => $contractor->tanggal_mulai,
                'tanggal_selesai' => $contractor->tanggal_selesai,
                'jam_mulai' => '08:00',
                'jam_selesai' => '17:00',
                'personil' => [
                    ['nama' => $contractor->pic, 'tugas' => 'Pimpinan Proyek'],
                    ['nama' => 'Petugas K3', 'tugas' => 'Ahli K3 Konstruksi'],
                ],
                'apd' => ['helm', 'sepatu', 'rompi', 'sarung_tangan'],
                'apd_lainnya' => null,
                'peralatan' => [
                    ['nama' => 'Peralatan kerja utama', 'jumlah' => '1 set'],
                    ['nama' => 'Alat pelindung diri cadangan', 'jumlah' => '1 set'],
                ],
                'bahan_b3' => in_array('msds', $documents, true) || in_array('limbah_b3', $documents, true),
                'limbah_b3' => in_array('limbah_b3', $documents, true),
                'prosedur_kerja' => in_array('prosedur_kerja', $documents, true),
                'rencana_inspeksi' => in_array('inspeksi', $documents, true),
                'tanggap_darurat' => true,
                'struktur_organisasi' => in_array('struktur_organisasi', $documents, true),
                'tanda_tangan' => '',
                'status' => HsePlan::STATUS_APPROVED,
                'submitted_at' => $data['submitted_at'],
                'reviewed_by' => \App\Models\User::where('role', 'manager')->value('id'),
                'reviewed_at' => $data['reviewed_at'],
                'rejection_reason' => null,
                'manager_signature' => null,
            ]
        );

        return $hsePlan;
    }

    private function attachDocument(Contractor $contractor, ?HsePlan $hsePlan, string $jenis): void
    {
        $label = self::DOC_LABELS[$jenis] ?? ucwords(str_replace('_', ' ', $jenis));
        $path = 'contractors/' . $contractor->id . '/contoh-' . $jenis . '.pdf';

        if (! Storage::disk('local')->exists($path)) {
            $pdf = Pdf::loadHTML(
                '<h1 style="font-family: sans-serif; font-size: 18px;">' . e($label) . '</h1>' .
                '<p style="font-family: sans-serif; font-size: 12px;">Dokumen contoh untuk ' . e($contractor->vendor) . '</p>' .
                '<p style="font-family: sans-serif; font-size: 12px;">Pekerjaan: ' . e($contractor->nama_pekerjaan) . '</p>'
            )->output();

            Storage::disk('local')->put($path, $pdf);
        }

        $attributes = [
            'nama_dokumen' => $label,
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => Storage::disk('local')->size($path),
        ];

        ContractorDocument::updateOrCreate(
            ['contractor_id' => $contractor->id, 'jenis' => $jenis],
            $attributes
        );

        if ($hsePlan) {
            HsePlanDocument::updateOrCreate(
                ['hse_plan_id' => $hsePlan->id, 'jenis' => $jenis],
                $attributes
            );
        }
    }

    private function contractors(): array
    {
        return [
            [
                'key' => '01',
                'vendor' => 'PT Karya Presisi Nusantara',
                'pic' => 'Andi Prasetyo',
                'jenis_pekerjaan' => 'ketinggian',
                'telepon' => '081234567801',
                'nama_pekerjaan' => 'Perbaikan atap dan talang Gudang B, termasuk bongkar genteng rusak, penggantian rangka ringan, dan pemasangan penutup atap.',
                'tanggal_mulai' => now()->addDays(2)->toDateString(),
                'tanggal_selesai' => now()->addDays(7)->toDateString(),
                'jumlah_pekerja' => '3',
                'lokasi' => 'Gudang B',
                'site' => 'Madiun',
                'status' => Contractor::STATUS_WAITING,
                'documents' => [],
                'plan' => null,
            ],
            [
                'key' => '02',
                'vendor' => 'CV Teknik Mandiri',
                'pic' => 'Siti Rahmawati',
                'jenis_pekerjaan' => 'panas',
                'telepon' => '081234567802',
                'nama_pekerjaan' => 'Pembuatan pagar pengaman area workshop dengan pengelasan dan pemotongan plat baja.',
                'tanggal_mulai' => now()->addDay()->toDateString(),
                'tanggal_selesai' => now()->addDays(3)->toDateString(),
                'jumlah_pekerja' => '4',
                'lokasi' => 'Area Workshop',
                'site' => 'Banyuwangi',
                'status' => Contractor::STATUS_WAITING,
                'documents' => ['jsa', 'sertifikat_personil'],
                'plan' => null,
            ],
            [
                'key' => '03',
                'vendor' => 'PT Sinar Baja Perkasa',
                'pic' => 'Dedi Kurniawan',
                'jenis_pekerjaan' => 'panas',
                'telepon' => '081234567803',
                'nama_pekerjaan' => 'Overhaul rangka kereta di Gedung Perakitan meliputi pengelasan, gerinda, dan pemeriksaan hasil sambungan.',
                'tanggal_mulai' => now()->subDay()->toDateString(),
                'tanggal_selesai' => now()->addDays(4)->toDateString(),
                'jumlah_pekerja' => '12',
                'lokasi' => 'Gedung Perakitan',
                'site' => 'Madiun',
                'status' => Contractor::STATUS_WAITING,
                'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'prosedur_kerja'],
                'plan' => [
                    'no' => '101/HSEPLAN/HSE/' . date('Y'),
                    'no_po' => 'PO/MDN/2026/0142',
                    'divisi' => 'Divisi Produksi',
                    'lama' => '6 hari kerja',
                    'mode' => 'existing',
                    'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'prosedur_kerja'],
                    'submitted_at' => now()->subDays(2),
                    'reviewed_at' => now()->subDay(),
                ],
            ],
            [
                'key' => '04',
                'vendor' => 'PT Mitra Listrik Utama',
                'pic' => 'Rizky Ramadhan',
                'jenis_pekerjaan' => 'tegangan_tinggi',
                'telepon' => '081234567804',
                'nama_pekerjaan' => 'Pemeliharaan instalasi tegangan menengah dan pengujian panel di Gardu Induk.',
                'tanggal_mulai' => now()->addDays(3)->toDateString(),
                'tanggal_selesai' => now()->addDays(10)->toDateString(),
                'jumlah_pekerja' => '8',
                'lokasi' => 'Gardu Induk',
                'site' => 'Banyuwangi',
                'status' => Contractor::STATUS_WAITING,
                'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'prosedur_kerja', 'inspeksi', 'struktur_organisasi'],
                'plan' => [
                    'no' => '102/HSEPLAN/HSE/' . date('Y'),
                    'no_po' => 'PO/BWI/2026/0087',
                    'divisi' => 'Divisi Teknik',
                    'lama' => '8 hari kerja',
                    'mode' => 'generated',
                    'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'prosedur_kerja', 'inspeksi', 'struktur_organisasi'],
                    'submitted_at' => now()->subDay(),
                    'reviewed_at' => now()->subHours(6),
                ],
            ],
            [
                'key' => '05',
                'vendor' => 'CV Bangun Sejahtera',
                'pic' => 'Agus Setiawan',
                'jenis_pekerjaan' => 'galian',
                'telepon' => '081234567805',
                'nama_pekerjaan' => 'Penggalian jalur drainase dan pemasangan pipa di Area Parkir Timur.',
                'tanggal_mulai' => now()->subDays(10)->toDateString(),
                'tanggal_selesai' => now()->subDays(5)->toDateString(),
                'jumlah_pekerja' => '6',
                'lokasi' => 'Area Parkir Timur',
                'site' => 'Madiun',
                'status' => Contractor::STATUS_WAITING,
                'documents' => [],
                'plan' => [
                    'no' => '103/HSEPLAN/HSE/' . date('Y'),
                    'no_po' => 'PO/MDN/2026/0129',
                    'divisi' => 'Divisi Umum',
                    'lama' => '6 hari kerja',
                    'mode' => 'existing',
                    'documents' => [],
                    'submitted_at' => now()->subDays(12),
                    'reviewed_at' => now()->subDays(11),
                ],
            ],
            [
                'key' => '06',
                'vendor' => 'PT Nusa Kimia Persada',
                'pic' => 'Nur Aisyah',
                'jenis_pekerjaan' => 'ruang_terbatas',
                'telepon' => '081234567806',
                'nama_pekerjaan' => 'Pembersihan dan inspeksi bagian dalam Tangki T-02 menggunakan bahan kimia pembersih.',
                'tanggal_mulai' => now()->addDay()->toDateString(),
                'tanggal_selesai' => now()->addDays(14)->toDateString(),
                'jumlah_pekerja' => '10',
                'lokasi' => 'Tangki T-02',
                'site' => 'Banyuwangi',
                'status' => Contractor::STATUS_WAITING,
                'documents' => ['jsa', 'sertifikat_personil', 'msds', 'limbah_b3', 'prosedur_kerja', 'inspeksi', 'struktur_organisasi'],
                'plan' => [
                    'no' => '104/HSEPLAN/HSE/' . date('Y'),
                    'no_po' => 'PO/BWI/2026/0091',
                    'divisi' => 'Divisi Produksi',
                    'lama' => '14 hari kerja',
                    'mode' => 'generated',
                    'documents' => ['jsa', 'sertifikat_personil', 'msds', 'limbah_b3', 'prosedur_kerja', 'inspeksi', 'struktur_organisasi'],
                    'submitted_at' => now()->subHours(20),
                    'reviewed_at' => now()->subHours(8),
                ],
            ],
            [
                'key' => '07',
                'vendor' => 'CV Ruang Terbatas Teknik',
                'pic' => 'Hendra Wijaya',
                'jenis_pekerjaan' => 'ruang_terbatas',
                'telepon' => '081234567807',
                'nama_pekerjaan' => 'Pembersihan dan perbaikan dinding Bak Penampungan air proses.',
                'tanggal_mulai' => now()->addDays(4)->toDateString(),
                'tanggal_selesai' => now()->addDays(8)->toDateString(),
                'jumlah_pekerja' => '5',
                'lokasi' => 'Bak Penampungan',
                'site' => 'Madiun',
                'status' => Contractor::STATUS_WAITING,
                'documents' => ['jsa', 'sertifikat_personil', 'prosedur_kerja'],
                'plan' => null,
            ],
            [
                'key' => '08',
                'vendor' => 'PT Radiografi Nusantara',
                'pic' => 'Bayu Saputra',
                'jenis_pekerjaan' => 'radiasi',
                'telepon' => '081234567808',
                'nama_pekerjaan' => 'Pengujian radiografi sambungan las pada rangka kereta di Area Uji Jalur Produksi.',
                'tanggal_mulai' => now()->subDays(3)->toDateString(),
                'tanggal_selesai' => now()->subDay()->toDateString(),
                'jumlah_pekerja' => '3',
                'lokasi' => 'Area Uji Jalur Produksi',
                'site' => 'Banyuwangi',
                'status' => Contractor::STATUS_WAITING,
                'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'prosedur_kerja', 'inspeksi'],
                'plan' => [
                    'no' => '105/HSEPLAN/HSE/' . date('Y'),
                    'no_po' => 'PO/BWI/2026/0094',
                    'divisi' => 'Divisi Quality Control',
                    'lama' => '3 hari kerja',
                    'mode' => 'generated',
                    'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'prosedur_kerja', 'inspeksi'],
                    'submitted_at' => now()->subDays(4),
                    'reviewed_at' => now()->subDays(3),
                ],
            ],
        ];
    }
}
