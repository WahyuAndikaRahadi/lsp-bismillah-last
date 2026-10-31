<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Permohonan Sertifikasi Diterima</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 24px; color: #18181b;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e4e4e7; overflow: hidden;">
        <div style="background-color: #0284c7; padding: 24px; color: #ffffff;">
            <h1 style="margin: 0; font-size: 20px; font-weight: 700;">LSP Sistem Sertifikasi</h1>
            <p style="margin: 4px 0 0; font-size: 13px; color: #e0f2fe;">Konfirmasi Pengajuan Permohonan Sertifikasi (FR-APL-01)</p>
        </div>
        <div style="padding: 24px;">
            <p style="margin-top: 0;">Halo <strong>{{ $application->user->name }}</strong>,</p>
            <p>Permohonan sertifikasi kompetensi Anda telah berhasil kami terima dengan rincian sebagai berikut:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #f4f4f5; color: #71717a; width: 40%;">Nomor Registrasi:</td>
                    <td style="padding: 8px; border-bottom: 1px solid #f4f4f5; font-weight: 600;">{{ $application->nomor_pendaftaran }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #f4f4f5; color: #71717a;">Skema Sertifikasi:</td>
                    <td style="padding: 8px; border-bottom: 1px solid #f4f4f5; font-weight: 600;">{{ $application->scheme->nama_skema }} ({{ $application->scheme->kode_skema }})</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #f4f4f5; color: #71717a;">Tujuan Asesmen:</td>
                    <td style="padding: 8px; border-bottom: 1px solid #f4f4f5;">{{ $application->tujuan_asesmen }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #f4f4f5; color: #71717a;">Waktu Pengajuan:</td>
                    <td style="padding: 8px; border-bottom: 1px solid #f4f4f5;">{{ $application->tgl_pengajuan?->format('d M Y H:i') ?? '-' }} WIB</td>
                </tr>
            </table>

            <p style="font-size: 13px; color: #52525b; line-height: 1.5;">
                Saat ini berkas persyaratan Anda sedang dalam tahap verifikasi oleh Tim Administrasi LSP. Kami akan memberitahukan perkembangan status permohonan melalui email ini atau silakan cek berkala di portal asesi Anda.
            </p>

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('asesi.applications.show', $application) }}" style="display: inline-block; background-color: #0284c7; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                    Pantau Status Permohonan
                </a>
            </div>
        </div>
        <div style="background-color: #fafafa; padding: 16px; text-align: center; font-size: 11px; color: #a1a1aa; border-top: 1px solid #f4f4f5;">
            Sistem Informasi Sertifikasi LSP &bull; Dilindungi Lisensi BNSP
        </div>
    </div>
</body>
</html>
