# PRD — SIMONIKA: Sistem Informasi Monitoring dan Izin Kerja
## PT INKA (Persero) — Madiun & Banyuwangi

**Versi:** 1.0  
**Tanggal:** Oktober 2026  
**Stack:** Laravel 13 · PHP 8.3 · Blade · Tailwind CSS v3 · Alpine.js · MySQL (dev) · SQLite (test) · DomPDF

---

## 1. Latar Belakang

PT INKA (Persero) memiliki kebutuhan untuk mendigitalisasi proses pengajuan dan verifikasi berlapis izin kerja berisiko tinggi bagi kontraktor yang bekerja di lingkungan pabrik. Sebelumnya proses ini dilakukan secara manual (kertas), yang menyebabkan keterlambatan verifikasi, risiko dokumen hilang, dan sulitnya pemantauan status secara real-time.

SIMONIKA hadir sebagai solusi digital end-to-end: mulai dari pengajuan publik tanpa akun, verifikasi bertingkat oleh HSE, hingga penerbitan izin kerja resmi dalam format PDF.

---

## 2. Tujuan Sistem

- Digitalisasi pengajuan izin kerja (Safety Permit) oleh divisi/kontraktor
- Menyediakan alur persetujuan berlapis (Staff HSE → Manager HSE) yang terdokumentasi
- Memantau seluruh permit aktif secara real-time dari halaman publik
- Mendukung dua lokasi operasi: **Madiun** dan **Banyuwangi**
- Menyediakan alur pendaftaran kontraktor (Data Kontraktor + HSE Plan) sebelum mengajukan permit

---

## 3. Pengguna & Peran

| Role | Akses | Deskripsi |
|---|---|---|
| **Publik** | Tanpa login | Monitoring permit, ajukan permit/daftar periksa/HSE Plan/data kontraktor, cek status |
| **Divisi** | Login required | Buat & kelola permit milik sendiri, batalkan, unduh PDF |
| **Staff HSE** | Login required | Review & approve/revisi permit dan daftar periksa, site-scoped |
| **Manager HSE** | Login required | Approve/revisi akhir permit, approve/tolak HSE Plan, semua site |
| **Superadmin** | Login required | Kelola akun divisi + master divisi, unduh PDF permit |

> Staff HSE dibatasi hanya melihat data sesuai `site` yang ditetapkan di akunnya (IDOR guard antar-site).

---

## 4. Modul & Fitur

### 4.1 Landing Page Publik (`/`)

- Hero section dengan 3 tombol aksi seragam: **Ajukan Safety Permit**, **Cek Status**, **Ajukan Data Kontraktor** (+ **Ke Dashboard** bila sudah login)
- Tabel monitoring permit publik (semua status kecuali `Draft`) dengan filter site, tipe, dan pencarian
- Tabel monitoring kontraktor publik
- Section alur pengajuan (5 langkah)
- Section galeri & tentang
- Multi-site: filter tab Madiun / Banyuwangi

### 4.2 Safety Permit

#### Pengajuan Publik (`/ajukan-permit`)
- Form wizard 7 langkah tanpa perlu akun:
  - Step 0: Pilih tipe (Internal / Eksternal)
  - Step 1: Upload dokumen pendukung (wajib bila Eksternal, maks 10 MB/file, format: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, gif)
  - Step 2: Klasifikasi pekerjaan + info dasar (nama, lokasi, kontraktor, PIC, telepon, tanggal)
  - Step 2b: Daftar pekerja (9 kategori: Engineer, Operator Alat Berat, Teknisi Listrik, Mekanik, Welder, dll.)
  - Step 2c: Peralatan kerja (baris dinamis: alat, jumlah, material)
  - Step 3: Bahaya (25 checkbox + bahaya lainnya) + Tindakan Pencegahan (10 checkbox + lainnya)
  - Step 4: APD (11 checkbox + lainnya)
  - Step 5: Tanda tangan digital (canvas base64 PNG)
  - Step 6: Review & submit
- Nomor permit: `WP-<tahun>-<8 hex chars>` (anti race-condition)
- `idempotency_key` mencegah double-submit
- Setelah submit → halaman sukses, permit langsung masuk `Review Staff` (Daftar Periksa dikelola terpisah oleh Staff HSE)

