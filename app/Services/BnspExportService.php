<?php

namespace App\Services;

use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BnspExportService
{
    /**
     * Header 16 Kolom Standar Sisfo BNSP (Sesuai Bagian 3.9 Dokumen Acuan)
     */
    public const HEADERS = [
        'NO',
        'NIK',
        'NO_PESERTA',
        'NAMA_LENGKAP',
        'TEMPAT_LAHIR',
        'TANGGAL_LAHIR',
        'JENIS_KELAMIN',
        'PENDIDIKAN_TERAKHIR',
        'KODE_SKEMA',
        'NAMA_SKEMA',
        'KODE_TUK',
        'NAMA_TUK',
        'NO_REG_ASESOR',
        'NAMA_ASESOR',
        'HASIL_ASESMEN',
        'TANGGAL_ASESMEN',
    ];

    /**
     * Konversi daftar permohonan asesmen ke baris data 16-kolom BNSP
     */
    public function formatRows(Collection|array $applications): array
    {
        $rows = [];
        $no = 1;

        foreach ($applications as $app) {
            $user = $app->user;
            $profile = $user?->asesiProfile;
            $schedule = $app->schedule;
            $scheme = $app->scheme ?? $schedule?->scheme;
            $tuk = $schedule?->tuk;
            $asesor = $app->asesor;
            $asesorProfile = $asesor?->asesorProfile;
            $ak02 = $app->ak02;

            // Hasil asesmen: K atau BK (prioritas dari kelulusan pleno atau ak02)
            $hasil = 'BK';
            if ($app->status_kelulusan === 'kompeten' || ($ak02 && $ak02->rekomendasi_akhir === 'K')) {
                $hasil = 'K';
            }

            $tglAsesmen = $schedule?->tgl_asesmen 
                ? \Carbon\Carbon::parse($schedule->tgl_asesmen)->format('Y-m-d')
                : now()->format('Y-m-d');

            $tglLahir = $profile?->tanggal_lahir
                ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('Y-m-d')
                : '';

            $rows[] = [
                'NO' => $no++,
                'NIK' => (string)($profile?->nik ?? ''),
                'NO_PESERTA' => (string)($app->nomor_permohonan ?? $app->nomor_pendaftaran ?? ''),
                'NAMA_LENGKAP' => (string)($profile?->nama_lengkap ?? $user?->name ?? ''),
                'TEMPAT_LAHIR' => (string)($profile?->tempat_lahir ?? ''),
                'TANGGAL_LAHIR' => $tglLahir,
                'JENIS_KELAMIN' => (string)($profile?->jenis_kelamin ?? 'L'),
                'PENDIDIKAN_TERAKHIR' => (string)($profile?->pendidikan_terakhir ?? '-'),
                'KODE_SKEMA' => (string)($scheme?->kode_skema ?? ''),
                'NAMA_SKEMA' => (string)($scheme?->nama_skema ?? ''),
                'KODE_TUK' => (string)($tuk?->kode_tuk ?? ''),
                'NAMA_TUK' => (string)($tuk?->nama_tuk ?? ''),
                'NO_REG_ASESOR' => (string)($asesorProfile?->no_met ?? '-'),
                'NAMA_ASESOR' => (string)($asesor?->name ?? '-'),
                'HASIL_ASESMEN' => $hasil,
                'TANGGAL_ASESMEN' => $tglAsesmen,
            ];
        }

        return $rows;
    }

    /**
     * Ekspor sebagai Excel XML Spreadsheet (.xls) resmi Sisfo BNSP
     */
    public function exportXmlSpreadsheet(Collection|array $applications, string $filename = 'exporthasilasesmen_bnsp.xls'): StreamedResponse
    {
        $rows = $this->formatRows($applications);

        return response()->streamDownload(function () use ($rows) {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
            echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
            echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
            echo '<Styles>' . "\n";
            echo ' <Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Center"/><Font ss:FontName="Calibri" ss:Size="11"/></Style>' . "\n";
            echo ' <Style ss:ID="Header"><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/><Interior ss:Color="#E2EFDA" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/></Borders></Style>' . "\n";
            echo ' <Style ss:ID="TextFormat"><NumberFormat ss:Format="@"/></Style>' . "\n";
            echo '</Styles>' . "\n";
            echo '<Worksheet ss:Name="Hasil Asesmen BNSP">' . "\n";
            echo '<Table>' . "\n";

            // Headers
            echo '<Row ss:StyleID="Header">' . "\n";
            foreach (self::HEADERS as $h) {
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>' . "\n";
            }
            echo '</Row>' . "\n";

            // Data Rows
            foreach ($rows as $r) {
                echo '<Row>' . "\n";
                echo '<Cell><Data ss:Type="Number">' . $r['NO'] . '</Data></Cell>' . "\n";
                echo '<Cell ss:StyleID="TextFormat"><Data ss:Type="String">' . htmlspecialchars($r['NIK']) . '</Data></Cell>' . "\n";
                echo '<Cell ss:StyleID="TextFormat"><Data ss:Type="String">' . htmlspecialchars($r['NO_PESERTA']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['NAMA_LENGKAP']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['TEMPAT_LAHIR']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['TANGGAL_LAHIR']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['JENIS_KELAMIN']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['PENDIDIKAN_TERAKHIR']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['KODE_SKEMA']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['NAMA_SKEMA']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['KODE_TUK']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['NAMA_TUK']) . '</Data></Cell>' . "\n";
                echo '<Cell ss:StyleID="TextFormat"><Data ss:Type="String">' . htmlspecialchars($r['NO_REG_ASESOR']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['NAMA_ASESOR']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['HASIL_ASESMEN']) . '</Data></Cell>' . "\n";
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($r['TANGGAL_ASESMEN']) . '</Data></Cell>' . "\n";
                echo '</Row>' . "\n";
            }

            echo '</Table>' . "\n";
            echo '</Worksheet>' . "\n";
            echo '</Workbook>' . "\n";
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
