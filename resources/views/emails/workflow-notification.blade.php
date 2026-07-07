<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;background:#f4f7f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f4f7f5;padding:32px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:640px;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#198754;color:#ffffff;padding:22px 28px;">
                            <div style="font-size:14px;font-weight:700;letter-spacing:.04em;">SIPARISUB</div>
                            <h1 style="font-size:22px;line-height:1.35;margin:8px 0 0;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 18px;line-height:1.6;">{{ $message ?: 'Ada pembaruan workflow konten pada dashboard SIPARISUB.' }}</p>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #edf2f7;color:#6b7280;width:150px;">Jenis Konten</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #edf2f7;font-weight:700;">{{ $contentType }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #edf2f7;color:#6b7280;">Nama Konten</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #edf2f7;font-weight:700;">{{ $contentName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-bottom:1px solid #edf2f7;color:#6b7280;">Status Terbaru</td>
                                    <td style="padding:10px 0;border-bottom:1px solid #edf2f7;font-weight:700;">{{ $status }}</td>
                                </tr>
                            </table>

                            @if($actionUrl)
                                <p style="margin:0 0 24px;">
                                    <a href="{{ $actionUrl }}" style="display:inline-block;background:#198754;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:8px;font-weight:700;">Lihat Detail Konten</a>
                                </p>
                            @endif

                            <p style="font-size:13px;line-height:1.6;color:#6b7280;margin:0;">Email ini dikirim otomatis oleh SIPARISUB. Jika tombol tidak dapat dibuka, salin tautan berikut ke browser: {{ $actionUrl ?: '-' }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>