#### Status Permit
| Status | Keterangan |
|---|---|
| `Draft` | Disimpan, belum diajukan (hanya via akun Divisi) |
| `Submitted` | Status lama, tidak lagi dipakai alur submit (orphan) |
| `Review Staff` | Antrean Staff HSE |
| `Review Manager` | Antrean Manager HSE |
| `Revision` | Dikembalikan + catatan revisi wajib |
| `Active` | Disetujui Manager (terminal sukses) |
| `Closed` | Ditutup (kolom `closed_at`, belum ada kode otomatis) |
| `Cancelled` | Dibatalkan oleh Divisi |

#### Alur Approval
```
Pengajuan Publik → Review Staff
Divisi (akun)   → Draft → (submit) → Review Staff
Review Staff    → (staff approve + TTD) → Review Manager
Review Manager  → (manager approve + TTD) → Active
Review Staff/Manager → (revise + catatan) → Revision → (divisi edit+submit) → Review Staff
```

#### Pembatalan
- Divisi dapat membatalkan permit berstatus apapun kecuali `Closed` / `Cancelled`
- Wajib isi alasan + tanda tangan
- Status langsung `Cancelled` tanpa approval

### 4.3 Daftar Periksa Izin Pekerjaan Berisiko Tinggi

Fitur ini **terpisah dari Safety Permit** dan **hanya dapat diakses Staff HSE** site Madiun / Banyuwangi melalui dashboard (`role:staff`). Tidak ada akses publik.

#### Form Dashboard (`/admin/work-inspections/create`)
- Staff membuka form di dashboard, site otomatis mengikuti `users.site` (tidak dapat diubah dari form).
- Isian:
  - Data pekerjaan (divisi, tipe, nama pekerjaan, kontraktor, peralatan, lokasi, telepon, tanggal mulai)
  - 19 pertanyaan checklist YA/TIDAK (Bagian A: 10 soal + Bagian B: 9 soal)
  - LOTO (Lock Out & Tag Out): nama pelaksana, tanggal, jam
  - Supervisor (opsional)
- Tanda tangan diambil pada **satu perangkat di site**: pelaksana/user menandatangani (`tanda_tangan`), lalu staff menandatangani sebagai Safety Officer (`staff_signature`).
- Nomor: `XXX/DPIKBT/INKA/<tahun>`
- Setelah submit, daftar periksa langsung final berstatus `Disetujui` (kedua tanda tangan wajib terisi).

#### Akses & Status
- Staff hanya melihat daftar periksa site-nya (`visibleTo`), termasuk PDF.
- Manager / staff site lain / staff tanpa site ditolak (403).
- Status: `Menunggu Verifikasi` (warisan data lama), `Disetujui`, `Ditolak`.
- Route approve/reject hanya dipertahankan untuk memproses data lama yang masih `Menunggu Verifikasi`.
- PDF (`/admin/work-inspections/{id}/pdf`) berisi data pekerjaan, checklist, LOTO, tanda tangan pelaksana & Safety Officer.

### 4.4 Data Kontraktor (`/data-kontraktor`)

- Form pendaftaran kontraktor publik tanpa login
- Data: vendor, PIC, jenis pekerjaan, telepon, nama pekerjaan, tanggal mulai/selesai, jumlah pekerja, lokasi, site
- Upload dokumen pendukung
- Status approval oleh Manager HSE: `pending` → `approved` / `on_hold` / `inactive`
- Setelah disetujui, kontraktor dapat mengajukan HSE Plan

### 4.5 HSE Plan (`/ajukan-hse-plan`)

- Form publik untuk kontraktor yang sudah terdaftar
- Data: no PO, site, tipe, divisi pengaju, nama kontraktor, nama pimpinan proyek, telepon, deskripsi pekerjaan, durasi, tanggal, personil, APD, peralatan, bahan B3, limbah B3, prosedur kerja, rencana inspeksi, tanggap darurat, struktur organisasi
- Tanda tangan digital
- Upload dokumen HSE Plan
- Nomor: `HSE-<tahun>-<sequence>`

