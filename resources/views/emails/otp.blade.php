<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width: 600px; width: 100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); border-radius: 16px 16px 0 0; padding: 36px 40px; text-align: center;">
                            <div style="display: inline-block; background: rgba(255,255,255,0.15); border-radius: 12px; padding: 12px 20px; margin-bottom: 16px;">
                                <span style="font-size: 28px;">🔐</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.5px;">
                                Sistem Backup & Restore OJS
                            </h1>
                            <p style="margin: 8px 0 0; color: #bfdbfe; font-size: 14px;">
                                Verifikasi Identitas Admin
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background-color: #ffffff; padding: 40px;">

                            <p style="margin: 0 0 24px; color: #374151; font-size: 15px; line-height: 1.6;">
                                Halo, Admin! 👋<br><br>
                                Kami menerima permintaan login ke <strong>Sistem Backup & Restore OJS</strong>.
                                Gunakan kode OTP di bawah ini untuk mengakses sistem:
                            </p>

                            {{-- OTP Box --}}
                            <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 2px dashed #3b82f6; border-radius: 12px; padding: 32px; text-align: center; margin: 24px 0;">
                                <p style="margin: 0 0 8px; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px;">
                                    Kode Verifikasi OTP
                                </p>
                                <div style="font-size: 42px; font-weight: 800; letter-spacing: 16px; color: #1d4ed8; font-family: 'Courier New', monospace; margin: 8px 0;">
                                    {{ $otp }}
                                </div>
                                <p style="margin: 12px 0 0; color: #ef4444; font-size: 13px; font-weight: 600;">
                                    ⏱ Berlaku selama 5 menit
                                </p>
                            </div>

                            {{-- Steps --}}
                            <p style="margin: 24px 0 12px; color: #374151; font-size: 14px; font-weight: 600;">
                                Cara menggunakan kode OTP ini:
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 8px 0; color: #4b5563; font-size: 14px;">
                                        <span style="display: inline-block; background: #dbeafe; color: #1d4ed8; border-radius: 50%; width: 22px; height: 22px; text-align: center; line-height: 22px; font-weight: 700; font-size: 12px; margin-right: 10px;">1</span>
                                        Buka halaman verifikasi OTP di browser Anda
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #4b5563; font-size: 14px;">
                                        <span style="display: inline-block; background: #dbeafe; color: #1d4ed8; border-radius: 50%; width: 22px; height: 22px; text-align: center; line-height: 22px; font-weight: 700; font-size: 12px; margin-right: 10px;">2</span>
                                        Masukkan 6 digit kode di atas ke kolom yang tersedia
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #4b5563; font-size: 14px;">
                                        <span style="display: inline-block; background: #dbeafe; color: #1d4ed8; border-radius: 50%; width: 22px; height: 22px; text-align: center; line-height: 22px; font-weight: 700; font-size: 12px; margin-right: 10px;">3</span>
                                        Klik tombol "Verifikasi" dan Anda akan masuk ke dashboard
                                    </td>
                                </tr>
                            </table>

                            {{-- Warning --}}
                            <div style="background-color: #fef9c3; border-left: 4px solid #eab308; border-radius: 0 8px 8px 0; padding: 16px; margin: 24px 0;">
                                <p style="margin: 0; color: #713f12; font-size: 13px; line-height: 1.6;">
                                    ⚠️ <strong>Peringatan Keamanan:</strong><br>
                                    Jangan pernah membagikan kode OTP ini kepada siapa pun. Tim kami tidak pernah
                                    meminta kode ini. Jika Anda tidak merasa melakukan permintaan login ini,
                                    abaikan email ini.
                                </p>
                            </div>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; border-radius: 0 0 16px 16px; padding: 24px 40px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0 0 8px; color: #6b7280; font-size: 12px;">
                                Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                © {{ date('Y') }} Sistem Backup & Restore OJS
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
