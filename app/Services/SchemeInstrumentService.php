<?php

namespace App\Services;

use App\Models\Scheme;
use Illuminate\Support\Facades\File;

class SchemeInstrumentService
{
    /**
     * Cache for loaded scheme json data.
     */
    protected static array $schemeData = [];

    /**
     * Clear cached scheme data (in-memory & application cache).
     */
    public static function clearSchemeCache(?string $schemeCode = null): void
    {
        if ($schemeCode) {
            unset(self::$schemeData[$schemeCode]);
            cache()->forget("scheme_json_data_{$schemeCode}");
        } else {
            self::$schemeData = [];
            // When clearing all, flush keys or invalidate in-memory
        }
    }

    /**
     * Load raw scheme data from JSON fixture (in-memory + framework cache).
     */
    public function getSchemeData(Scheme $scheme): array
    {
        if (isset(self::$schemeData[$scheme->code])) {
            return self::$schemeData[$scheme->code];
        }

        return self::$schemeData[$scheme->code] = cache()->remember(
            "scheme_json_data_{$scheme->code}",
            3600,
            function () use ($scheme) {
                $filePath = database_path("seeders/data/{$scheme->code}.json");
                if (File::exists($filePath)) {
                    return json_decode(File::get($filePath), true) ?: [];
                }

                return [];
            }
        );
    }

    /**
     * Get units for a scheme from scheme reference JSON or DB.
     */
    public function getUnitsForScheme(Scheme $scheme): array
    {
        $data = $this->getSchemeData($scheme);
        if (! empty($data['units'])) {
            return $data['units'];
        }

        // Fallback: check CompetencyUnit or CertificationScheme
        return \App\Models\CompetencyUnit::where('scheme_id', $scheme->id)
            ->get()
            ->map(fn ($u) => [
                'code' => $u->code ?? $u->unit_code,
                'title' => $u->title ?? $u->unit_name,
            ])
            ->toArray();
    }

    /**
     * Get default MAPA-01 work groups for a scheme based on its specific units.
     */
    public function getMapa01Groups(Scheme $scheme): array
    {
        $units = $this->getUnitsForScheme($scheme);
        if (empty($units)) {
            return [];
        }

        $groupName = match ($scheme->code) {
            'SKM-01-2026' => 'General Service & Engine',
            'SKM-02-2026' => 'Gasoline Engine Injection',
            'SKM-03-2026' => 'Periodical Maintenance EV',
            'SKM-04-2026' => 'Perbaikan Panel Bodi',
            'SKM-05-2026' => 'Mekanik Junior',
            'SKM-06-2026' => 'Sepeda Motor Listrik',
            'SKM-07-2026' => 'Sepeda Motor Injeksi',
            default => 'Kelompok Inti ' . $scheme->name,
        };

        return [
            [
                'name' => $groupName,
                'units' => array_map(fn ($u) => [
                    'code' => $u['code'],
                    'title' => $u['title'],
                    'evidence_text' => 'Hasil observasi langsung dan tanya jawab tentang ' . $u['title'],
                    'evidence_types' => ['L', 'T'],
                    'methods' => ['CL', 'DPT'],
                ], $units),
            ],
        ];
    }