#### Status HSE Plan
| Status | Keterangan |
|---|---|
| `Review Manager` | Menunggu persetujuan Manager HSE |
| `Approved` | Disetujui Manager |
| `Rejected` | Ditolak Manager |
| `On Hold` | Ditahan sementara |

#### Cek Status (`/cek-hse-plan`)
- Cari dengan nomor HSE Plan + nomor telepon
- Tampilkan detail + status + alasan penolakan (bila Rejected)
- Unduh PDF bila Approved

### 4.6 Cek Status Terpadu (`/cek-status`)

- Satu halaman untuk mengecek status dokumen publik: Permit dan HSE Plan (Daftar Periksa tidak lagi publik)
- Input: nomor dokumen + nomor telepon

### 4.7 Dashboard Divisi

- Widget: Draft, Review Staff+Manager+Revision (dalam proses), Active, Closed
- Tabel 5 permit terbaru milik sendiri
- Semua query di-scope ke `user_id = Auth::id()` (IDOR guard)

### 4.8 Dashboard Admin (Staff & Manager HSE)

- Widget antrean sesuai role:
  - Staff: antrean `Review Staff` + total daftar periksa site-nya (dibuat via dashboard)
  - Manager: antrean `Review Manager` + HSE Plan `Review Manager`
- Tabel permit FIFO (urut `submitted_at ASC`)
- Permit detail: grid A (Klasifikasi) · B (Informasi) · C (Bahaya) · D (Pencegahan) · E (APD) · F (Dokumen)
- Form approval: textarea catatan revisi + canvas tanda tangan + tombol Setujui / Kembalikan
- Approval Staff → wajib cek ada daftar periksa `Disetujui` yang terhubung

### 4.9 Dashboard Superadmin

- Widget: total divisi / aktif / nonaktif
- Tabel 5 akun terbaru
- Kelola akun divisi: create, edit, toggle aktif/nonaktif, reset password, delete
- Kelola master divisi (orphan table, tidak FK ke permit/user)
- Akses unduh PDF permit (gate: status `Review Staff`, `Review Manager`, `Revision`, `Active`, `Closed`)

---

## 5. Autentikasi & Keamanan

- Login pakai **username + password** (bukan email), via Laravel Breeze yang dikustomisasi
- User nonaktif (`is_active = false`) diblokir saat login
- Rate limit login: 5 kali/menit per username+IP
- Middleware `prevent-back-history`: header `Cache-Control: no-store` agar tombol Back browser tidak membuka halaman auth lama
- Middleware `role`: redirect ke `/login` bila belum login, `403` bila role salah
- Semua route akun dibungkus `auth + prevent-back-history + role:...`
- IDOR guard: divisi hanya bisa akses permit milik sendiri (`where user_id = Auth::id()`)
- Staff HSE dibatasi ke site sesuai kolom `users.site`
- Signed URL untuk halaman sukses, unduh PDF publik, dan aksi batalkan permit
- `idempotency_key` pada form pengajuan untuk mencegah double-submit

---

## 6. Desain & UI/UX

- **Gaya**: minimal, Apple-like, card-based, banyak whitespace, tombol primer besar
- **Font**: Inter (Google Fonts)
- **Warna tema** (Tailwind custom):
  - `inka-navy` — `#111d33` (navy gelap, warna utama)
  - `inka-light-gray` — `#f4f6f9`
  - `inka-text-muted` — `#6c757d`
  - `inka-border` — `#dee2e6`
  - `accent-orange` / `amber-500` — `#f2994a`
- **Komponen reusable**: `permit-tipe-badge`, `permit-klasifikasi-badges`, `permit-status-badge`, `permit-tanggal`
- **Badge warna status**: Draft (abu) · Review Staff/Manager (oranye) · Revision (merah) · Active (hijau) · Closed (slate) · Cancelled (merah solid)
- **Responsif**: mobile-first, layout berubah di breakpoint `sm` / `md` / `lg`
- **Alpine.js**: dipakai untuk modal monitoring, dropdown navbar, wizard step, canvas tanda tangan

---

## 7. Skema Database (Ringkas)

