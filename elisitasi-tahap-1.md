# ELISITASI KEBUTUHAN TAHAP 1
## SIMONIKA — Sistem Informasi Monitoring dan Izin Kerja
### PT INKA (Persero) — Madiun & Banyuwangi

| Atribut | Keterangan |
|---|---|
| Versi dokumen | 1.0 |
| Tanggal | November 2026 |
| Tahap elisitasi | Tahap 1 — Identifikasi & pendefinisian kebutuhan awal |
| Acuan utama | `prd.md`, `presentasi-sistem-permit.md`, kode sumber |
| Stack | Laravel 13 · PHP 8.3 · Blade · Tailwind CSS v3 · Alpine.js · MySQL (dev) · SQLite (test) · DomPDF |
| Status | Baseline kebutuhan (siap divalidasi stakeholder) |

---

## 1. Tujuan Elisitasi Tahap 1

Tahap 1 bertujuan menetapkan **baseline kebutuhan** sistem sebelum desain teknis lanjutan, dengan cara:

1. Mengidentifikasi seluruh **pemangku kepentingan** dan kebutuhannya.
2. Menurunkan **kebutuhan fungsional (Functional Requirements / FR)** yang dapat ditelusuri ke fitur/modul sistem.
3. Menetapkan **kebutuhan non-fungsional (Non-Functional Requirements / NFR)** beserta metrik terukur.
4. Mencatat **asumsi, ketergantungan, dan batasan** yang memengaruhi ruang lingkup.
5. Menyusun **prioritas (MoSCoW)** dan **matriks keterlacakan** ke dokumen PRD.

**Ruang lingkup Tahap 1** mencakup modul: pengajuan permit publik, monitoring publik, autentikasi & peran, dashboard Divisi, alur approval Staff/Manager HSE, Daftar Periksa (work inspection), Data Kontraktor, HSE Plan, dashboard Superadmin, dokumen PDF, dan notifikasi email.

**Di luar ruang lingkup Tahap 1:** integrasi ERP/HRIS, aplikasi mobile native, pembacaan dokumen otomatis (OCR), dan mesin analitik lanjutan.

---

## 2. Pemangku Kepentingan (Stakeholder)

| Kode | Stakeholder | Peran dalam sistem | Kebutuhan Utama | Kepentingan/Kekhawatiran |
|---|---|---|---|---|
| STK-01 | Kontraktor / Vendor | Pengaju publik | Ajukan permit, daftar periksa, data kontraktor, HSE Plan tanpa akun | Proses cepat, status jelas, aman dari double-submit |
| STK-02 | Divisi PT INKA | Pemohon berakun | Buat/kelola permit milik sendiri, revisi, batalkan, unduh PDF | Hanya melihat data miliknya (IDOR), kemudahan revisi |
| STK-03 | Staff HSE | Verifikator tingkat 1 | Review permit, buat Daftar Periksa, setujui/kembalikan | Antrean FIFO, scoping site, jejak tanda tangan |
| STK-04 | Manager HSE | Approver final | Setujui/aktifkan permit, setujui Daftar Periksa & HSE Plan, kelola kontraktor | Visibilitas lintas site, keputusan terdokumentasi |
| STK-05 | Superadmin | Administrator | Kelola akun divisi & master divisi, unduh PDF permit | Kontrol akses, audit akun, konsistensi data |
| STK-06 | Publik umum | Pemantau | Monitoring permit aktif & kontraktor | Transparansi tanpa membuka data Draft/rahasia |
| STK-07 | Manajemen K3LH / PT INKA | Pemilik proses bisnis | Kepatuhan K3LH, audit, pelaporan PDF | Regulasi, retensi dokumen, akuntabilitas |
| STK-08 | Tim Pengembang / Operator TI | Pengelola sistem | Deployment, pemeliharaan, pengujian | Kemudahan maintain, konfigurasi, backup |

---

## 3. Metode Elisitasi Tahap 1

