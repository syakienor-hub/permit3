<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daftar Periksa {{ $inspection->no_periksa }}</title>
    <style>
        @page { margin: 22px 28px 20px 28px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 9.6px; line-height: 1.35; color: #000; }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }
        .header td { border: 1px solid #000; padding: 5px 7px; vertical-align: middle; }
        .header-logo { width: 22%; text-align: center; }
        .header-title { width: 48%; text-align: center; font-size: 13px; font-weight: bold; letter-spacing: .3px; }
        .header-meta { width: 30%; font-size: 8.3px; }
        .header-meta table td { border: none; padding: 0; }
        .logo { width: 80px; height: auto; }
        .section-title { background: #e8e8e8; border: 1px solid #000; border-bottom: none; padding: 3px 6px; font-weight: bold; font-size: 9.2px; margin-top: 8px; }
        .data-table td { border: 1px solid #000; padding: 3px 5px; }
        .data-label { width: 22%; background: #f7f7f7; }
        .data-value { width: 28%; }
        .q-table th, .q-table td { border: 1px solid #000; padding: 2px 4px; }
        .q-table th { text-align: center; background: #f0f0f0; font-size: 8.5px; }
        .q-num { width: 5%; text-align: center; }
        .q-choice { width: 8%; text-align: center; font-weight: bold; }
        .mark { font-family: "DejaVu Sans", sans-serif; font-size: 10px; font-weight: bold; }
        .q-note { padding-left: 14px !important; font-style: italic; border-top: none !important; }
        .loto-table { border-collapse: collapse; width: 100%; margin-top: 8px; }
        .loto-table td { border: 1px solid #000; padding: 3px 5px; vertical-align: top; }
        .loto-title { background: #e8e8e8; font-weight: bold; font-size: 9.2px; padding: 3px 6px !important; }
        .loto-text { padding: 6px !important; }
        .loto-label { background: #f7f7f7; }
        .loto-sig-title { text-align: center; font-weight: bold; background: #f0f0f0; }
        .loto-sig-space { height: 40px; text-align: center; vertical-align: middle; }
        .loto-sig-name { text-align: center; }
        .sig-img { max-height: 36px; max-width: 160px; }
        .status-line { margin: 8px 0 0; font-size: 9px; }
        .rejected { margin-top: 6px; border: 1px solid #b91c1c; color: #b91c1c; padding: 5px 7px; }
        .page-footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 8px; color: #000; }
        .page-footer table td { border: none; padding: 0; }
        .page-footer .print-info { text-align: right; color: #555; }
        .muted { color: #555; }
    </style>
</head>
<body>
    @php
        $answerKeys = array_keys(\App\Models\WorkInspection::ANSWER_LABELS);
        $bagianA = array_slice($answerKeys, 0, 10);
        $bagianB = array_slice($answerKeys, 10);
        $statusLabel = match($inspection->status) {
            \App\Models\WorkInspection::STATUS_APPROVED => 'Disetujui',
            \App\Models\WorkInspection::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
        $logoPath = public_path('assets/images/logoinka.svg');
        $tanggal = $inspection->submitted_at ?? $inspection->created_at;
    @endphp

    <div class="page">
        <table class="header">
            <tr>
                <td class="header-logo">
                    @if(file_exists($logoPath))
                        <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo INKA">
                    @else
                        <strong style="font-size: 16px;">INKA</strong>
                    @endif
                </td>
                <td class="header-title">
                    DAFTAR PERIKSA IZIN PEKERJAAN BERISIKO TINGGI
                </td>
                <td class="header-meta">
                    <table>
                        <tr><td style="width: 26%;">No</td><td>: {{ $inspection->no_periksa }}</td></tr>
                        <tr><td>Tgl</td><td>: {{ $tanggal?->format('d/m/Y') ?? '-' }}</td></tr>
                        <tr><td>Halaman</td><td>: 01 dari 01</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <p class="status-line"><strong>Status:</strong> {{ $statusLabel }} &nbsp;&middot;&nbsp; <strong>Site:</strong> {{ $inspection->site }} &nbsp;&middot;&nbsp; <strong>Tipe:</strong> {{ $inspection->tipe }}</p>

        @if($inspection->status === \App\Models\WorkInspection::STATUS_REJECTED && $inspection->rejection_reason)
            <div class="rejected"><strong>Alasan ditolak:</strong> {{ $inspection->rejection_reason }}</div>
        @endif

        <div class="section-title">1. DATA PEKERJAAN</div>
        <table class="data-table">
            <tr>
                <td class="data-label">Divisi / Unit Pengaju</td>
                <td class="data-value">{{ $inspection->divisi_pengaju }}</td>
                <td class="data-label">Kontraktor / Pelaksana</td>
                <td class="data-value">{{ $inspection->kontraktor }}</td>
            </tr>
            <tr>
                <td class="data-label">Pekerjaan</td>
                <td class="data-value" colspan="3">{{ $inspection->nama_pekerjaan }}</td>
            </tr>
            <tr>
                <td class="data-label">Jenis Peralatan / Material</td>
                <td class="data-value">{{ $inspection->jenis_peralatan ?: '-' }}</td>
                <td class="data-label">Lokasi</td>
                <td class="data-value">{{ $inspection->lokasi }}</td>
            </tr>
            <tr>
                <td class="data-label">Mulai Tanggal</td>
                <td class="data-value">{{ $inspection->tanggal_mulai?->format('d/m/Y') ?? '-' }}</td>
                <td class="data-label">No. Telepon</td>
                <td class="data-value">{{ $inspection->telepon }}</td>
            </tr>
            <tr>
                <td class="data-label">Safety Permit Terkait</td>
                <td class="data-value">{{ $inspection->permit->no_permit ?? 'Belum ada' }}</td>
                <td class="data-label">Diajukan</td>
                <td class="data-value">{{ $tanggal?->format('d/m/Y H:i') ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">2. PEMERIKSAAN SUMBER ENERGI &amp; AREA KERJA</div>
        <table class="q-table">
            <thead>
                <tr>
                    <th class="q-num">No</th>
                    <th>Pemeriksaan</th>
                    <th class="q-choice">YA</th>
                    <th class="q-choice">TIDAK</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bagianA as $index => $key)
                    @php $value = $inspection->answer($key); @endphp
                    <tr>
                        <td class="q-num">{{ $index + 1 }}</td>
                        <td>{{ \App\Models\WorkInspection::ANSWER_LABELS[$key] }}</td>
                        <td class="q-choice">{!! $value === true ? '<span class="mark">&#10003;</span>' : '&nbsp;' !!}</td>
                        <td class="q-choice">{!! $value === false ? '<span class="mark">&#10007;</span>' : '&nbsp;' !!}</td>
                    </tr>
                    @if($key === 'izin_lain' && !empty($inspection->answers['izin_lain_ket']))
                        <tr>
                            <td class="q-num"></td>
                            <td class="q-note" colspan="3">Izin lain: {{ $inspection->answers['izin_lain_ket'] }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="section-title">3. PEMERIKSAAN LANJUTAN</div>
        <table class="q-table">
            <thead>
                <tr>
                    <th class="q-num">No</th>
                    <th>Pemeriksaan</th>
                    <th class="q-choice">YA</th>
                    <th class="q-choice">TIDAK</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bagianB as $index => $key)
                    @php $value = $inspection->answer($key); @endphp
                    <tr>
                        <td class="q-num">{{ $index + 11 }}</td>
                        <td>{{ \App\Models\WorkInspection::ANSWER_LABELS[$key] }}</td>
                        <td class="q-choice">{!! $value === true ? '<span class="mark">&#10003;</span>' : '&nbsp;' !!}</td>
                        <td class="q-choice">{!! $value === false ? '<span class="mark">&#10007;</span>' : '&nbsp;' !!}</td>
                    </tr>
                    @if($key === 'apd_khusus' && !empty($inspection->answers['apd_khusus_ket']))
                        <tr>
                            <td class="q-num"></td>
                            <td class="q-note" colspan="3">APD khusus: {{ $inspection->answers['apd_khusus_ket'] }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <table class="loto-table">
            <tr>
                <td colspan="12" class="loto-title">4. LOTO &amp; TANDA TANGAN</td>
            </tr>
            <tr>
                <td colspan="12" class="loto-text">"LOTO (Lock Out &amp; Tag Out)" telah diisi/dipasang dengan sebenarnya oleh:</td>
            </tr>
            <tr>
                <td colspan="3" class="loto-label">Nama Pelaksana LOTO</td>
                <td colspan="3">{{ $inspection->loto_nama }}</td>
                <td colspan="3" class="loto-label">Tanggal / Jam</td>
                <td colspan="3">
                    {{ $inspection->loto_tanggal?->format('d/m/Y') ?? '-' }}
                    {{ $inspection->loto_jam ? '/ ' . $inspection->loto_jam : '' }}
                </td>
            </tr>
            <tr>
                <td colspan="3" class="loto-label">Supervisor</td>
                <td colspan="3">{{ $inspection->supervisor_nama ?: '-' }}</td>
                <td colspan="3" class="loto-label">Tanggal / Jam</td>
                <td colspan="3">
                    {{ $inspection->supervisor_tanggal?->format('d/m/Y') ?? '-' }}
                    {{ $inspection->supervisor_jam ? '/ ' . $inspection->supervisor_jam : '' }}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="loto-sig-title">Pelaksana LOTO</td>
                <td colspan="4" class="loto-sig-title">Diperiksa Supervisor</td>
                <td colspan="4" class="loto-sig-title">Safety Officer</td>
            </tr>
            <tr>
                <td colspan="4" class="loto-sig-space">
                    @if($inspection->tanda_tangan)
                        <img src="{{ $inspection->tanda_tangan }}" class="sig-img" alt="Tanda tangan pelaksana">
                    @endif
                </td>
                <td colspan="4" class="loto-sig-space"></td>
                <td colspan="4" class="loto-sig-space">
                    @if($inspection->staff_signature)
                        <img src="{{ $inspection->staff_signature }}" class="sig-img" alt="Tanda tangan Safety Officer">
                    @else
                        <span class="muted">{{ $statusLabel === 'Disetujui' ? '-' : 'Menunggu verifikasi' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="4" class="loto-sig-name">
                    <strong>{{ $inspection->loto_nama }}</strong><br>
                    {{ $inspection->loto_tanggal?->format('d/m/Y') ?? '-' }}{{ $inspection->loto_jam ? ' / ' . $inspection->loto_jam : '' }}
                </td>
                <td colspan="4" class="loto-sig-name">
                    <strong>{{ $inspection->supervisor_nama ?: '..........................' }}</strong><br>
                    {{ $inspection->supervisor_tanggal?->format('d/m/Y') ?? '..........................' }}{{ $inspection->supervisor_jam ? ' / ' . $inspection->supervisor_jam : '' }}
                </td>
                <td colspan="4" class="loto-sig-name">
                    <strong>{{ $inspection->safety_officer_nama ?: '..........................' }}</strong><br>
                    {{ $inspection->reviewed_at?->format('d/m/Y') ?? '..........................' }}
                </td>
            </tr>
        </table>

    </div>

    <div class="page-footer">
        <table>
            <tr>
                <td>Form K3LH: IV-01.013 Rev. 0</td>
                <td class="print-info">Dicetak dari SIMONIKA &middot; {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