| Tabel | Kolom penting |
|---|---|
| `users` | `name, username(unique), email, password, role, site, is_active` |
| `divisions` | `nama, kode, deskripsi` (orphan, tidak FK ke permit/user) |
| `permits` | `no_permit, user_id, work_inspection_id, site, tipe, status(enum), tanda_tangan(longText), approval_signatures(JSON), cancellation_signatures(JSON), submitted_at, closed_at, cancelled_at` |
| `permit_documents` | `permit_id(FK cascade), nama_dokumen, file_path, file_type, file_size` |
| `classifications` | `name, code(unique), description` |
| `classification_permit` | pivot `classification_id + permit_id` |
| `work_inspections` | `no_periksa, site, requested_permit_id, tipe, answers(JSON), loto_*, tanda_tangan, staff_signature, status, reviewed_by, reviewed_at` |
| `contractors` | `token, vendor, pic, site, approval_status, hse_plan_id` |
| `contractor_documents` | `contractor_id(FK), file_path` |
| `hse_plans` | `no_hse_plan, no_po, site, tipe, personil(JSON), apd(JSON), peralatan(JSON), tanda_tangan, manager_signature, status, reviewed_by` |
| `hse_plan_documents` | `hse_plan_id(FK), file_path` |

---

## 8. Klasifikasi Pekerjaan

Enam kode klasifikasi tersedia sebagai master:

| Kode | Label |
|---|---|
| `panas` | Pekerjaan Panas |
| `ketinggian` | Pekerjaan di Ketinggian |
| `ruang_terbatas` | Pekerjaan Ruang Terbatas |
| `galian` | Pekerjaan Galian |
| `tegangan_tinggi` | Pekerjaan Tegangan Tinggi |
| `radiasi` | Pekerjaan Radiasi |

---

## 9. PDF

| Dokumen | Route | Akses |
|---|---|---|
| Safety Permit | `/divisi/permits/{id}/pdf/download` | Divisi pemilik (semua status) |
| Safety Permit | `/admin/permits/{id}/pdf/download` | Staff/Manager (gate: Review Staff/Manager, Revision, Active, Closed) |
| Safety Permit | `/superadmin/permits/{id}/pdf/download` | Superadmin (gate: Review Staff/Manager, Revision, Active, Closed) |
| Daftar Periksa | `/admin/work-inspections/{id}/pdf/download` | Staff HSE site terkait (semua status) |
| HSE Plan | `/admin/hse-plans/{id}/pdf/download` | Staff/Manager |

Semua PDF dibuat dengan `barryvdh/laravel-dompdf`, ukuran A4 portrait.

---

## 10. Notifikasi Email

- **PermitMasukMail**: dikirim ke Staff HSE saat permit masuk `Review Staff`
- **PermitDibatalkanMail**: dikirim ke Staff HSE saat permit dibatalkan divisi
- Service `PermitMailNotifier` menangani pengiriman

---

## 11. Multi-Site

Sistem mendukung dua lokasi: **Madiun** dan **Banyuwangi**.

- Setiap permit, work inspection, HSE Plan, dan kontraktor memiliki kolom `site`
- Staff HSE hanya dapat melihat data sesuai `users.site` miliknya
- Manager HSE dan Superadmin dapat melihat semua site
- Halaman publik memiliki filter tab per site

---

## 12. Batasan & Bug Terdokumentasi

> Detail lengkap di `perbaikanbug.md`

- `Submitted` adalah orphan status — submit langsung ke `Review Staff`, jangan tambahkan logika baru di status ini
- Kolom `active_at` dirujuk di `ApprovalController` tapi tidak ada di migrasi (bug #4)
- `closed_at` belum diisi otomatis oleh kode manapun (bug terdokumentasi)
- Tanda tangan adalah base64 data-URL PNG dari canvas, bukan file upload
- `DivisionSeeder` tidak dipanggil `DatabaseSeeder` — tabel `divisions` perlu diisi manual via superadmin

---

## 13. Akun Demo

Semua password: `password`

| Username | Role | Halaman awal |
|---|---|---|
| `superadmin` | superadmin | `/superadmin/dashboard` |
| `divisi_teknik` | divisi | `/divisi/dashboard` |
| `staff_hse` | staff | `/admin/dashboard` |
| `manager_hse` | manager | `/admin/dashboard` |