| Teknik | Sasaran | Keluaran |
|---|---|---|
| Wawancara pemangku kepentingan | STK-02 s.d. STK-05, STK-07 | Alur kerja, aturan approval, batasan site |
| Analisis dokumen | Form K3LH IV Rev.0, form HSE Plan, PRD | Field wajib, 28 item Daftar Periksa, format nomor dokumen |
| Observasi proses manual | Alur kertas lama | Titik gesek (verifikasi lambat, dokumen hilang) |
| Analisis sistem berjalan | Kode & basis data | Kebutuhan yang sudah terimplementasi vs celah |
| Prototipe / walkthrough | Wizard permit, dashboard | Validasi UX dan urutan langkah |
| Pemodelan use case | Seluruh aktor | Daftar FR & aktor |

**Sumber kebenaran repo:** `prd.md` (kebutuhan), `perbaikanbug.md` (cacat/risiko), `relasi.md` (skema), kode pada `routes/web.php` dan `app/Http/Controllers/*`.

---

## 4. Kebutuhan Fungsional (FR)

Notasi prioritas: **M** = Must have, **S** = Should have, **C** = Could have, **W** = Won't have (Tahap 1).

### 4.1 Modul Publik — Landing & Monitoring (FR-01)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-01-01 | Sistem menampilkan landing page dengan hero, 3 tombol aksi (Ajukan Safety Permit, Cek Status, Ajukan Data Kontraktor) dan tombol Ke Dashboard bila sudah login. | Publik | M |
| FR-01-02 | Sistem menampilkan tabel monitoring permit publik yang mengecualikan status `Draft`, dengan filter site, tipe (Internal/Eksternal), dan pencarian (nomor, nama pekerjaan, kontraktor, lokasi), terpaginasi. | Publik | M |
| FR-01-03 | Sistem menampilkan tabel monitoring Data Kontraktor publik dengan filter site, status, dan pencarian (vendor, PIC, nama pekerjaan, lokasi). | Publik | M |
| FR-01-04 | Sistem menampilkan section alur pengajuan 5 langkah, galeri, dan tentang. | Publik | S |
| FR-01-05 | Pengguna dapat membuka detail permit pada modal read-only tanpa login. | Publik | S |
| FR-01-06 | Sistem menyediakan filter tab lokasi Madiun / Banyuwangi. | Publik | M |

### 4.2 Modul Pengajuan Safety Permit Publik (FR-02)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-02-01 | Sistem menyediakan form wizard 7 langkah tanpa akun; langkah 0 memilih tipe `Internal` / `Eksternal`. | Kontraktor/Divisi | M |
| FR-02-02 | Bila tipe `Eksternal`, sistem mewajibkan minimal 1 dokumen pendukung (pdf, doc, docx, xls, xlsx, jpg, jpeg, png, gif; maks 10 MB/file) dan menyembunyikan langkah ini bila `Internal`. | Kontraktor | M |
| FR-02-03 | Sistem menampilkan input klasifikasi pekerjaan (6 kode) dan informasi dasar: nama pekerjaan, lokasi, kontraktor, PIC, telepon, tanggal mulai/selesai. | Kontraktor/Divisi | M |
| FR-02-04 | Sistem menyediakan input daftar pekerja untuk 9 kategori (Engineer, Operator Alat Berat, Teknisi Listrik, Mekanik, Welder, dan lainnya). | Kontraktor/Divisi | M |
| FR-02-05 | Sistem menyediakan baris dinamis peralatan kerja (nama alat, jumlah, material). | Kontraktor/Divisi | M |
| FR-02-06 | Sistem menyediakan 25 pilihan bahaya pekerjaan + kolom "bahaya lainnya". | Kontraktor/Divisi | M |
| FR-02-07 | Sistem menyediakan 10 pilihan tindakan pencegahan + kolom "pencegahan lainnya". | Kontraktor/Divisi | M |
| FR-02-08 | Sistem menyediakan 11 pilihan APD + kolom "APD lainnya". | Kontraktor/Divisi | M |
| FR-02-09 | Sistem menyediakan tanda tangan digital berbasis canvas (disimpan sebagai data-URL PNG base64) dan wajib saat submit. | Kontraktor/Divisi | M |
| FR-02-10 | Sistem menampilkan halaman review sebelum submit serta tombol Simpan Draft / Submit. | Kontraktor/Divisi | M |
| FR-02-11 | Sistem membuat nomor permit unik `WP-<tahun>-<8 hex>` dan mencegah double-submit melalui `idempotency_key`. | Sistem | M |
| FR-02-12 | Setelah submit, sistem menampilkan halaman sukses ber-URL bertanda tangan dan permit langsung berstatus `Review Staff`. | Kontraktor/Divisi | M |
| FR-02-13 | Sistem mencatat `submitted_at` saat submit dan menyimpan dokumen ke penyimpanan lokal. | Sistem | M |

