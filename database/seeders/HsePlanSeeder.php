<?php

namespace Database\Seeders;

use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HsePlanSeeder extends Seeder
{
    private const SIGNATURE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPAAAABGCAYAAADyxhn6AAAACXBIWXMAAA7EAAAOxAGVKw4bAAACkUlEQVR4nO3d3XHjIBQGUHtnu0gf6SbVuRv34TqyT5nNJDjRzwUucM5rJgJx+UYIyfblAgAAAAAAAAAAAAAAAAAAAIS79u4AtPTy+vb+098f99tQmRiqs7DXb4F95nG/XV9e396zBzp15+Coo8EtyRzitB2DI84ukZ/9f9YQp+wU7PFTaI8G7+sxsy6pU3UG9qp9xSwdP1OI03QE9moVrswhTtEJ2Ku0xG3ZXos2t+jega27hRkGi35q3OdG9KP3vOzW+Jnnc9F9Ia+ML1587lPv+di08VWezXHcKCuyLCHutvT4sPXkR3s+R9lMK68MIW7SaOQGQMYlFd9FrbYy1zPD/XD1BmudpCDnskJgS3qHuOnWe6tndCWjTYxR7A3ujHXoGeJqDfU6qciNspIZJ+BeW8Z4tXHqNd+rNJLh5r52kD98Pr+M78pGs5n4XI95X31Jm6WwrQJ9ueQ552hZ30bKpHWIQxvIGt5Iq95zr1DbKC1DHHbwVQu8wpJy1doe1XK8qjzSWbnAo3yOdCu1PabVuIUHWIHnuSoL7zktxu/0AYW3bPQQC2+M2uN46mDC+7sRd26FN1bN8fwTdSDKHvfb9WvBWj7S2kt449Ws/+EAu/ruM0KIhbee1JtYbJc1JFn7NZvocVakDrKFJVt/Zhe5L+IeuINMy2nhbS9yjBWro97h6d0+/2twdOwVrLMsH7sU3jEpWgK9v+NYeMelcEm0CpXwzsUmVhKlIEVvbgnvfBQwmVqvXgrvnBQxodpfwyu881DIpM5eMUf/NFQLLZ6/D/ONHNSxN8itgpvxXe6MBHhSArAGAS4w+Ykww+1Et19KYGwzTP4Z/O3dgVUJABGGDLDJDwAAAAAAAAAAAMT4B6uM2+TEJl8vAAAAAElFTkSuQmCC';

    public function run(): void
    {
        $manager = User::where('role', 'manager')->first();

        if (! $manager) {
            $this->command->warn('Manager HSE tidak ditemukan. Jalankan UserSeeder terlebih dahulu.');

            return;
        }

        foreach ($this->plans($manager) as $data) {
            $documents = $data['documents'];
            unset($data['documents']);

            $hsePlan = HsePlan::updateOrCreate(
                ['no_hse_plan' => $data['no_hse_plan']],
                $data
            );

            $this->seedDocuments($hsePlan, $documents);
        }

        $this->command->info('HsePlanSeeder: 5 contoh HSE Plan berhasil dibuat.');
    }

    private function plans(User $manager): array
    {
        return [
            [
                'no_hse_plan' => '001/HSEPLAN/HSE/' . date('Y'),
                'no_po' => 'PO/MDN/2026/0142',
                'site' => 'Madiun',
                'tipe' => 'Internal',
                'divisi_pengaju' => 'Divisi Teknik',
                'nama_kontraktor' => 'PT Karya Presisi Nusantara',
                'nama_pimpinan_proyek' => 'Andi Prasetyo',
                'telepon' => '081234567801',
                'deskripsi_pekerjaan' => 'Perbaikan atap dan talang Gudang B, meliputi bongkar genteng rusak, penggantian rangka ringan, pemasangan penutup atap, serta pembersihan area kerja.',
                'lama_pekerjaan' => '5 hari kerja',
                'tanggal_mulai' => Carbon::now()->addDay(),
                'tanggal_selesai' => Carbon::now()->addDays(5),
                'jam_mulai' => '08:00',
                'jam_selesai' => '17:00',
                'personil' => [
                    ['nama' => 'Andi Prasetyo', 'tugas' => 'Pimpinan Proyek'],
                    ['nama' => 'Rudi Hartono', 'tugas' => 'Teknisi Ketinggian'],
                    ['nama' => 'Slamet Riyadi', 'tugas' => 'Helper'],
                ],
                'apd' => ['helm', 'sepatu', 'rompi', 'sarung_tangan', 'safety_harness'],
                'apd_lainnya' => null,
                'peralatan' => [
                    ['nama' => 'Scaffolding', 'jumlah' => '4 set'],
                    ['nama' => 'Gerinda tangan', 'jumlah' => '2 unit'],
                    ['nama' => 'Tangga aluminium', 'jumlah' => '2 unit'],
                ],
                'bahan_b3' => false,
                'limbah_b3' => false,
                'prosedur_kerja' => true,
                'rencana_inspeksi' => true,
                'tanggap_darurat' => true,
                'struktur_organisasi' => true,
                'tanda_tangan' => self::SIGNATURE,
                'status' => HsePlan::STATUS_REVIEW,
                'submitted_at' => Carbon::now()->subHours(2),
                'reviewed_by' => null,
                'reviewed_at' => null,
                'rejection_reason' => null,
                'manager_signature' => null,
                'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'prosedur_kerja', 'inspeksi', 'struktur_organisasi'],
            ],
            [
                'no_hse_plan' => '002/HSEPLAN/HSE/' . date('Y'),
                'no_po' => 'PO/BWI/2026/0087',
                'site' => 'Banyuwangi',
                'tipe' => 'Eksternal',
                'divisi_pengaju' => 'Divisi Logistik',
                'nama_kontraktor' => 'PT Bahari Selatan Logistik',
                'nama_pimpinan_proyek' => 'Nur Aisyah',
                'telepon' => '081345678902',
                'deskripsi_pekerjaan' => 'Bongkar muat kontainer dan penataan material di Gudang Logistik, termasuk pengoperasian forklift dan pemeriksaan area kerja.',
                'lama_pekerjaan' => '3 hari kerja',
                'tanggal_mulai' => Carbon::now()->addDays(2),
                'tanggal_selesai' => Carbon::now()->addDays(4),
                'jam_mulai' => '07:00',
                'jam_selesai' => '16:00',
                'personil' => [
                    ['nama' => 'Nur Aisyah', 'tugas' => 'Supervisor'],
                    ['nama' => 'Bayu Saputra', 'tugas' => 'Operator Forklift'],
                    ['nama' => 'Hendra Wijaya', 'tugas' => 'Helper'],
                    ['nama' => 'Siti Aminah', 'tugas' => 'Checker'],
                ],
                'apd' => ['helm', 'sepatu', 'rompi', 'sarung_tangan', 'ear_plug'],
                'apd_lainnya' => null,
                'peralatan' => [
                    ['nama' => 'Forklift 3 ton', 'jumlah' => '1 unit'],
                    ['nama' => 'Hand pallet', 'jumlah' => '2 unit'],
                    ['nama' => 'Trolley', 'jumlah' => '2 unit'],
                ],
                'bahan_b3' => false,
                'limbah_b3' => false,
                'prosedur_kerja' => true,
                'rencana_inspeksi' => true,
                'tanggap_darurat' => true,
                'struktur_organisasi' => false,
                'tanda_tangan' => self::SIGNATURE,
                'status' => HsePlan::STATUS_REVIEW,
                'submitted_at' => Carbon::now()->subHours(6),
                'reviewed_by' => null,
                'reviewed_at' => null,
                'rejection_reason' => null,
                'manager_signature' => null,
                'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'prosedur_kerja', 'inspeksi'],
            ],
            [
                'no_hse_plan' => '003/HSEPLAN/HSE/' . date('Y'),
                'no_po' => 'PO/MDN/2026/0158',
                'site' => 'Madiun',
                'tipe' => 'Eksternal',
                'divisi_pengaju' => 'Divisi Produksi',
                'nama_kontraktor' => 'PT Prima Coating Indonesia',
                'nama_pimpinan_proyek' => 'Dedi Kurniawan',
                'telepon' => '081456789003',
                'deskripsi_pekerjaan' => 'Pengecatan rangka kereta di area finishing, meliputi persiapan permukaan, masking, penyemprotan cat, pengeringan, dan pemeriksaan hasil akhir.',
                'lama_pekerjaan' => '10 hari kerja',
                'tanggal_mulai' => Carbon::now()->subDays(2),
                'tanggal_selesai' => Carbon::now()->addDays(8),
                'jam_mulai' => '08:00',
                'jam_selesai' => '16:00',
                'personil' => [
                    ['nama' => 'Dedi Kurniawan', 'tugas' => 'Supervisor Pengecatan'],
                    ['nama' => 'Ahmad Fauzi', 'tugas' => 'Painter'],
                    ['nama' => 'Yusuf Maulana', 'tugas' => 'Helper'],
                    ['nama' => 'Rina Wulandari', 'tugas' => 'Quality Control'],
                ],
                'apd' => ['helm', 'sepatu', 'kacamata', 'masker', 'sarung_tangan', 'baju_pelindung'],
                'apd_lainnya' => 'Respirator cartridge',
                'peralatan' => [
                    ['nama' => 'Air compressor', 'jumlah' => '2 unit'],
                    ['nama' => 'Spray gun', 'jumlah' => '3 unit'],
                    ['nama' => 'Exhaust fan', 'jumlah' => '2 unit'],
                ],
                'bahan_b3' => true,
                'limbah_b3' => true,
                'prosedur_kerja' => true,
                'rencana_inspeksi' => true,
                'tanggap_darurat' => true,
                'struktur_organisasi' => true,
                'tanda_tangan' => self::SIGNATURE,
                'status' => HsePlan::STATUS_APPROVED,
                'submitted_at' => Carbon::now()->subDays(5),
                'reviewed_by' => $manager->id,
                'reviewed_at' => Carbon::now()->subDay(),
                'rejection_reason' => null,
                'manager_signature' => null,
                'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'msds', 'limbah_b3', 'prosedur_kerja', 'inspeksi', 'struktur_organisasi'],
            ],
            [
                'no_hse_plan' => '004/HSEPLAN/HSE/' . date('Y'),
                'no_po' => null,
                'site' => 'Banyuwangi',
                'tipe' => 'Internal',
                'divisi_pengaju' => 'Divisi Pemeliharaan',
                'nama_kontraktor' => 'CV Sinar Mandiri Teknik',
                'nama_pimpinan_proyek' => 'Agus Setiawan',
                'telepon' => '081567890104',
                'deskripsi_pekerjaan' => 'Perbaikan instalasi listrik panel penerangan area produksi, termasuk pemeriksaan kabel, penggantian MCB, dan pengujian tegangan.',
                'lama_pekerjaan' => '2 hari kerja',
                'tanggal_mulai' => Carbon::now()->addDays(2),
                'tanggal_selesai' => Carbon::now()->addDays(3),
                'jam_mulai' => '08:00',
                'jam_selesai' => '15:00',
                'personil' => [
                    ['nama' => 'Agus Setiawan', 'tugas' => 'Teknisi Listrik'],
                    ['nama' => 'Bagus Prakoso', 'tugas' => 'Helper'],
                ],
                'apd' => ['helm', 'sepatu', 'kacamata', 'sarung_tangan', 'baju_pelindung'],
                'apd_lainnya' => null,
                'peralatan' => [
                    ['nama' => 'Multimeter', 'jumlah' => '1 unit'],
                    ['nama' => 'Tangga fiber', 'jumlah' => '1 unit'],
                    ['nama' => 'Set kunci insulated', 'jumlah' => '1 set'],
                ],
                'bahan_b3' => false,
                'limbah_b3' => false,
                'prosedur_kerja' => false,
                'rencana_inspeksi' => true,
                'tanggap_darurat' => true,
                'struktur_organisasi' => false,
                'tanda_tangan' => self::SIGNATURE,
                'status' => HsePlan::STATUS_REJECTED,
                'submitted_at' => Carbon::now()->subDay(),
                'reviewed_by' => $manager->id,
                'reviewed_at' => Carbon::now()->subHours(4),
                'rejection_reason' => 'JSA belum mencantumkan prosedur LOTO dan batas area kerja. Mohon lengkapi sebelum diajukan kembali.',
                'manager_signature' => null,
                'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'inspeksi'],
            ],
            [
                'no_hse_plan' => '005/HSEPLAN/HSE/' . date('Y'),
                'no_po' => 'PO/MDN/2026/0171',
                'site' => 'Madiun',
                'tipe' => 'Eksternal',
                'divisi_pengaju' => 'Divisi Pengadaan',
                'nama_kontraktor' => 'PT Nusantara Fabrikasi Utama',
                'nama_pimpinan_proyek' => 'Hendra Gunawan',
                'telepon' => '081678901005',
                'deskripsi_pekerjaan' => 'Fabrikasi dan erection tangki air 20 m3, meliputi pemotongan pelat, pengelasan, pemasangan komponen, dan uji hidrostatik.',
                'lama_pekerjaan' => '14 hari kerja',
                'tanggal_mulai' => Carbon::now()->addDays(4),
                'tanggal_selesai' => Carbon::now()->addDays(18),
                'jam_mulai' => '08:00',
                'jam_selesai' => '17:00',
                'personil' => [
                    ['nama' => 'Hendra Gunawan', 'tugas' => 'Project Manager'],
                    ['nama' => 'Joko Susilo', 'tugas' => 'Welder'],
                    ['nama' => 'Agung Nugroho', 'tugas' => 'Fitter'],
                    ['nama' => 'Tri Handoko', 'tugas' => 'Rigger'],
                    ['nama' => 'Bayu Firmansyah', 'tugas' => 'Safety Officer'],
                ],
                'apd' => ['helm', 'sepatu', 'pelindung_muka', 'kacamata', 'rompi', 'masker', 'sarung_tangan', 'ear_plug', 'baju_pelindung', 'safety_harness'],
                'apd_lainnya' => 'Apron las',
                'peralatan' => [
                    ['nama' => 'Mesin las', 'jumlah' => '3 unit'],
                    ['nama' => 'Gerinda', 'jumlah' => '4 unit'],
                    ['nama' => 'Crane', 'jumlah' => '1 unit'],
                    ['nama' => 'Air compressor', 'jumlah' => '1 unit'],
                    ['nama' => 'Scaffolding', 'jumlah' => '6 set'],
                ],
                'bahan_b3' => true,
                'limbah_b3' => true,
                'prosedur_kerja' => true,
                'rencana_inspeksi' => true,
                'tanggap_darurat' => true,
                'struktur_organisasi' => true,
                'tanda_tangan' => self::SIGNATURE,
                'status' => HsePlan::STATUS_APPROVED,
                'submitted_at' => Carbon::now()->subDays(6),
                'reviewed_by' => $manager->id,
                'reviewed_at' => Carbon::now()->subDays(2),
                'rejection_reason' => null,
                'manager_signature' => null,
                'documents' => ['jsa', 'sertifikat_personil', 'sertifikat_peralatan', 'msds', 'limbah_b3', 'prosedur_kerja', 'inspeksi', 'struktur_organisasi'],
            ],
        ];
    }

    private function seedDocuments(HsePlan $hsePlan, array $types): void
    {
        $labels = [
            'jsa' => 'Job Safety Analysis (JSA)',
            'sertifikat_personil' => 'Sertifikat Keahlian Personil',
            'sertifikat_peralatan' => 'Sertifikat Peralatan',
            'msds' => 'MSDS Bahan B3',
            'limbah_b3' => 'Rencana Pengelolaan Limbah B3',
            'prosedur_kerja' => 'Prosedur / Instruksi Kerja',
            'inspeksi' => 'Rencana Pemantauan / Inspeksi',
            'struktur_organisasi' => 'Struktur Organisasi Proyek',
        ];

        foreach ($types as $jenis) {
            $label = $labels[$jenis] ?? $jenis;
            $path = 'hse-plans/' . $hsePlan->id . '/contoh-' . $jenis . '.pdf';

            if (! Storage::disk('local')->exists($path)) {
                $pdf = Pdf::loadHTML(
                    '<h1 style="font-family: sans-serif; font-size: 18px;">' . e($label) . '</h1>' .
                    '<p style="font-family: sans-serif; font-size: 12px;">Dokumen contoh untuk ' . e($hsePlan->no_hse_plan) . '</p>' .
                    '<p style="font-family: sans-serif; font-size: 12px;">Kontraktor: ' . e($hsePlan->nama_kontraktor) . '</p>'
                )->output();

                Storage::disk('local')->put($path, $pdf);
            }

            HsePlanDocument::updateOrCreate(
                ['hse_plan_id' => $hsePlan->id, 'jenis' => $jenis],
                [
                    'nama_dokumen' => $label,
                    'file_path' => $path,
                    'file_type' => 'pdf',
                    'file_size' => Storage::disk('local')->size($path),
                ]
            );
        }
    }
}
