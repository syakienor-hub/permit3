<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permit Dibatalkan - {{ $permit->no_permit }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
                <tr>
                    <td style="background:#991b1b;padding:20px 28px;color:#ffffff;">
                        <p style="margin:0;font-size:12px;letter-spacing:1px;font-weight:bold;">SAFETY PERMIT &bull; PT INKA (Persero)</p>
                        <h1 style="margin:8px 0 0;font-size:20px;line-height:1.3;">Permit dibatalkan oleh pemohon</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 28px;color:#1f2937;">
                        <p style="margin:0 0 12px;font-size:14px;">Yth. Bapak/Ibu HSE,</p>
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">
                            Permit berikut telah <strong>dibatalkan oleh pemohon</strong> melalui halaman publik.
                            Hentikan pekerjaan terkait di lapangan dan lakukan verifikasi bila diperlukan.
                        </p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;width:180px;">No. Permit</td>
                                <td style="padding:10px 14px;font-weight:bold;color:#991b1b;">{{ $permit->no_permit }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;">Site</td>
                                <td style="padding:10px 14px;font-weight:bold;">{{ $permit->site ?? 'Madiun' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;">Nama Pekerjaan</td>
                                <td style="padding:10px 14px;">{{ $permit->nama_pekerjaan }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;">Kontraktor / Divisi</td>
                                <td style="padding:10px 14px;">{{ $permit->kontraktor }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;">Lokasi</td>
                                <td style="padding:10px 14px;">{{ $permit->lokasi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;">Pengaju</td>
                                <td style="padding:10px 14px;">{{ optional($permit->user)->name ?? ($permit->divisi_pengaju ?? 'Pengaju Publik') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;">Alasan Pembatalan</td>
                                <td style="padding:10px 14px;">{{ $permit->cancellation_reason ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;">Waktu Pembatalan</td>
                                <td style="padding:10px 14px;">{{ $permit->cancelled_at ? $permit->cancelled_at->format('d M Y H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px;background:#f8fafc;color:#64748b;">Ditandatangani Oleh</td>
                                <td style="padding:10px 14px;">
                                    @forelse($permit->cancellation_signatures ?? [] as $sig)
                                        {{ $sig['name'] ?? '-' }} ({{ $sig['role'] ?? '-' }})@if(!$loop->last)<br>@endif
                                    @empty
                                        -
                                    @endforelse
                                </td>
                            </tr>
                        </table>

                        <div style="text-align:center;margin:24px 0 8px;">
                            <a href="{{ $detailUrl }}" style="display:inline-block;background:#0f2a4a;color:#ffffff;text-decoration:none;font-weight:bold;font-size:15px;padding:13px 32px;border-radius:10px;">
                                Lihat Detail Permit
                            </a>
                        </div>

                        <p style="margin:12px 0 0;font-size:12px;color:#64748b;line-height:1.6;">
                            Jika tombol di atas tidak berfungsi, salin dan buka tautan berikut di browser Anda:<br>
                            <a href="{{ $detailUrl }}" style="color:#0f2a4a;word-break:break-all;">{{ $detailUrl }}</a>
                        </p>
                        <p style="margin:12px 0 0;font-size:12px;color:#64748b;line-height:1.6;">
                            Catatan: Anda harus login dengan akun HSE untuk membuka halaman detail.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 28px;background:#f8fafc;color:#94a3b8;font-size:11px;text-align:center;">
                        Email otomatis dari Sistem Safety Permit PT INKA (Persero). Mohon tidak membalas email ini.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