### 4.3 Modul Cek Status Publik (FR-03)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-03-01 | Pengguna dapat mengecek status permit menggunakan nomor permit + nomor telepon. | Publik | M |
| FR-03-02 | Pengguna dapat mengecek status HSE Plan menggunakan nomor HSE Plan + nomor telepon, termasuk alasan penolakan dan unduhan PDF bila `Approved`. | Publik | M |
| FR-03-03 | Sistem menyediakan halaman cek status terpadu untuk Permit dan HSE Plan. | Publik | M |
| FR-03-04 | Sistem membatasi permintaan cek status (throttle 10 permintaan/menit). | Sistem | M |

### 4.4 Modul Autentikasi & Akun (FR-04)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-04-01 | Sistem menyediakan login menggunakan **username + password** (bukan email). | Semua akun | M |
| FR-04-02 | Sistem menolak login akun nonaktif (`is_active = false`) dengan pesan yang jelas. | Sistem | M |
| FR-04-03 | Sistem membatasi percobaan login 5 kali/menit per username + IP. | Sistem | M |
| FR-04-04 | Sistem mengarahkan pengguna setelah login sesuai peran (superadmin, divisi, staff/manager). | Sistem | M |
| FR-04-05 | Pengguna dapat logout dan mengelola profil (ubah data & password). | Semua akun | S |
| FR-04-06 | Middleware `prevent-back-history` mencegah halaman terautentikasi dibuka ulang via tombol Back. | Sistem | S |
| FR-04-07 | Middleware `role` mengembalikan `403` untuk peran tidak berwenang dan redirect ke `/login` bila belum login. | Sistem | M |

### 4.5 Modul Dashboard Divisi (FR-05)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-05-01 | Sistem menampilkan widget jumlah permit berstatus Draft, dalam proses (Review Staff/Manager/Revision), Active, dan Closed milik sendiri. | Divisi | M |
| FR-05-02 | Sistem menampilkan tabel 5 permit terbaru milik sendiri. | Divisi | M |
| FR-05-03 | Divisi dapat membuat permit (simpan Draft atau submit). | Divisi | M |
| FR-05-04 | Divisi dapat mengedit permit hanya berstatus `Draft` atau `Revision` dan hanya miliknya. | Divisi | M |
| FR-05-05 | Divisi dapat melihat riwayat permit dengan filter status/tipe/pencarian dan paginasi. | Divisi | M |
| FR-05-06 | Divisi dapat melihat detail permit (klasifikasi, informasi, bahaya, pencegahan, APD, dokumen, tanda tangan, catatan revisi). | Divisi | M |
| FR-05-07 | Divisi dapat mengunduh PDF permit miliknya untuk semua status. | Divisi | M |
| FR-05-08 | Divisi dapat mengunduh dokumen permit miliknya dengan validasi kepemilikan. | Divisi | M |
| FR-05-09 | Divisi dapat membatalkan permit berstatus apa pun kecuali `Closed`/`Cancelled`, wajib alasan + tanda tangan, status langsung `Cancelled`. | Divisi | M |
| FR-05-10 | Semua query permit Divisi dibatasi `user_id = Auth::id()` (IDOR guard). | Sistem | M |

