<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>HSE Plan {{ $hsePlan->no_hse_plan }}</title>
    <style>
        @page { margin: 32px 38px 30px 38px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; line-height: 1.35; color: #000; }
        table { border-collapse: collapse; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }
        .header { width: 100%; border: 1px solid #000; margin-bottom: 10px; }
        .header td { border: 1px solid #000; vertical-align: middle; }
        .header-logo { width: 40%; padding: 5px 8px; text-align: center; }
        .header-title { width: 30%; text-align: center; font-size: 19px; font-weight: bold; letter-spacing: 0.5px; }
        .header-meta { width: 30%; padding: 5px 7px; font-size: 9.5px; }
        .header-meta table { width: 100%; }
        .header-meta td { border: none; padding: 1px 0; vertical-align: top; }
        .logo { width: 105px; }
        .form-box { border: 1px solid #000; padding: 12px 16px; min-height: 920px; }
        .form-box-page-two { border: 1px solid #000; padding: 12px 16px; min-height: 920px; }
        .field-table { width: 100%; margin-bottom: 4px; }
        .field-table td { border: none; padding: 2px 0; vertical-align: top; }
        .field-label { width: 34%; }
        .field-colon { width: 2%; }
        .field-sub { padding-left: 22px !important; }
        .field-value { font-weight: normal; }
        .personil-table, .alat-table, .apd-table { width: 100%; margin-top: 7px; margin-bottom: 4px; }
        .personil-table th, .personil-table td,
        .alat-table th, .alat-table td,
        .apd-table td { border: 1px solid #000; padding: 2px 5px; vertical-align: middle; }
        .personil-table th, .alat-table th { font-weight: bold; text-align: center; }
        .personil-table td { height: 18px; }
        .alat-table { width: 62%; }
        .alat-table td { height: 18px; }
        .apd-table td { height: 21px; font-size: 9.5px; }
        .apd-box { font-family: monospace; font-size: 10px; }
        .note { font-style: italic; font-size: 9.5px; margin: 2px 0 10px 4px; }
        .section-title { margin: 13px 0 0; font-weight: normal; }
        .section-title-bold { margin: 18px 0 0; font-weight: bold; }
        .answer-table { width: 72%; margin: 5px 0 0 20px; }
        .answer-table td { border: none; padding: 2px 0; width: 50%; }
        .answer-note { margin: 3px 0 0 20px; }
        .signature-table { width: 100%; margin-top: 42px; }
        .signature-table td { border: none; width: 50%; vertical-align: top; }
        .signature-space { height: 60px; text-align: center; vertical-align: middle !important; }
        .signature-img { max-height: 50px; max-width: 210px; }
        .signature-name { margin-top: 2px; }
        .form-code { margin-top: 7px; font-size: 11px; }
    </style>
</head>
<body>
    @php
        $personil = $hsePlan->personil ?? [];
        $peralatan = $hsePlan->peralatan ?? [];
        $apdDipilih = $hsePlan->apd ?? [];
        $apdOptions = [
            'helm' => 'Helm Keselamatan',
            'sepatu' => 'Sepatu Keselamatan',
            'pelindung_muka' => 'Pelindung Muka',
            'kacamata' => 'Kacamata Keselamatan',
            'rompi' => 'Rompi Keselamatan',
            'masker' => 'Masker/Respirator',
            'sarung_tangan' => 'Sarung Tangan',
            'ear_plug' => 'Ear Plug/Ear Muff',
            'baju_pelindung' => 'Baju Pelindung',
            'safety_harness' => 'Safety Harness',
        ];
        $apdOrder = [
            'helm', 'sepatu', 'pelindung_muka',
            'kacamata', 'rompi', 'masker',
            'sarung_tangan', 'ear_plug', null,
            'baju_pelindung', 'safety_harness', null,
        ];
        $apdCells = [];
        $apdLainnya = $hsePlan->apd_lainnya;

        foreach ($apdOrder as $key) {
            if ($key === null) {
                $apdCells[] = ['key' => null, 'label' => $apdLainnya ?: '....................'];
                $apdLainnya = null;
            } else {
                $apdCells[] = ['key' => $key, 'label' => $apdOptions[$key]];
            }
        }

        $tanggal = $hsePlan->submitted_at ?? $hsePlan->created_at;
        $manager = $hsePlan->reviewer;
        $approved = $hsePlan->status === \App\Models\HsePlan::STATUS_APPROVED;
        $logoPath = public_path('assets/images/logoinka.svg');
    @endphp

    <div class="page">
        <table class="header">
            <tr>
                <td class="header-title">HSE PLAN</td>
                <td class="header-logo">
                    @if(file_exists($logoPath))
                        <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo INKA">
                    @else
                        <strong style="font-size: 18px;">INKA</strong>
                    @endif
                </td>
                <td class="header-meta">
                    <table>
                        <tr><td style="width: 20%;">No</td><td>: {{ $hsePlan->no_hse_plan }}</td></tr>
                        <tr><td>Tgl</td><td>: {{ $tanggal?->format('d/m/Y') ?? '-' }}</td></tr>
                        <tr><td>Halaman</td><td>: 01 dari 02</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="form-box">
            <table class="field-table">
                <tr>
                    <td class="field-label">1. Nomor PO</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">{{ $hsePlan->no_po ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="field-label">2. Deskripsi Pekerjaan</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">{{ $hsePlan->deskripsi_pekerjaan }}</td>
                </tr>
                <tr>
                    <td class="field-label">3. Lama Pekerjaan</td>
                    <td class="field-colon"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="field-label field-sub">a. Tanggal</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">
                        {{ $hsePlan->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                        &nbsp;s/d&nbsp;
                        {{ $hsePlan->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="field-label field-sub">b. Jam</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">
                        {{ $hsePlan->jam_mulai ?: '-' }}
                        &nbsp;s/d&nbsp;
                        {{ $hsePlan->jam_selesai ?: '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="field-label">4. Nama Kontraktor</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">{{ $hsePlan->nama_kontraktor }}</td>
                </tr>
                <tr>
                    <td class="field-label">5. Nama Pimpinan Proyek</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">{{ $hsePlan->nama_pimpinan_proyek }}</td>
                </tr>
                <tr>
                    <td class="field-label">6. Nama &amp; Tugas Setiap Personil</td>
                    <td class="field-colon">:</td>
                    <td></td>
                </tr>
            </table>

            <table class="personil-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 42%;">Nama Personil</th>
                        <th style="width: 50%;">Tugas Personil</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < max(5, count($personil)); $i++)
                        <tr>
                            <td style="text-align: center;">{{ isset($personil[$i]) ? $i + 1 : '' }}</td>
                            <td>{{ $personil[$i]['nama'] ?? '' }}</td>
                            <td>{{ $personil[$i]['tugas'] ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            <p class="note">*Wajib melampirkan sertifikat keahlian setiap personil sesuai dengan persyaratan peraturan</p>

            <p class="section-title">7. Jenis APD yang digunakan</p>
            <table class="apd-table">
                @foreach(array_chunk($apdCells, 3) as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>
                                <span class="apd-box">[{{ $cell['key'] && in_array($cell['key'], $apdDipilih, true) ? 'X' : ' ' }}]</span>
                                {{ $cell['label'] }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>

            <p class="section-title">8. Daftar peralatan kerja</p>
            <table class="alat-table">
                <thead>
                    <tr>
                        <th style="width: 65%;">Nama Alat</th>
                        <th style="width: 35%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < max(5, count($peralatan)); $i++)
                        <tr>
                            <td>{{ $peralatan[$i]['nama'] ?? '' }}</td>
                            <td style="text-align: center;">{{ $peralatan[$i]['jumlah'] ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            <p class="note">*Wajib melampirkan sertifikat peralatan yang dipersyaratkan dalam peraturan.</p>

            <p class="section-title-bold">9. Wajib melampirkan analisa keselamatan pekerjaan (Job Safety Analysis)</p>

            <p class="section-title">10. Apakah pekerjaan menggunakan bahan-bahan B3?</p>
            <table class="answer-table">
                <tr>
                    <td>Ya <span class="apd-box">[{{ $hsePlan->bahan_b3 ? 'X' : ' ' }}]</span></td>
                    <td>Tidak <span class="apd-box">[{{ $hsePlan->bahan_b3 ? ' ' : 'X' }}]</span></td>
                </tr>
            </table>
            <p class="answer-note">Jika &ldquo;Ya&rdquo; maka wajib melampirkan MSDS</p>
        </div>
    </div>

    <div class="page">
        <table class="header">
            <tr>
                <td class="header-title">HSE PLAN</td>
                <td class="header-logo">
                    @if(file_exists($logoPath))
                        <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo INKA">
                    @else
                        <strong style="font-size: 18px;">INKA</strong>
                    @endif
                </td>
                <td class="header-meta">
                    <table>
                        <tr><td style="width: 20%;">No</td><td>: {{ $hsePlan->no_hse_plan }}</td></tr>
                        <tr><td>Tgl</td><td>: {{ $tanggal?->format('d/m/Y') ?? '-' }}</td></tr>
                        <tr><td>Halaman</td><td>: 02 dari 02</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="form-box-page-two">
            <p class="section-title" style="margin-top: 0;">11. Apakah pekerjaan menghasilkan limbah B3?</p>
            <table class="answer-table">
                <tr>
                    <td>Ya <span class="apd-box">[{{ $hsePlan->limbah_b3 ? 'X' : ' ' }}]</span></td>
                    <td>Tidak <span class="apd-box">[{{ $hsePlan->limbah_b3 ? ' ' : 'X' }}]</span></td>
                </tr>
            </table>
            <p class="answer-note">Jika &ldquo;Ya&rdquo; maka wajib melampirkan rencana pengelolaan limbah B3</p>

            <p class="section-title">12. Memiliki prosedur/instruksi kerja yang terkait</p>
            <table class="answer-table">
                <tr>
                    <td>Ya <span class="apd-box">[{{ $hsePlan->prosedur_kerja ? 'X' : ' ' }}]</span></td>
                    <td>Tidak <span class="apd-box">[{{ $hsePlan->prosedur_kerja ? ' ' : 'X' }}]</span></td>
                </tr>
            </table>
            <p class="answer-note">Jika &ldquo;Ya&rdquo; maka wajib melampirkan prosedur/instruksi kerja</p>

            <p class="section-title">13. Memiliki rencana pemantauan/inspeksi selama proses pekerjaan berlangsung</p>
            <table class="answer-table">
                <tr>
                    <td>Ya <span class="apd-box">[{{ $hsePlan->rencana_inspeksi ? 'X' : ' ' }}]</span></td>
                    <td>Tidak <span class="apd-box">[{{ $hsePlan->rencana_inspeksi ? ' ' : 'X' }}]</span></td>
                </tr>
            </table>
            <p class="answer-note">Jika &ldquo;Ya&rdquo; maka wajib melampirkan rencana pemantauan/inspeksi</p>

            <p class="section-title">14. Menyediakan fasilitas peralatan tanggap darurat seperti APAR dan lain sebagainya</p>
            <table class="answer-table">
                <tr>
                    <td>Ya <span class="apd-box">[{{ $hsePlan->tanggap_darurat ? 'X' : ' ' }}]</span></td>
                    <td>Tidak <span class="apd-box">[{{ $hsePlan->tanggap_darurat ? ' ' : 'X' }}]</span></td>
                </tr>
            </table>

            <p class="section-title">15. Memiliki struktur organisasi proyek</p>
            <table class="answer-table">
                <tr>
                    <td>Ya <span class="apd-box">[{{ $hsePlan->struktur_organisasi ? 'X' : ' ' }}]</span></td>
                    <td>Tidak <span class="apd-box">[{{ $hsePlan->struktur_organisasi ? ' ' : 'X' }}]</span></td>
                </tr>
            </table>
            <p class="answer-note">Jika &ldquo;Ya&rdquo; maka wajib melampirkan struktur organisasi proyek</p>

            <p style="margin-top: 70px;">Madiun, {{ $tanggal?->format('d/m/Y') ?? '....................' }}</p>

            <table class="signature-table">
                <tr>
                    <td>
                        <p style="margin: 0;">Dibuat oleh,<br>Kontraktor</p>
                        <div class="signature-space">
                            @if($hsePlan->tanda_tangan)
                                <img src="{{ $hsePlan->tanda_tangan }}" class="signature-img" alt="Tanda tangan kontraktor">
                            @endif
                        </div>
                        <p class="signature-name">({{ $hsePlan->nama_pimpinan_proyek }})</p>
                    </td>
                    <td>
                        <p style="margin: 0;">Disetujui oleh,<br>Manager HSE</p>
                        <div class="signature-space">
                            @if($approved && $hsePlan->manager_signature)
                                <img src="{{ $hsePlan->manager_signature }}" class="signature-img" alt="Tanda tangan Manager HSE">
                            @endif
                        </div>
                        <p class="signature-name">
                            ({{ $approved && $manager ? $manager->name : '..............................' }})
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <p class="form-code">Form K3LH : IV-01.053</p>
    </div>
</body>
</html>
