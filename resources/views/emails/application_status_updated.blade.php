<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembaruan Status Permohonan Sertifikasi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 24px; color: #18181b;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e4e4e7; overflow: hidden;">
        <div style="background-color: {{ $application->status->value === 'diterima' ? '#059669' : ($application->status->value === 'revisi' ? '#ea580c' : '#dc2626') }}; padding: 24px; color: #ffffff;">
            <h1 style="margin: 0; font-size: 20px; font-weight: 700;">LSP Sistem Sertifikasi</h1>
            <p style="margin: 4px 0 0; font-size: 13px; color: #f4f4f5;">Status: {{ $application->status->label() }}</p>
        </div>
        <div style="padding: 24px;">
            <p style="margin-top: 0;">Halo <strong>{{ $application->user->name }}</strong>,</p>
            <p>
                Status permohonan sertifikasi kompetensi Anda (<strong>{{ $application->nomor_pendaftaran }}</strong>) pada skema <strong>{{ $application->scheme->nama_skema }}</strong> telah diperbarui:
            </p>

            <div style="background-color: #f4f4f5; border-left: 4px solid {{ $application->status->value === 'diterima' ? '#059669' : ($application->status->value === 'revisi' ? '#ea580c' : '#dc2626') }}; padding: 14px 16px; margin: 16px 0; border-radius: 4px;">
                <div style="font-size: 14px; font-weight: 700; color: #18181b;">
                    Status: {{ $application->status->label() }}
                </div>
                @if($application->status->value === 'revisi')
                    <p style="margin: 8px 0 0; font-size: 13px; color: #9a3412;">
                        <strong>Catatan Perbaikan:</strong><br>
                        {{ $application->catatan_revisi ?: 'Mohon periksa kembali dokumen persyaratan yang ditandai tidak memenuhi.' }}
                    </p>
                @elseif($application->status->value === 'diterima')
                    <p style="margin: 8px 0 0; font-size: 13px; color: #065f46;">
                        Selamat! Seluruh berkas persyaratan Anda telah diverifikasi dan memenuhi ketentuan. 
                        @if($application->schedule)
                            Anda telah terdaftar pada jadwal asesmen: <strong>{{ $application->schedule->nama_jadwal }}</strong> (Tgl: {{ $application->schedule->tgl_asesmen->format('d M Y') }} di {{ $application->schedule->tuk->nama_tuk }}).
                        @else
                            Admin akan segera memplot Anda ke jadwal asesmen terdekat.
                        @endif
                    </p>
                @elseif($application->status->value === 'ditolak')
                    <p style="margin: 8px 0 0; font-size: 13px; color: #991b1b;">
                        <strong>Alasan Penolakan:</strong><br>
                        {{ $application->catatan_admin ?: 'Permohonan tidak dapat diproses lebih lanjut.' }}
                    </p>
                @endif
            </div>

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('asesi.applications.show', $application) }}" style="display: inline-block; background-color: #18181b; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                    Buka Rincian Permohonan
                </a>
            </div>
        </div>
        <div style="background-color: #fafafa; padding: 16px; text-align: center; font-size: 11px; color: #a1a1aa; border-top: 1px solid #f4f4f5;">
            Sistem Informasi Sertifikasi LSP &bull; Notifikasi Otomatis
        </div>
    </div>
</body>
</html>