    /**
     * Get FR.IA.02 (Tugas Praktik Demonstrasi - TPD) data for a scheme.
     */
    public function getTpdData(Scheme $scheme): array
    {
        $data = $this->getSchemeData($scheme);
        $tpd = $data['tpd'] ?? [];

        $defaultScenario = 'Anda adalah seorang teknisi otomotif profesional yang ditugaskan untuk melakukan pemeriksaan, pemeliharaan berkala, dan perbaikan pada unit kendaraan uji di Tempat Uji Kompetensi (TUK). Lakukan seluruh tahapan kerja dengan menerapkan K3, SOP pabrikan, dan catat hasil pekerjaan pada formulir yang disediakan.';

        return [
            'scenario' => ! empty($tpd['scenario']) ? $tpd['scenario'] : $defaultScenario,
            'time_limit' => $tpd['time_limit'] ?? '30 Menit',
            'tasks' => ! empty($tpd['tasks']) ? $tpd['tasks'] : [
                ['id' => '1', 'instruction' => 'Gunakan Alat Pelindung Diri (APD) dan persiapkan tempat kerja sesuai standar K3.'],
                ['id' => '2', 'instruction' => 'Siapkan peralatan tangan (hand tools), special service tools (SST), dan alat ukur sesuai spesifikasi.'],
                ['id' => '3', 'instruction' => 'Laksanakan prosedur diagnosis, pembongkaran, pengukuran, dan perbaikan komponen sesuai manual servis.'],
                ['id' => '4', 'instruction' => 'Catat seluruh hasil pengukuran pada Lembar Laporan Pemeriksaan (Report Sheet / FR.IA.01B).'],
                ['id' => '5', 'instruction' => 'Lakukan pembersihan area kerja (housekeeping) dan kembalikan peralatan ke tempat penyimpanan.'],
            ],
            'equipment' => [
                'Alat Pelindung Diri (Wearpack, Safety Shoes, Kacamata Pelindung, Sarung Tangan)',
                'Tool set mekanik lengkap (Kunci Ring, Pas, Soket, Kunci Momen)',
                'Alat ukur presisi terkalibrasi (Vernier Caliper, Micrometer, Multitester / AVO, Dial Gauge)',
                'Kendaraan unit uji / Trainer simulator kerja',
                'Buku Manual Reparasi (Workshop Manual / Manual Service)',
            ],
        ];
    }

    /**
     * Get FR.IA.03 (Pertanyaan Mendukung Observasi - PMO) questions for a scheme.
     */
    public function getPmoQuestions(Scheme $scheme): array
    {
        $data = $this->getSchemeData($scheme);
        $pmo = $data['pmo'] ?? [];

        if (! empty($pmo)) {
            return $pmo;
        }

        return [
            [
                'number' => '1',
                'question' => 'Jelaskan prosedur penerapan K3 dan APD yang wajib diperhatikan sebelum memulai pekerjaan servis pada unit ini!',
                'expected_answer' => 'Mengidentifikasi potensi bahaya, memastikan APD lengkap terpasang, memasang pelindung kendaraan (fender cover), dan menjaga kebersihan lantai kerja.',
            ],
            [
                'number' => '2',
                'question' => 'Bagaimana prosedur kalibrasi dan pengecekan nol (zero point) pada alat ukur presisi sebelum digunakan?',
                'expected_answer' => 'Memeriksa kebersihan bidang ukur, merapatkan spindle/jaw, memastikan garis nol sejajar atau jarum tepat di angka nol, serta mengunci baut stopper jika diperlukan.',
            ],
            [
                'number' => '3',
                'question' => 'Apa tindakan yang harus Anda ambil jika menemukan komponen yang aus atau memiliki nilai ukur di luar batas toleransi servis?',
                'expected_answer' => 'Mencatat penyimpangan pada lembar kerja teknis, mengonfirmasi spesifikasi batas servis pada manual resmi, dan merekomendasikan penggantian komponen kepada konsumen/atasan.',
            ],
        ];
    }

    /**
     * Get FR.IA.01B (Report Sheet / Lembar Pemeriksaan Teknis) items for a scheme.
     */
    public function getReportSheetItems(Scheme $scheme): array
    {
        $data = $this->getSchemeData($scheme);
        $items = $data['report_sheet'] ?? [];

        if (! empty($items)) {
            return $items;
        }

        return [
            ['item' => '1. Pemeriksaan APD dan Keselamatan Kerja Bengkel', 'standard' => 'SOP K3 & Kebersihan'],
            ['item' => '2. Kelayakan dan Kalibrasi Alat Ukur / Hand Tools', 'standard' => 'Presisi & Terkalibrasi'],
            ['item' => '3. Pemeriksaan Visual dan Fisik Sistem Utama', 'standard' => 'Workshop Manual'],
            ['item' => '4. Pengukuran Parameter Teknis Komponen', 'standard' => 'Dalam Rentang Spesifikasi Servis'],
            ['item' => '5. Housekeeping dan Pembersihan Area Kerja Uji', 'standard' => '5R / Ringkas Rapi Resik'],
        ];
    }
}
