<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Schedule;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MukExportService
{
    /**
     * Data bundle for schedule-level exports (BA, Attendance, AK05, AK06, Cover, Daftar Periksa)
     *
     * @return array<string, mixed>
     */
    public function getExportData(Schedule $schedule): array
    {
        $schedule->load([
            'scheme.competencyUnits.elements.kuks',
            'scheme.questionBanks',
            'assessors.profile',
            'assessments.asesi.profile',
            'assessments.ak02Items.competencyUnit',
            'assessments.ak03Items',
            'assessments.appeals',
        ]);

        $totalAsesi = $schedule->assessments->count();
        $totalTidakHadir = $schedule->assessments->filter(function ($a) {
            return $a->final_result === 'TIDAK_HADIR';
        })->count();
        $totalKompeten = $schedule->assessments->filter(function ($a) {
            return in_array($a->final_result, ['K', 'KOMPETEN'], true) || ($a->final_result === null && $a->status === 'COMPLETED');
        })->count();
        $totalBelumKompeten = $schedule->assessments->filter(function ($a) {
            return in_array($a->final_result, ['BK', 'BELUM_KOMPETEN'], true);
        })->count();

        return [
            'schedule' => $schedule,
            'scheme' => $schedule->scheme,
            'assessors' => $schedule->assessors,
            'leadAssessor' => $schedule->assessors->first(),
            'assessments' => $schedule->assessments,
            'total_asesi' => $totalAsesi,
            'total_asesi_terbilang' => self::terbilang($totalAsesi),
            'total_kompeten' => $totalKompeten,
            'total_kompeten_terbilang' => self::terbilang($totalKompeten),
            'total_belum_kompeten' => $totalBelumKompeten,
            'total_belum_kompeten_terbilang' => self::terbilang($totalBelumKompeten),
            'total_tidak_hadir' => $totalTidakHadir,
            'total_tidak_hadir_terbilang' => self::terbilang($totalTidakHadir),
            'questionBanks' => $schedule->scheme?->questionBanks ?? collect(),
        ];
    }

    /**
     * Data bundle for individual asesi MUK exports (APL01, APL02, AK01, AK02, AK03, AK04, AK07, Pernyataan)
     *
     * @return array<string, mixed>
     */
    public function getAsesiExportData(Assessment $assessment): array
    {
        $assessment->load([
            'schedule.scheme.competencyUnits.elements.kuks',
            'schedule.scheme.questionBanks',
            'schedule.assessors.profile',
            'asesi.profile',
            'assessor.profile',
            'proofs',
            'apl02Items.kuk.unitElement.competencyUnit',
            'iaResponses',
            'ak07Items',
            'ak02Items.competencyUnit',
            'ak03Items',
            'appeals',
        ]);

        return [
            'assessment' => $assessment,
            'schedule' => $assessment->schedule,
            'scheme' => $assessment->schedule?->scheme ?? $assessment->scheme,
            'asesi' => $assessment->asesi,
            'profile' => $assessment->asesi->profile,
            'assessor' => $assessment->assessor ?? $assessment->schedule?->assessors->first(),
            'ak02Items' => $assessment->ak02Items,
            'ak03Items' => $assessment->ak03Items,
            'appeals' => $assessment->appeals,
            'iaResponses' => $assessment->iaResponses,
            'questionBanks' => $assessment->schedule?->scheme?->questionBanks ?? collect(),
        ];
    }

    /**
     * Convert integer to Indonesian word representation (e.g. 10 -> sepuluh).
     */
    public static function terbilang(int $number): string
    {
        if ($number === 0) {
            return 'nol';
        }

        $words = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($number < 12) {
            return $words[$number];
        }
        if ($number < 20) {
            return self::terbilang($number - 10).' belas';
        }
        if ($number < 100) {
            $remainder = $number % 10;

            return self::terbilang((int) ($number / 10)).' puluh'.($remainder ? ' '.self::terbilang($remainder) : '');
        }
        if ($number < 200) {
            $remainder = $number - 100;

            return 'seratus'.($remainder ? ' '.self::terbilang($remainder) : '');
        }
        if ($number < 1000) {
            $remainder = $number % 100;

            return self::terbilang((int) ($number / 100)).' ratus'.($remainder ? ' '.self::terbilang($remainder) : '');
        }

        return (string) $number;
    }

    /**
     * Generate native Excel (.xlsx) file containing complete Biodata Asesi using PhpSpreadsheet.
     */
    public function exportExcelBiodata(Schedule $schedule): StreamedResponse
    {
        $data = $this->getExportData($schedule);
        $assessments = $data['assessments'];
        $scheme = $data['scheme'];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Biodata Asesi');

        // Page Title
        $sheet->setCellValue('A1', 'DAFTAR BIODATA ASESI / PESERTA UJI KOMPETENSI');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('1E2B6E'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'LSP PrO INDO - '.($scheme->name ?? '-'));
        $sheet->mergeCells('A2:N2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Schedule Metadata
        $sheet->setCellValue('A4', 'Kode Jadwal');
        $sheet->setCellValue('C4', ': '.$schedule->code);
        $sheet->setCellValue('A5', 'Tempat Uji Kompetensi (TUK)');
        $sheet->setCellValue('C5', ': '.$schedule->tuk_name);
        $sheet->setCellValue('A6', 'Tanggal Pelaksanaan');
        $sheet->setCellValue('C6', ': '.Carbon::parse($schedule->date)->translatedFormat('d F Y'));

        $sheet->getStyle('A4:A6')->getFont()->setBold(true);

        // Table Headers
        $headers = [
            'A8' => 'NO',
            'B8' => 'NIK',
            'C8' => 'NAMA LENGKAP',
            'D8' => 'TEMPAT LAHIR',
            'E8' => 'TGL LAHIR',
            'F8' => 'JK',
            'G8' => 'PENDIDIKAN',
            'H8' => 'JABATAN / PEKERJAAN',
            'I8' => 'INSTITUSI / PERUSAHAAN',
            'J8' => 'ALAMAT LENGKAP',
            'K8' => 'NO HP / WA',
            'L8' => 'EMAIL',
            'M8' => 'ASESOR PENGUJI',
            'N8' => 'HASIL ASESMEN',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E2B6E'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A8:N8')->applyFromArray($headerStyle);
        $sheet->getRowDimension(8)->setRowHeight(25);

        // Data Rows
        $row = 9;
        $no = 1;
        foreach ($assessments as $item) {
            $asesi = $item->asesi;
            $profile = $asesi->profile;

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValueExplicit("B{$row}", $profile->nik ?? $asesi->nik ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $asesi->full_name);
            $sheet->setCellValue("D{$row}", $profile->tempat_lahir ?? '-');
            $sheet->setCellValue("E{$row}", $profile->tanggal_lahir ? Carbon::parse($profile->tanggal_lahir)->format('d/m/Y') : '-');
            $sheet->setCellValue("F{$row}", $profile->jenis_kelamin ?? '-');
            $sheet->setCellValue("G{$row}", $profile->pendidikan_terakhir ?? '-');
            $sheet->setCellValue("H{$row}", $profile->jabatan ?? '-');
            $sheet->setCellValue("I{$row}", $profile->nama_institusi_perusahaan ?? '-');
            $sheet->setCellValue("J{$row}", $profile->alamat_rumah ?? '-');
            $sheet->setCellValueExplicit("K{$row}", $asesi->phone ?? $profile->no_telepon_rumah ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("L{$row}", $asesi->email);
            $sheet->setCellValue("M{$row}", $item->assessor->full_name ?? '-');
            $sheet->setCellValue("N{$row}", $item->final_result ?? 'BELUM DIASES');

            // Alignment
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A{$row}:N{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Biodata_Asesi_'.$schedule->code.'_'.date('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Generate native Excel (.xlsx) file containing Daftar Hadir Peserta Uji Kompetensi.
     */
    public function exportExcelDaftarHadirAsesi(Schedule $schedule): StreamedResponse
    {
        $data = $this->getExportData($schedule);
        $assessments = $data['assessments'];
        $scheme = $data['scheme'];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Hadir Asesi');

        // Header
        $sheet->setCellValue('A1', 'DAFTAR HADIR PESERTA UJI KOMPETENSI');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('1E2B6E'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'LSP PrO INDO - '.($scheme->name ?? '-'));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Metadata
        $sheet->setCellValue('A4', 'Kode Jadwal');
        $sheet->setCellValue('C4', ': '.$schedule->code);
        $sheet->setCellValue('A5', 'Tempat Uji Kompetensi');
        $sheet->setCellValue('C5', ': '.$schedule->tuk_name);
        $sheet->setCellValue('A6', 'Waktu Pelaksanaan');
        $sheet->setCellValue('C6', ': '.Carbon::parse($schedule->date)->translatedFormat('d F Y').' ('.$schedule->start_time.' - '.$schedule->end_time.')');

        $sheet->getStyle('A4:A6')->getFont()->setBold(true);

        // Columns
        $headers = [
            'A8' => 'NO',
            'B8' => 'NIK / NO. REGISTRASI',
            'C8' => 'NAMA LENGKAP ASESI',
            'D8' => 'INSTITUSI / SEKOLAH / PERUSAHAAN',
            'E8' => 'STATUS TANDA TANGAN',
            'F8' => 'KEPUTUSAN AKHIR',
            'G8' => 'KETERANGAN KEHADIRAN',
        ];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E2B6E'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A8:G8')->applyFromArray($headerStyle);
        $sheet->getRowDimension(8)->setRowHeight(25);

        $row = 9;
        $no = 1;
        foreach ($assessments as $item) {
            $profile = $item->asesi->profile;
            $isHadir = $item->final_result !== 'TIDAK_HADIR';
            $hasSig = ! empty($profile->signature_data) || ! empty($item->asesi_signature);

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValueExplicit("B{$row}", $profile->nik ?? $item->asesi->nik ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $item->asesi->full_name);
            $sheet->setCellValue("D{$row}", $profile->nama_institusi_perusahaan ?? '-');
            $sheet->setCellValue("E{$row}", $hasSig ? 'TTD Digital Terverifikasi' : 'Belum TTD');
            $sheet->setCellValue("F{$row}", $item->final_result ?? 'BELUM SELESAI');
            $sheet->setCellValue("G{$row}", $isHadir ? 'Hadir' : 'Tidak Hadir');

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A{$row}:G{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Daftar_Hadir_Asesi_'.$schedule->code.'_'.date('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Generate native Excel (.xlsx) file containing Daftar Hadir Asesor Penguji.
     */
    public function exportExcelDaftarHadirAsesor(Schedule $schedule): StreamedResponse
    {
        $data = $this->getExportData($schedule);
        $assessors = $data['assessors'];
        $scheme = $data['scheme'];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Hadir Asesor');

        // Header
        $sheet->setCellValue('A1', 'DAFTAR HADIR ASESOR PENGUJI KOMPETENSI');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('1E2B6E'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'LSP PrO INDO - '.($scheme->name ?? '-'));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Metadata
        $sheet->setCellValue('A4', 'Kode Jadwal');
        $sheet->setCellValue('C4', ': '.$schedule->code);
        $sheet->setCellValue('A5', 'Tempat Uji Kompetensi');
        $sheet->setCellValue('C5', ': '.$schedule->tuk_name);
        $sheet->setCellValue('A6', 'Waktu Pelaksanaan');
        $sheet->setCellValue('C6', ': '.Carbon::parse($schedule->date)->translatedFormat('d F Y').' ('.$schedule->start_time.' - '.$schedule->end_time.')');

        $sheet->getStyle('A4:A6')->getFont()->setBold(true);

        // Columns
        $headers = [
            'A8' => 'NO',
            'B8' => 'NO REGISTRASI ASESOR (MET)',
            'C8' => 'NAMA LENGKAP ASESOR',
            'D8' => 'PERAN PENUGASAN',
            'E8' => 'STATUS TANDA TANGAN',
            'F8' => 'KETERANGAN KEHADIRAN',
        ];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E2B6E'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A8:F8')->applyFromArray($headerStyle);
        $sheet->getRowDimension(8)->setRowHeight(25);

        $row = 9;
        $no = 1;
        foreach ($assessors as $index => $asesor) {
            $profile = $asesor->profile;
            $hasSig = ! empty($profile->signature_data);
            $roleLabel = ($index === 0) ? 'Lead Assessor (Ketua)' : 'Anggota Asesor';

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValueExplicit("B{$row}", $profile->met_number ?? $asesor->assessor_registration_number ?? 'MET.000.00'.$asesor->id, DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $asesor->full_name);
            $sheet->setCellValue("D{$row}", $roleLabel);
            $sheet->setCellValue("E{$row}", $hasSig ? 'TTD Digital Terverifikasi' : 'Belum TTD');
            $sheet->setCellValue("F{$row}", 'Hadir Bertugas');

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A{$row}:F{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Daftar_Hadir_Asesor_'.$schedule->code.'_'.date('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