### 4.6 Modul Staff HSE (FR-06)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-06-01 | Sistem menampilkan dashboard antrean `Review Staff` (FIFO berdasarkan `submitted_at`) dan ringkasan Daftar Periksa site sendiri. | Staff HSE | M |
| FR-06-02 | Staff dapat melihat antrean approval dengan filter status, pencarian, dan tanggal. | Staff HSE | M |
| FR-06-03 | Staff dapat membuka detail permit A–F beserta dokumen dan tanda tangan sebelumnya. | Staff HSE | M |
| FR-06-04 | Staff dapat menyetujui permit (wajib TTD) sehingga status menjadi `Review Manager`. | Staff HSE | M |
| FR-06-05 | Staff dapat mengembalikan permit ke `Revision` dengan catatan revisi wajib (maks 500 karakter). | Staff HSE | M |
| FR-06-06 | Staff dapat mengoreksi detail permit selama berstatus `Review Staff`, dengan pencatatan `edit_history`. | Staff HSE | S |
| FR-06-07 | Staff dapat melihat riwayat seluruh permit dengan filter global. | Staff HSE | S |
| FR-06-08 | Staff hanya dapat melihat permit/Daftar Periksa sesuai `users.site` (IDOR antar-site). | Sistem | M |
| FR-06-09 | Staff dapat mengunduh PDF/dokumen permit sesuai gate status yang diizinkan. | Staff HSE | M |

### 4.7 Modul Daftar Periksa Izin Pekerjaan Berisiko Tinggi (FR-07)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-07-01 | Staff dapat membuat Daftar Periksa mengikuti form **K3LH IV — Rev.0** dengan site otomatis dari akun. | Staff HSE | M |
| FR-07-02 | Sistem menyediakan 28 item pengecekan dengan pilihan **OK / NOK / N/A**. | Staff HSE | M |
| FR-07-03 | Sistem mencatat header dokumen (No. Form, tanggal terbit, hal, otorisasi) serta isian pekerjaan, peralatan, material, lokasi, tanggal mulai, petugas pemeriksa. | Staff HSE | M |
| FR-07-04 | Sistem membuat nomor `XXX/DPIKBT/INKA/<tahun>`. | Sistem | M |
| FR-07-05 | Staff menandatangani (`staff_signature`); setelah submit status menjadi `Review Manager`. | Staff HSE | M |
| FR-07-06 | Manager dapat menyetujui/menolak Daftar Periksa dengan mengisi nama dan tanda tangan. | Manager HSE | M |
| FR-07-07 | Sistem menampilkan daftar periksa sesuai site untuk Staff, dan lintas site untuk Manager. | Staff/Manager | M |
| FR-07-08 | Sistem menyediakan PDF Daftar Periksa berisi 28 item + tanda tangan Staff dan Manager. | Staff/Manager | M |
| FR-07-09 | Sistem mendukung status `Menunggu Verifikasi` (data lama), `Review Manager`, `Disetujui`, `Ditolak`. | Sistem | S |
| FR-07-10 | Role selain Staff ditolak (403) saat membuat Daftar Periksa; Staff tanpa site ditolak. | Sistem | M |

### 4.8 Modul Data Kontraktor (FR-08)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-08-01 | Publik dapat mendaftarkan data kontraktor tanpa login (vendor, PIC, jenis pekerjaan, telepon, nama pekerjaan, tanggal mulai/selesai, jumlah pekerja, lokasi, site). | Kontraktor | M |
| FR-08-02 | Pendaftaran dapat menyertakan unggahan dokumen pendukung. | Kontraktor | M |
| FR-08-03 | Sistem membuat token unik dan halaman sukses ber-URL bertanda tangan. | Sistem | M |
| FR-08-04 | Status approval kontraktor: `pending` → `approved` / `on_hold` / `inactive`, disinkronkan dari keputusan HSE Plan Manager. | Manager HSE | M |
| FR-08-05 | Kontraktor berstatus approved dapat mengajukan HSE Plan. | Kontraktor | M |
| FR-08-06 | Dokumen kontraktor dapat dilihat melalui URL bertanda tangan dan throttle. | Kontraktor/Publik | S |

