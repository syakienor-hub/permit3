<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daftar Periksa {{ $inspection->no_periksa }}</title>
    <style>
        @page { margin: 18px 24px 16px 24px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 8.8px; line-height: 1.3; color: #000; }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }

        .header td { border: 1px solid #000; padding: 4px 6px; vertical-align: middle; }
        .header-logo { width: 20%; text-align: center; }
        .header-title { width: 52%; text-align: center; font-size: 12.5px; font-weight: bold; letter-spacing: .2px; }
        .header-meta { width: 28%; font-size: 7.8px; }
        .header-meta table td { border: none; padding: 0; }
        .logo { width: 92px; height: auto; }

        .meta-table { margin-top: 6px; }
        .meta-table td { border: 1px solid #000; padding: 3px 5px; }
        .meta-label { width: 14%; background: #f2f2f2; font-weight: bold; }
        .meta-value { width: 36%; }

        .section-title {
            margin-top: 8px;
            border: 1px solid #000;
            border-bottom: none;
            padding: 3px 6px;
            background: #e8e8e8;
            font-weight: bold;
            font-size: 8.8px;
        }

        .q-table th, .q-table td { border: 1px solid #000; padding: 2px 4px; }
        .q-table th { text-align: center; background: #f0f0f0; font-size: 8px; }
        .q-num { width: 5%; text-align: center; }
        .q-choice { width: 8%; text-align: center; font-weight: bold; }
        .mark { font-family: "DejaVu Sans", sans-serif; font-size: 10px; font-weight: bold; }

        .data-table td { border: 1px solid #000; padding: 3px 5px; }
        .data-label { width: 20%; background: #f7f7f7; font-weight: bold; }
        .data-value { width: 30%; }

        .legend { margin-top: 5px; font-size: 7.8px; }
        .legend span { display: inline-block; margin-right: 14px; }

        .sign-table { margin-top: 10px; }
        .sign-table td { border: 1px solid #000; padding: 4px 6px; text-align: center; }
        .sign-title { background: #f0f0f0; font-weight: bold; }
        .sign-space { height: 48px; vertical-align: middle; }
        .sign-name { font-weight: bold; }
        .sig-img { max-height: 42px; max-width: 170px; }

        .status-line { margin: 7px 0 0; font-size: 8.2px; }
        .rejected { margin-top: 5px; border: 1px solid #b91c1c; color: #b91c1c; padding: 4px 6px; }
        .footer { margin-top: 10px; font-size: 7.4px; color: #333; }
        .footer-table td { border: none; padding: 0; }
    </style>
</head>
<body>
    @php
        $items = \App\Models\WorkInspection::ITEMS;
        $options = \App\Models\WorkInspection::ANSWER_OPTIONS;
        $statusLabel = match($inspection->status) {
            \App\Models\WorkInspection::STATUS_APPROVED => 'Disetujui',
            \App\Models\WorkInspection::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
        $logoPath = public_path('assets/images/logoinka.svg');
        $tanggalTerbit = $inspection->tanggal_terbit ?? $inspection->created_at;
    @endphp

    <table class="header">
        <tr>
            <td class="header-logo">
                @if(file_exists($logoPath))
                    <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="Logo INKA">
                @else
                    <strong style="font-size: 15px;">INKA</strong>
                @endif
            </td>
            <td class="header-title">
                DAFTAR PERIKSA IZIN PEKERJAAN<br>BERISIKO TINGGI
            </td>
            <td class="header-meta">
                <table>
                    <tr><td style="width: 34%; font-weight: bold;">No. Dokumen</td><td>: FORM K3LH IV - Rev.0</td></tr>
                    <tr><td style="font-weight: bold;">Tgl. Terbit</td><td>: {{ $tanggalTerbit?->format('d/m/Y') ?? '-' }}</td></tr>
                    <tr><td style="font-weight: bold;">Hal</td><td>: 1 dari 1</td></tr>
                    <tr><td style="font-weight: bold;">Otorisasi</td><td>: {{ $inspection->otorisasi ?: '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <p class="status-line"><strong>Status:</strong> {{ $statusLabel }} &nbsp;&middot;&nbsp; <strong>Site:</strong> {{ $inspection->site }} &nbsp;&middot;&nbsp; <strong>No. Periksa:</strong> {{ $inspection->no_periksa }}</p>

    @if($inspection->status === \App\Models\WorkInspection::STATUS_REJECTED && $inspection->rejection_reason)
        <div class="rejected"><strong>Alasan ditolak:</strong> {{ $inspection->rejection_reason }}</div>
    @endif

    <table class="meta-table">
        <tr>
            <td class="meta-label">Pekerjaan</td>
            <td class="meta-value" colspan="3">{{ $inspection->nama_pekerjaan }}</td>
        </tr>
        <tr>
            <td class="meta-label">Peralatan</td>
            <td class="meta-value">{{ $inspection->jenis_peralatan ?: '-' }}</td>
            <td class="meta-label">Material</td>
            <td class="meta-value">{{ $inspection->material ?: '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Lokasi</td>
            <td class="meta-value">{{ $inspection->lokasi }}</td>
            <td class="meta-label">Mulai tanggal</td>
            <td class="meta-value">{{ $inspection->tanggal_mulai?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Petugas pemeriksa</td>
            <td class="meta-value" colspan="3">{{ $inspection->petugas_pemeriksa ?: '-' }}</td>
        </tr>
    </table>

    <div class="section-title">ITEM PENGECEKAN</div>
    <table class="q-table">
        <thead>
            <tr>
                <th class="q-num">NO.</th>
                <th>ITEM PENGECEKAN</th>
                <th class="q-choice">OK</th>
                <th class="q-choice">NOK</th>
                <th class="q-choice">N/A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $key => $item)
                @php $value = $inspection->answer($key); @endphp
                <tr>
                    <td class="q-num">{{ $loop->iteration }}</td>
                    <td>{{ $item }}</td>
                    <td class="q-choice">{!! $value === 'ok' ? '<span class="mark">&#10003;</span>' : '&nbsp;' !!}</td>
                    <td class="q-choice">{!! $value === 'nok' ? '<span class="mark">&#10007;</span>' : '&nbsp;' !!}</td>
                    <td class="q-choice">{!! $value === 'na' ? '<span class="mark">&#8212;</span>' : '&nbsp;' !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <span><strong>OK</strong> : Sesuai standar</span>
        <span><strong>NOK</strong> : Perlu tindak lanjut</span>
        <span><strong>N/A</strong> : Tidak relevan</span>
    </div>

    <table class="sign-table">
        <tr>
            <td class="sign-title" style="width: 50%;">Mengetahui<br>Plt. Manager QM&amp;SHE {{ $inspection->site }}</td>
            <td class="sign-title" style="width: 50%;">Staff QM&amp;SHE {{ $inspection->site }}</td>
        </tr>
        <tr>
            <td class="sign-space">
                @if($inspection->tanda_tangan)
                    <img src="{{ $inspection->tanda_tangan }}" class="sig-img" alt="Tanda tangan manager">
                @endif
            </td>
            <td class="sign-space">
                @if($inspection->staff_signature)
                    <img src="{{ $inspection->staff_signature }}" class="sig-img" alt="Tanda tangan staff">
                @endif
            </td>
        </tr>
        <tr>
            <td class="sign-name">({{ $inspection->manager_nama ?: '.............................' }})</td>
            <td class="sign-name">({{ $inspection->safety_officer_nama ?: '.............................' }})</td>
        </tr>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>Form K3LH IV - Rev.0 DAFTAR PERIKSA IZIN PEKERJAAN BERISIKO TINGGI PT INKA Banyuwangi</td>
                <td style="text-align: right;">Dokumen Refer : PK3LH - 08.02 Penanggulangan Keadaan Darurat</td>
            </tr>
        </table>
    </div>
</body>
</html>
