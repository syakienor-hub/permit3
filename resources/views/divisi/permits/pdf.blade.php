<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Permit {{ $permit->no_permit }}</title>
    <style>
        @page { margin: 14px 22px; }
        body { font-family: sans-serif; font-size: 9.5px; line-height: 1.3; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 2px 3px; vertical-align: top; }
        .no-border { border: none !important; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .section-header { background-color: #000; color: #fff; font-weight: bold; padding: 4px; font-size: 10px; }
        .checkbox-container { display: inline-block; width: 33%; margin-bottom: 4px; }
        .checkbox-container-4 { display: inline-block; width: 24%; margin-bottom: 4px; }
        .checkbox-box { display: inline-block; width: 9px; height: 9px; border: 1px solid #000; margin-right: 3px; text-align: center; line-height: 9px; font-size: 7px; font-weight: bold; }
        .header-table td { padding: 4px; }
        .logo { width: 110px; }
        
        .box { border: 1px solid #000; display: inline-block; width: 10px; height: 10px; text-align: center; line-height: 10px; font-weight: bold; font-family: monospace; font-size: 9px; vertical-align: middle; }
        .sig-img { max-height: 28px; }
        .sig-cell { height: 32px; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table" style="margin-bottom: 4px;">
        <tr>
            <td width="25%" align="center" class="text-center" style="vertical-align: middle; text-align: center;">
                @php
                    $path = public_path('assets/images/logoinka.svg');
                    // DomPDF sometimes struggles with SVG directly, so we use base64 or fallback to text if missing
                    if(file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        echo '<img src="'.$base64.'" class="logo" style="display:block; margin-left:auto; margin-right:auto; padding-left:25px; padding-top:6px;" alt="Logo INKA">';
                    } else {
                        echo '<h1 style="color:#d32f2f; margin:0;">INKA</h1>';
                    }
                @endphp
            </td>
            <td width="50%" class="text-center font-bold" style="vertical-align: middle; font-size: 13px;">
                SURAT IZIN PEKERJAAN BERESIKO<br>TINGGI
            </td>
            <td width="25%">
                <table style="border: none;">
                    <tr><td style="border: none; border-bottom: 1px solid #000; padding: 1px;">NO : {{ $permit->no_permit }}</td></tr>
                    <tr><td style="border: none; border-bottom: 1px solid #000; padding: 1px;">Tgl : {{ $permit->created_at->format('d/m/Y') }}</td></tr>
                    <tr><td style="border: none; padding: 1px;">Hal : ........... dari ...........</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- A. KLASIFIKASI -->
    <div class="section-header">A. KLASIFIKASI PEKERJAAN</div>
    <table style="margin-bottom: 4px;">
        <tr>
            @php 
                $klas = is_string($permit->klasifikasi_pekerjaan) ? json_decode($permit->klasifikasi_pekerjaan, true) : ($permit->klasifikasi_pekerjaan ?? []); 
                $klasMap = [
                    'panas' => 'Pekerjaan Panas',
                    'ketinggian' => 'Pekerjaan Ketinggian',
                    'ruang_terbatas' => 'Ruang Terbatas',
                    'galian' => 'Pekerjaan Galian'
                ];
            @endphp
            @foreach($klasMap as $key => $label)
                <td width="25%">
                    <span class="box">{!! in_array($key, $klas) ? 'X' : '&nbsp;' !!}</span> {!! $label !!}
                </td>
            @endforeach
        </tr>
        <tr>
            @php 
                $klasMap2 = [
                    'tegangan_tinggi' => 'Pekerjaan Tegangan Tinggi',
                    'radiasi' => 'Radiasi',
                    '' => '',
                    ' ' => ''
                ];
            @endphp
            @foreach($klasMap2 as $key => $label)
                <td width="25%">
                    @if($label)
                        <span class="box">{!! in_array($key, $klas) ? 'X' : '&nbsp;' !!}</span> {!! $label !!}
                    @endif
                </td>
            @endforeach
        </tr>
    </table>

    <!-- B. INFORMASI -->
    <div class="section-header">B. INFORMASI PEKERJAAN</div>
    <table style="margin-bottom: 4px; border-bottom: none;">
        <tr>
            <td width="55%" style="padding: 0; border: none; border-right: 1px solid #000;">
                <table style="border: none; width: 100%;">
                    <table style="border: none;"> 
                        <table style="border: none;">
                            <tr><td width="40%" style="border: none;">Pekerjaan</td><td style="border: none;">: {{ $permit->nama_pekerjaan }}</td></tr>
                            <tr><td style="border: none;">Site</td><td style="border: none;">: {{ $permit->site ?? 'Madiun' }}</td></tr>
                            <tr><td style="border: none;">Lokasi</td><td style="border: none;">: {{ $permit->lokasi }}</td></tr>
                            <tr><td style="border: none;">Manager / Penanggung Jawab</td><td style="border: none;">: {{ $permit->penanggung_jawab }}</td></tr>
                            <tr><td style="border: none;">No. Telpon</td><td style="border: none;">: {{ $permit->telepon }}</td></tr>
                            <tr><td style="border: none;">Perusahaan</td><td style="border: none;">: {{ $permit->kontraktor }}</td></tr>
                        </table>
                    </table>
                </table>
            </td>
            <td width="45%" style="padding: 0; border: none;">
                @php
                    $dp = is_string($permit->daftar_pekerja) ? json_decode($permit->daftar_pekerja, true) : ($permit->daftar_pekerja ?? []);
                    $pList = [
                        ['engineer', 'Engineer'],
                        ['operator_alat_berat', 'Operator Alat Berat'],
                        ['teknisi_listrik', 'Teknisi Listrik'],
                        ['mekanik', 'Mekanik'],
                        ['welder', 'Welder'],
                        ['operator', 'Operator'],
                        ['tukang_bangunan', 'Tukang Bangunan'],
                        ['tukang_kayu', 'Tukang Kayu'],
                        ['helper', 'Helper']
                    ];
                @endphp
                <table style="border: none;">
                    <tr>
                        <th width="80%" style="background-color: #ccc; border: none; border-bottom: 1px solid #000; border-right: 1px solid #000;">Daftar Pekerjaan</th>
                        <th width="20%" style="background-color: #ccc; border: none; border-bottom: 1px solid #000; border-right: 1px solid #000;">Jumlah</th>
                    </tr>
                    @foreach($pList as $p)
                        <tr>
                            <td style="border: none; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 2px 4px;">{{ $p[1] }}</td>
                            <td class="text-center" style="border: none; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 2px 4px;">{{ $dp[$p[0]] ?? '' }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    <!-- PERALATAN KERJA -->
    <table style="margin-bottom: 4px;">
        <tr>
            <th width="35%" style="background-color: #ccc;">Peralatan Kerja</th>
            <th width="15%" style="background-color: #ccc;">Jumlah</th>
            <th width="35%" style="background-color: #ccc;">Material</th>
            <th width="15%" style="background-color: #ccc;">Jumlah</th>
        </tr>
        @php
            $pk = is_string($permit->peralatan_kerja) ? json_decode($permit->peralatan_kerja, true) : ($permit->peralatan_kerja ?? []);
            // Pad to at least 3 rows to match design
            $pk = array_pad($pk, 3, ['alat'=>'', 'jumlah_alat'=>'', 'material'=>'', 'jumlah_material'=>'']);
        @endphp
        @foreach($pk as $row)
            <tr>
                <td class="text-center" style="height: 16px; padding: 2px 4px;">{{ $row['alat'] ?? '' }}</td>
                <td class="text-center" style="padding: 2px 4px;">{{ $row['jumlah_alat'] ?? '' }}</td>
                <td class="text-center" style="padding: 2px 4px;">{{ $row['material'] ?? '' }}</td>
                <td class="text-center" style="padding: 2px 4px;">{{ $row['jumlah_material'] ?? '' }}</td>
            </tr>
         @endforeach
    </table>

    <!-- C. BAHAYA PEKERJAAN -->
    <div class="section-header">C. BAHAYA PEKERJAAN</div>
    <div style="border: 1px solid #000; padding: 3px; margin-bottom: 4px;">
        @php
            $bp = is_string($permit->bahaya_pekerjaan) ? json_decode($permit->bahaya_pekerjaan, true) : ($permit->bahaya_pekerjaan ?? []);
            $bList1 = ['percikan_panas'=>'Percikan Panas', 'bahaya_kebakaran'=>'Bahaya Kebakaran', 'cidera_tulang_belakang'=>'Cidera Tulang Belakang', 'pencemaran_lingkungan'=>'Pencemaran Lingk.', 'terpukul_terbentur'=>'Terpukul / Terbentur', 'penerangan_kurang'=>'Penerangan Kurang', 'bahaya_makhluk_hidup'=>'Bahaya Makhluk Hidup'];
            $bList2 = ['jatuh_dari_ketinggian'=>'Jatuh Dari Ketinggian', 'lantai_licin'=>'Lantai Licin', 'tangga_tidak_kokoh'=>'Tangga / Penyangga<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tidak Kokoh / Patah', 'bising'=>'Bising', 'menghasilkan_debu'=>'Menghasilkan Debu', 'bahaya_angin'=>'Bahaya Angin'];
            $bList3 = ['keracunan_gas'=>'Keracunan Gas', 'peledakan'=>'Peledakan', 'bahaya_listrik'=>'Bahaya Alat / Aliran<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Listrik', 'bahaya_getaran'=>'Bahaya Getaran', 'bahaya_zat_kimia'=>'Bahaya Zat Kimia', 'terpotong_tertusuk'=>'Terpotong/Tertusuk'];
            $bList4 = ['terperosok'=>'Terperosok', 'tertimbun_tertimpa'=>'Tertimbun / Tertimpa', 'mata_kemasukan_benda'=>'Mata Kemasukan<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Benda', 'tertabrak_tabrakan'=>'Tertabrak / Tabrakan', 'limbah_b3'=>'Limbah B3', 'bahaya_radiasi'=>'Bahaya Radiasi'];
            
            $maxC = max(count($bList1), count($bList2), count($bList3), count($bList4));
            $k1 = array_keys($bList1); $v1 = array_values($bList1);
            $k2 = array_keys($bList2); $v2 = array_values($bList2);
            $k3 = array_keys($bList3); $v3 = array_values($bList3);
            $k4 = array_keys($bList4); $v4 = array_values($bList4);
        @endphp
        <table class="no-border">
        @for($i=0; $i<$maxC; $i++)
            <tr>
                <td class="no-border" width="25%" style="padding: 1px;">
                    @if(isset($k1[$i])) <span class="box">{!! in_array($k1[$i], $bp) ? 'X' : '&nbsp;' !!}</span> {!! $v1[$i] !!} @endif
                </td>
                <td class="no-border" width="25%" style="padding: 1px;">
                    @if(isset($k2[$i])) <span class="box">{!! in_array($k2[$i], $bp) ? 'X' : '&nbsp;' !!}</span> {!! $v2[$i] !!} @endif
                    @if($i == $maxC - 1) <span class="box">{!! $permit->bahaya_lainnya ? 'X' : '&nbsp;' !!}</span> {{ $permit->bahaya_lainnya ? substr($permit->bahaya_lainnya,0,20) : '................................' }} @endif
                </td>
                <td class="no-border" width="25%" style="padding: 1px;">
                    @if(isset($k3[$i])) <span class="box">{!! in_array($k3[$i], $bp) ? 'X' : '&nbsp;' !!}</span> {!! $v3[$i] !!} @endif
                    @if($i == $maxC - 1) <span class="box">&nbsp;</span> ................................ @endif
                </td>
                <td class="no-border" width="25%" style="padding: 1px;">
                    @if(isset($k4[$i])) <span class="box">{!! in_array($k4[$i], $bp) ? 'X' : '&nbsp;' !!}</span> {!! $v4[$i] !!} @endif
                    @if($i == $maxC - 1) <span class="box">&nbsp;</span> ................................ @endif
                </td>
            </tr>
        @endfor
        </table>
    </div>

    <!-- D. TINDAKAN PENCEGAHAN BAHAYA -->
    <div class="section-header">D. TINDAKAN PENCEGAHAN BAHAYA</div>
    <div style="border: 1px solid #000; padding: 3px; margin-bottom: 4px;">
        @php
            $tp = is_string($permit->tindakan_pencegahan) ? json_decode($permit->tindakan_pencegahan, true) : ($permit->tindakan_pencegahan ?? []);
            $tList1 = ['proteksi_dari_jatuh'=>'Proteksi Dari Jatuh', 'media_penghambat_api'=>'Media Penghambat Api / Percikan', 'pintu_masuk_keluar'=>'Pintu Masuk / Keluar', 'safety_briefing'=>'Safety Brifing'];
            $tList2 = ['tangga_penyangga_kokoh'=>'Tangga / Penyangga Yang Kokoh', 'rambu_rambu'=>'Rambu-Rambu', 'jalur_evakuasi'=>'Jalur Evakuasi'];
            $tList3 = ['penyediaan_pemadam_api'=>'Penyediaan Pemadam Api', 'barikade_pagar'=>'Barikade / Pagar / Police Line', 'sertifikat_kompetensi'=>'Sertifikat Kompetensi'];
            
            $maxD = max(count($tList1), count($tList2), count($tList3)) + 1; // +1 for lainnya
            $k1 = array_keys($tList1); $v1 = array_values($tList1);
            $k2 = array_keys($tList2); $v2 = array_values($tList2);
            $k3 = array_keys($tList3); $v3 = array_values($tList3);
        @endphp
        <table class="no-border">
        @for($i=0; $i<$maxD; $i++)
            <tr>
                <td class="no-border" width="33%" style="padding: 1px;">
                    @if(isset($k1[$i])) <span class="box">{!! in_array($k1[$i], $tp) ? 'X' : '&nbsp;' !!}</span> {!! $v1[$i] !!} @endif
                </td>
                <td class="no-border" width="33%" style="padding: 1px;">
                    @if(isset($k2[$i])) 
                        <span class="box">{!! in_array($k2[$i], $tp) ? 'X' : '&nbsp;' !!}</span> {!! $v2[$i] !!} 
                    @elseif($i == count($tList2))
                        <span class="box">{!! $permit->pencegahan_lainnya ? 'X' : '&nbsp;' !!}</span> {{ $permit->pencegahan_lainnya ? substr($permit->pencegahan_lainnya,0,25) : '.........................................' }}
                    @endif
                </td>
                <td class="no-border" width="34%" style="padding: 1px;">
                    @if(isset($k3[$i])) 
                        <span class="box">{!! in_array($k3[$i], $tp) ? 'X' : '&nbsp;' !!}</span> {!! $v3[$i] !!} 
                    @elseif($i == count($tList3))
                        <span class="box">&nbsp;</span> .........................................
                    @endif
                </td>
            </tr>
        @endfor
        </table>
    </div>

    <!-- E. ALAT PELINDUNG DIRI -->
    <div class="section-header">E. ALAT PELINDUNG DIRI</div>
    <div style="border: 1px solid #000; padding: 3px; margin-bottom: 4px;">
        @php
            $apd = is_string($permit->apd) ? json_decode($permit->apd, true) : ($permit->apd ?? []);
            $aList1 = ['helm_keselamatan'=>'Helm Keselamatan', 'kaca_mata_keselamatan'=>'Kaca Mata Keselamatan', 'sarung_tangan'=>'Sarung Tangan Kulit/Kaos/Karet', 'baju_pelindung'=>'Baju Pelindung'];
            $aList2 = ['sepatu_keselamatan'=>'Sepatu Keselamatan', 'kaca_mata_debu'=>'Kaca Mata Debu', 'rompi_keselamatan'=>'Rompi Keselamatan', 'ear_plug_muff'=>'Ear Plug / Ear Muff'];
            $aList3 = ['tali_sabuk_keselamatan'=>'Tali/ Sabuk Keselamatan', 'pelindung_muka'=>'Pelindung Muka', 'masker_respirator'=>'Masker / Respirator'];
            
            $maxE = max(count($aList1), count($aList2), count($aList3)) + 1;
            $k1 = array_keys($aList1); $v1 = array_values($aList1);
            $k2 = array_keys($aList2); $v2 = array_values($aList2);
            $k3 = array_keys($aList3); $v3 = array_values($aList3);
        @endphp
        <table class="no-border">
        @for($i=0; $i<$maxE; $i++)
            <tr>
                <td class="no-border" width="33%" style="padding: 1px;">
                    @if(isset($k1[$i])) <span class="box">{!! in_array($k1[$i], $apd) ? 'X' : '&nbsp;' !!}</span> {!! $v1[$i] !!} @endif
                </td>
                <td class="no-border" width="33%" style="padding: 1px;">
                    @if(isset($k2[$i])) 
                        <span class="box">{!! in_array($k2[$i], $apd) ? 'X' : '&nbsp;' !!}</span> {!! $v2[$i] !!} 
                    @endif
                </td>
                <td class="no-border" width="34%" style="padding: 1px;">
                    @if(isset($k3[$i])) 
                        <span class="box">{!! in_array($k3[$i], $apd) ? 'X' : '&nbsp;' !!}</span> {!! $v3[$i] !!} 
                    @elseif($i == count($aList3))
                        <span class="box">{!! $permit->apd_lainnya ? 'X' : '&nbsp;' !!}</span> {{ $permit->apd_lainnya ? substr($permit->apd_lainnya,0,25) : '.........................................' }}
                    @endif
                </td>
            </tr>
        @endfor
        </table>
    </div>

    <!-- F. VALIDASI KERJA -->
    <div class="section-header">F. VALIDASI KERJA</div>
    <table style="margin-bottom: 4px; table-layout: fixed;">
        <tr>
            <td width="60%" style="border-right: none; padding-right: 0;">
                <div>Izin diberikan sesuai pengajuan dan kondisi di atas :</div>
                <div style="margin-top: 3px;">Mulai Tgl &nbsp;: {{ $permit->tanggal_mulai ? \Carbon\Carbon::parse($permit->tanggal_mulai)->format('d/m/Y') : '......................' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jam : {{ $permit->tanggal_mulai ? \Carbon\Carbon::parse($permit->tanggal_mulai)->format('H:i') : '......................' }}</div>
                <div style="margin-top: 3px;">Selesai Tgl : {{ $permit->tanggal_selesai ? \Carbon\Carbon::parse($permit->tanggal_selesai)->format('d/m/Y') : '......................' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jam : {{ $permit->tanggal_selesai ? \Carbon\Carbon::parse($permit->tanggal_selesai)->format('H:i') : '......................' }}</div>
            </td>
            <td width="40%" style="padding: 0; border: none;">
                <table style="border: none; width: 100%; border-left: 1px solid #000; border-right: 1px solid #000; border-top: 1px solid #000;">
                    <tr>
                        <td width="50%" class="text-center" style="background-color: #ccc; border: none; border-bottom: 1px solid #000; border-right: 1px solid #000;">Nama</td>
                        <td width="50%" class="text-center" style="background-color: #ccc; border: none; border-bottom: 1px solid #000;">Tanda Tangan</td>
                    </tr>
                    @php
                        $sigs = is_string($permit->approval_signatures) ? json_decode($permit->approval_signatures, true) : ($permit->approval_signatures ?? []);
                        $pemohon = $permit->penanggung_jawab;
                        $pemohon_sig = $permit->tanda_tangan ?? '';
                        $so = ''; $so_sig = '';
                        $sm = ''; $sm_sig = '';
                        foreach($sigs as $s) {
                            $sRole = $s['role'] ?? '';
                            if($sRole == 'Staff' || $sRole == 'Safety Officer') { $so = $s['name'] ?? ''; $so_sig = $s['signature'] ?? ''; }
                            if($sRole == 'Manager' || str_contains($sRole, 'QM & SHE') || str_contains($sRole, 'Manager')) { $sm = $s['name'] ?? ''; $sm_sig = $s['signature'] ?? ''; }
                        }
                    @endphp
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 0;">
                            <div style="border-bottom: 1px solid #000; font-size: 7px; padding: 1px 2px;">Pemohon</div>
                            <div class="text-center" style="padding: 5px 2px 2px;">{{ $pemohon }}</div>
                        </td>
                        <td class="text-center sig-cell" style="border: none; border-bottom: 1px solid #000; height: 32px; vertical-align: middle;">
                            @if($pemohon_sig)<img src="{{ $pemohon_sig }}" class="sig-img" style="max-height: 28px;">@endif
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 0;">
                            <div style="border-bottom: 1px solid #000; font-size: 7px; padding: 1px 2px;">Safety Officer</div>
                            <div class="text-center" style="padding: 5px 2px 2px;">{{ $so }}</div>
                        </td>
                        <td class="text-center sig-cell" style="border: none; border-bottom: 1px solid #000; height: 32px; vertical-align: middle;">
                            @if($so_sig)<img src="{{ $so_sig }}" class="sig-img" style="max-height: 28px;">@endif
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 0;">
                            <div style="border-bottom: 1px solid #000; font-size: 7px; padding: 1px 2px;">Manager HSE</div>
                            <div class="text-center" style="padding: 5px 2px 2px;">Dimas Ali Akbar</div>
                        </td>
                        <td class="text-center sig-cell" style="border: none; border-bottom: 1px solid #000; height: 32px; vertical-align: middle;">
                            @if($sm_sig)<img src="{{ $sm_sig }}" class="sig-img" style="max-height: 28px;">@endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- G. PEMBATALAN IZIN KERJA -->
    <div class="section-header">G. PEMBATALAN IZIN KERJA</div>
    <table style="margin-bottom: 4px; table-layout: fixed;">
        <tr>
            <td width="60%" style="border-right: none; padding-right: 0;">
                <div>Izin kerja dibatalkan</div>
                <div style="margin-top: 6px;">Alasan &nbsp;: {{ $permit->cancellation_reason ?: '..................................' }}</div>
                <div style="margin-top: 6px;">Tanggal &nbsp;: {{ $permit->cancelled_at ? \Carbon\Carbon::parse($permit->cancelled_at)->format('d/m/Y') : '..................................' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jam : {{ $permit->cancelled_at ? \Carbon\Carbon::parse($permit->cancelled_at)->format('H:i') : '......................' }}</div>
            </td>
            <td width="40%" style="padding: 0; border: none;">
                <table style="border: none; width: 100%; border-left: 1px solid #000; border-right: 1px solid #000; border-top: 1px solid #000;">
                    <tr>
                        <td width="50%" class="text-center" style="background-color: #ccc; border: none; border-bottom: 1px solid #000; border-right: 1px solid #000;">Nama</td>
                        <td width="50%" class="text-center" style="background-color: #ccc; border: none; border-bottom: 1px solid #000;">Tanda Tangan</td>
                    </tr>
                    @php
                        $csigs = is_string($permit->cancellation_signatures) ? json_decode($permit->cancellation_signatures, true) : ($permit->cancellation_signatures ?? []);
                    @endphp
                    @forelse($csigs as $s)
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 0;">
                            <div style="border-bottom: 1px solid #000; font-size: 7px; padding: 1px 2px;">{{ $s['role'] ?? '-' }}</div>
                            <div class="text-center" style="padding: 5px 2px 2px;">{{ $s['name'] ?? '-' }}</div>
                        </td>
                        <td class="text-center sig-cell" style="border: none; border-bottom: 1px solid #000; height: 32px; vertical-align: middle;">
                            @if(!empty($s['signature']))<img src="{{ $s['signature'] }}" class="sig-img" style="max-height: 28px;">@endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 0;">
                            <div style="border-bottom: 1px solid #000; font-size: 7px; padding: 1px 2px;">&nbsp;</div>
                            <div class="text-center" style="padding: 5px 2px 2px;">&nbsp;</div>
                        </td>
                        <td class="text-center sig-cell" style="border: none; border-bottom: 1px solid #000; height: 32px; vertical-align: middle;">&nbsp;</td>
                    </tr>
                    @endforelse
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer Notes -->
     <div style="margin-top: 5px; font-weight: bold;">Form K3LH : IV-01.012 Rev. A</div>

</body>
</html>