### 4.9 Modul HSE Plan (FR-09)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-09-01 | Publik dapat mengajukan HSE Plan dengan data: no PO, site, tipe, divisi pengaju, nama kontraktor, pimpinan proyek, telepon, deskripsi, durasi, tanggal/jam, personil, APD, peralatan, bahan B3, limbah B3, prosedur kerja, rencana inspeksi, tanggap darurat, struktur organisasi. | Kontraktor | M |
| FR-09-02 | Pengajuan menyertakan tanda tangan digital dan unggahan dokumen HSE Plan. | Kontraktor | M |
| FR-09-03 | Sistem membuat nomor `HSE-<tahun>-<sequence>`. | Sistem | M |
| FR-09-04 | Manager dapat menyetujui, menolak (dengan alasan), meminta revisi, menahan (On Hold), dan membuka kembali (Resume) HSE Plan. | Manager HSE | M |
| FR-09-05 | Sistem menampilkan status HSE Plan: `Review Manager`, `Approved`, `Rejected`, `Perlu Revisi`. | Sistem | M |
| FR-09-06 | Staff/Manager dapat melihat detail dan mengunduh PDF HSE Plan sesuai kewenangan site. | Staff/Manager | M |
| FR-09-07 | Manager dapat mengoreksi data HSE Plan sebelum keputusan; aksi memicu validasi ulang dokumen tergenerasi. | Manager HSE | S |

### 4.10 Modul Superadmin (FR-10)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-10-01 | Sistem menampilkan statistik akun divisi (total/aktif/nonaktif) dan 5 akun terbaru. | Superadmin | M |
| FR-10-02 | Superadmin dapat membuat, mengubah, dan menghapus akun divisi (role dipaksa `divisi`). | Superadmin | M |
| FR-10-03 | Superadmin dapat mengaktifkan/menonaktifkan akun dan mereset password. | Superadmin | M |
| FR-10-04 | Sistem menyarankan nonaktif (bukan hapus) bila akun terikat permit. | Sistem | S |
| FR-10-05 | Superadmin dapat mengelola master divisi (CRUD tabel `divisions`). | Superadmin | S |
| FR-10-06 | Superadmin dapat mengunduh PDF permit untuk status `Review Staff`, `Review Manager`, `Revision`, `Active`, `Closed`. | Superadmin | M |
| FR-10-07 | Superadmin tidak memiliki akses approval permit (pemisahan tugas). | Sistem | M |

### 4.11 Modul Dokumen PDF (FR-11)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-11-01 | Sistem menghasilkan PDF Safety Permit A4 portrait via DomPDF. | Sistem | M |
| FR-11-02 | Sistem menghasilkan PDF Daftar Periksa dengan kedua tanda tangan. | Sistem | M |
| FR-11-03 | Sistem menghasilkan PDF HSE Plan. | Sistem | M |
| FR-11-04 | Akses PDF dibatasi per peran/status/kepemilikan. | Sistem | M |

### 4.12 Modul Notifikasi Email (FR-12)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-12-01 | Sistem mengirim email ke Staff HSE saat permit masuk `Review Staff`. | Sistem | S |
| FR-12-02 | Sistem mengirim email ke Staff HSE saat permit dibatalkan Divisi. | Sistem | S |

### 4.13 Modul Audit & Riwayat (FR-13)

| ID | Kebutuhan | Aktor | Prioritas |
|---|---|---|---|
| FR-13-01 | Sistem menyimpan jejak approval (`approval_signatures` JSON: role, nama, tanda tangan, waktu). | Sistem | M |
| FR-13-02 | Sistem menyimpan jejak pembatalan (`cancellation_signatures` JSON, alasan, waktu). | Sistem | M |
| FR-13-03 | Sistem menyimpan riwayat koreksi detail permit oleh Staff (`edit_history`). | Sistem | S |
| FR-13-04 | Sistem menampilkan waktu dibuat, diajukan, ditutup pada detail permit. | Sistem | S |

---

## 5. Kebutuhan Non-Fungsional (NFR)

| ID | Kategori | Pernyataan Kebutuhan | Metrik / Target | Prioritas |
|---|---|---|---|---|
| NFR-01 | Performance | Halaman daftar/dashboard merespons cepat pada beban normal. | Waktu muat ≤ 2 detik (server lokal); paginasi 5–15 baris. | M |
| NFR-02 | Performance | Pembuatan PDF selesai dalam waktu wajar. | ≤ 5 detik per dokumen A4. | S |
| NFR-03 | Performance | Sistem memakai indeks pada kolom pencarian/filter permit. | Indeks pada `status`, `site`, `submitted_at`, `no_permit`. | M |
| NFR-04 | Security | Autentikasi memakai hash password kuat dan sesi Laravel. | Bcrypt/Argon2; CSRF token aktif di semua form. | M |
| NFR-05 | Security | Pencegahan akses data lintas pemilik/ site (IDOR). | 100% query Divisi ter-scope `user_id`; Staff ter-scope `site`. | M |
| NFR-06 | Security | Pembatasan laju akses untuk login dan endpoint publik. | Login 5/menit; cek status/data publik 10/menit; dokumen 30/menit. | M |
| NFR-07 | Security | Perlindungan terhadap XSS, SQL Injection, dan CSRF. | Escaping Blade, query builder/ORM, validasi server-side. | M |
| NFR-08 | Security | Unggahan file dibatasi tipe dan ukuran. | MIME whitelist; maks 10 MB/file; disimpan di disk lokal privat. | M |
| NFR-09 | Security | Akses halaman sukses/unduhan tertentu memakai URL bertanda tangan. | Signature valid; kedaluwarsa sesuai konfigurasi. | S |
| NFR-10 | Security | Akun nonaktif tidak dapat mengakses sistem. | Ditolak saat login; sesi tidak dibuat. | M |
| NFR-11 | Reliability | Pencegahan double-submit pengajuan. | `idempotency_key` unik; submit berulang tidak membuat duplikat. | M |
| NFR-12 | Reliability | Nomor dokumen unik dan aman dari race condition. | Random 8 hex + constraint unik DB. | M |
| NFR-13 | Reliability | Perubahan status approval atomik. | Transaksi DB untuk approve/hold/resume/tolak. | S |
| NFR-14 | Availability | Sistem tersedia pada jam kerja operasional. | ≥ 99% uptime jam kerja; graceful error page. | S |
| NFR-15 | Usability | Antarmuka berbahasa Indonesia, konsisten, dan mudah dipahami. | 100% teks UI Indonesia; alur wizard jelas. | M |
| NFR-16 | Usability | Desain responsif mobile-first. | Berfungsi di 360 px–1440 px; breakpoint sm/md/lg. | M |
| NFR-17 | Usability | Gaya visual minimalis, card-based, banyak whitespace. | Sesuai tema `inka-navy`, Inter, tombol primer besar. | S |
| NFR-18 | Usability | Aksesibilitas dasar. | Kontras teks memadai, label input, navigasi keyboard canvas/modal. | C |
| NFR-19 | Compatibility | Mendukung browser modern. | Chrome, Edge, Firefox, Safari versi terbaru. | M |
| NFR-20 | Compatibility | Berjalan pada MySQL (produksi) dan SQLite (pengujian). | Migrasi & test lulus di kedua lingkungan. | M |
| NFR-21 | Maintainability | Kode mengikuti konvensi Laravel/PSR dan terstruktur per peran. | Controller per namespace peran; model Eloquent; migrasi berversi. | M |
| NFR-22 | Maintainability | Perubahan terverifikasi otomatis. | `composer run test` lulus (PHPUnit, SQLite memory). | S |
| NFR-23 | Scalability | Sistem mendukung dua site dan pertumbuhan data. | Multi-site filter; paginasi; relasi terindeks. | S |
| NFR-24 | Auditability | Setiap keputusan approval dapat ditelusuri. | Tersimpan role, nama, TTD, waktu, catatan revisi. | M |
| NFR-25 | Data Integrity | Integritas relasi dan status terjaga. | FK cascade dokumen; enum status tervalidasi; nilai status konsisten dengan migrasi. | M |
| NFR-26 | Privacy | Data Draft dan data sensitif tidak tampil ke publik. | Monitoring publik mengecualikan `Draft`; akses via URL bertanda tangan. | M |
| NFR-27 | Portability | Mudah dipasang di server VPS. | Skrip `setup_vps.sh`; `npm run build`; `migrate --seed`. | S |
| NFR-28 | Localization | Format tanggal Indonesia dan istilah K3LH konsisten. | Tanggal `d/m/Y`; istilah sesuai form resmi. | S |
| NFR-29 | Observability | Kesalahan sistem tercatat untuk diagnosis. | Log Laravel (pail/log file); pesan error ramah pengguna. | C |
| NFR-30 | Configurability | Konfigurasi site, mail, dan storage dapat diubah tanpa ubah kode. | Variabel `.env`; konstanta `SITES` terpusat. | C |

---

## 6. Asumsi

1. Kontraktor/divisi memiliki akses internet dan browser modern.
2. Tanda tangan digital diterima sebagai bukti sah internal (canvas base64), bukan sertifikat digital tersertifikasi.
3. Data master klasifikasi pekerjaan (6 kode) tersedia melalui seeder/migrasi.
4. Akun Staff HSE selalu memiliki `site` yang valid; akun tanpa site tidak memproses data site.
5. Manager HSE dan Superadmin berwenang lintas site.
6. Email notifikasi menggunakan konfigurasi SMTP yang tersedia di `.env`.
7. Dua lokasi (Madiun, Banyuwangi) memakai instance/aplikasi yang sama dengan filter `site`.
8. Pengguna akun Divisi dibuatkan oleh Superadmin (tanpa registrasi mandiri).

---

## 7. Ketergantungan

| Kode | Ketergantungan | Dampak bila tidak tersedia |
|---|---|---|
| DEP-01 | Server PHP 8.3 + Laravel 13 + Composer | Aplikasi tidak dapat dijalankan |
| DEP-02 | MySQL untuk produksi | Penyimpanan data utama gagal |
| DEP-03 | Node.js + Vite untuk aset frontend | UI tanpa CSS/JS yang benar |
| DEP-04 | DomPDF (`barryvdh/laravel-dompdf`) | PDF tidak dapat dibuat |
| DEP-05 | Layanan SMTP | Notifikasi email tidak terkirim |
| DEP-06 | Penyimpanan disk lokal (dokumen) | Unggahan/unduhan dokumen gagal |
| DEP-07 | Form K3LH IV Rev.0 sebagai acuan | Isi Daftar Periksa tidak sesuai regulasi |

---

## 8. Batasan (Constraints)

1. **Teknologi tetap**: Laravel 13, PHP 8.3, Blade, Tailwind v3, Alpine.js, MySQL/SQLite, DomPDF.
2. **Bahasa UI wajib Indonesia** untuk seluruh antarmuka.
3. **Login memakai username**, bukan email.
4. **Role tetap**: `superadmin`, `divisi`, `staff`, `manager`.
5. **Alur approval tetap**: Draft → Review Staff → Review Manager → Active → Closed.
6. **`Submitted` adalah status orphan** — tidak boleh dipakai untuk logika baru.
7. **Tanda tangan bukan file upload** — berupa data-URL PNG base64.
8. **Tidak ada unggahan dokumen nyata untuk permit pada tahap ini** (form belum `enctype="multipart/form-data"`); dokumen permit dikelola sebagai lampiran terpisah bila ada.
9. **Mayoritas route tanpa nama**; view memakai URL hardcoded — refactor ke named route memerlukan rencana tersendiri.
10. **Kolom `active_at` tidak ada** di migrasi meski dirujuk kode (bug terdokumentasi) — jangan diandalkan.
11. **`closed_at` belum diisi otomatis** oleh kode manapun.
12. **Tabel `divisions` orphan** — tidak FK ke user/permit; "divisi" pemohon adalah role.
13. **Pengujian memakai SQLite** sementara produksi MySQL — validasi enum/status wajib manual.

---

## 9. Prioritas MoSCoW — Ringkasan

| Prioritas | Jumlah FR | Contoh Cakupan |
|---|---|---|
| **Must have (M)** | Lihat tabel per modul | Pengajuan permit, approval berlapis, Daftar Periksa, HSE Plan, autentikasi, IDOR guard, PDF inti, audit |
| **Should have (S)** | Lihat tabel per modul | Notifikasi email, edit detail oleh Staff, edit history, master divisi, filter lanjutan |
| **Could have (C)** | — | Aksesibilitas lanjutan, observability/config tambahan |
| **Won't have (W) — Tahap 1** | — | Integrasi ERP/HRIS, mobile native, OCR, analitik lanjutan |

---

## 10. Matriks Keterlacakan (FR → PRD → Implementasi)

| Modul | FR | PRD | Implementasi utama |
|---|---|---|---|
| Landing & Monitoring | FR-01 | §4.1 | `routes/web.php:7`, `welcome.blade.php` |
| Pengajuan Permit Publik | FR-02 | §4.2 | `Public\PermitController`, `divisi/permits/create.blade.php` |
| Cek Status Publik | FR-03 | §4.4, §4.6 | `Public\PermitStatusController`, `Public\StatusCheckController`, `Public\HsePlanController` |
| Autentikasi & Akun | FR-04 | §5 | `Auth\AuthenticatedSessionController`, `LoginRequest`, `EnsureRole`, `PreventBackHistory` |
| Dashboard Divisi | FR-05 | §4.7 | `Divisi\DashboardController`, `PermitController`, `PermitShowController`, `HistoryController`, `CancellationController` |
| Staff HSE | FR-06 | §4.8 | `Admin\DashboardController`, `ApprovalController`, `HistoryController` |
| Daftar Periksa | FR-07 | §4.3 | `Admin\WorkInspectionApprovalController`, model `WorkInspection` |
| Data Kontraktor | FR-08 | §4.4 | `Public\ContractorController`, model `Contractor` |
| HSE Plan | FR-09 | §4.5 | `Public\HsePlanController`, `Admin\HsePlanApprovalController`, model `HsePlan` |
| Superadmin | FR-10 | §4.9 | `SuperAdmin\DashboardController`, `UserController`, `DivisionController`, `PermitController` |
| PDF | FR-11 | §9 | `divisi/permits/pdf.blade.php`, `HsePlanPdfService`, DomPDF |
| Notifikasi Email | FR-12 | §10 | `PermitMasukMail`, `PermitDibatalkanMail`, `PermitMailNotifier` |
| Audit & Riwayat | FR-13 | §4.2, §12 | `approval_signatures`, `cancellation_signatures`, `edit_history` |

---

## 11. Kriteria Penerimaan Elisitasi Tahap 1

Tahap 1 dinyatakan selesai dan siap masuk desain bila:

1. Seluruh stakeholder (STK-01 s.d. STK-08) menyetujui daftar kebutuhan.
2. Tidak ada FR berprioritas **Must** yang belum memiliki aktor dan modul jelas.
3. Setiap NFR memiliki metrik/target yang dapat diuji.
4. Asumsi, ketergantungan, dan batasan telah dikonfirmasi pemilik proses.
5. Matriks keterlacakan FR → PRD → implementasi lengkap.
6. Daftar bug terdokumentasi (`perbaikanbug.md`) ditinjau; tidak ada kebutuhan baru yang bertentangan dengan batasan.

---

## 12. Risiko Kebutuhan (Ringkas)

| Kode | Risiko | Dampak | Mitigasi |
|---|---|---|---|
| R-01 | Status `Submitted` orphan membingungkan filter riwayat | Laporan tidak akurat | Dokumentasikan; jangan tambah logika |
| R-02 | Perbedaan enum MySQL vs SQLite saat uji | Bug status lolos pengujian | Verifikasi manual ke migrasi |
| R-03 | IDOR bila scoping terlewat | Kebocoran data antar-site/pemilik | Review query; test kepemilikan |
| R-04 | Double-submit pengajuan | Data ganda | `idempotency_key` + constraint unik |
| R-05 | Tanda tangan base64 membengkak | Performa DB/PDF | Batasi ukuran; kompresi canvas |
| R-06 | Dokumen tanpa enkripsi disk | Kebocoran lampiran | Akses bertanda tangan + validasi kepemilikan |
| R-07 | Notifikasi email gagal kirim | Staff tidak tahu antrean baru | Log + monitoring; dashboard tetap sumber utama |
| R-08 | `closed_at` tidak terisi otomatis | Status Closed tidak konsisten | Tetapkan aturan penutupan pada tahap desain |
