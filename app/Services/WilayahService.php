<?php

namespace App\Services;

class WilayahService
{
    /**
     * Daftar 38 Provinsi di Indonesia (Standar BPS / Kemendagri)
     */
    public function getProvinces(): array
    {
        return [
            ['id' => '11', 'nama' => 'ACEH'],
            ['id' => '12', 'nama' => 'SUMATERA UTARA'],
            ['id' => '13', 'nama' => 'SUMATERA BARAT'],
            ['id' => '14', 'nama' => 'RIAU'],
            ['id' => '15', 'nama' => 'JAMBI'],
            ['id' => '16', 'nama' => 'SUMATERA SELATAN'],
            ['id' => '17', 'nama' => 'BENGKULU'],
            ['id' => '18', 'nama' => 'LAMPUNG'],
            ['id' => '19', 'nama' => 'KEPULAUAN BANGKA BELITUNG'],
            ['id' => '21', 'nama' => 'KEPULAUAN RIAU'],
            ['id' => '31', 'nama' => 'DKI JAKARTA'],
            ['id' => '32', 'nama' => 'JAWA BARAT'],
            ['id' => '33', 'nama' => 'JAWA TENGAH'],
            ['id' => '34', 'nama' => 'DI YOGYAKARTA'],
            ['id' => '35', 'nama' => 'JAWA TIMUR'],
            ['id' => '36', 'nama' => 'BANTEN'],
            ['id' => '51', 'nama' => 'BALI'],
            ['id' => '52', 'nama' => 'NUSA TENGGARA BARAT'],
            ['id' => '53', 'nama' => 'NUSA TENGGARA TIMUR'],
            ['id' => '61', 'nama' => 'KALIMANTAN BARAT'],
            ['id' => '62', 'nama' => 'KALIMANTAN TENGAH'],
            ['id' => '63', 'nama' => 'KALIMANTAN SELATAN'],
            ['id' => '64', 'nama' => 'KALIMANTAN TIMUR'],
            ['id' => '65', 'nama' => 'KALIMANTAN UTARA'],
            ['id' => '71', 'nama' => 'SULAWESI UTARA'],
            ['id' => '72', 'nama' => 'SULAWESI TENGAH'],
            ['id' => '73', 'nama' => 'SULAWESI SELATAN'],
            ['id' => '74', 'nama' => 'SULAWESI TENGGARA'],
            ['id' => '75', 'nama' => 'GORONTALO'],
            ['id' => '76', 'nama' => 'SULAWESI BARAT'],
            ['id' => '81', 'nama' => 'MALUKU'],
            ['id' => '82', 'nama' => 'MALUKU UTARA'],
            ['id' => '91', 'nama' => 'PAPUA BARAT'],
            ['id' => '92', 'nama' => 'PAPUA'],
            ['id' => '93', 'nama' => 'PAPUA SELATAN'],
            ['id' => '94', 'nama' => 'PAPUA TENGAH'],
            ['id' => '95', 'nama' => 'PAPUA PEGUNUNGAN'],
            ['id' => '96', 'nama' => 'PAPUA BARAT DAYA'],
        ];
    }

    /**
     * Daftar Kabupaten / Kota berdasarkan Kode Provinsi
     */
    public function getRegencies(string $provinsiId): array
    {
        $all = [
            '31' => [ // DKI Jakarta
                ['id' => '3171', 'nama' => 'KOTA JAKARTA SELATAN'],
                ['id' => '3172', 'nama' => 'KOTA JAKARTA TIMUR'],
                ['id' => '3173', 'nama' => 'KOTA JAKARTA PUSAT'],
                ['id' => '3174', 'nama' => 'KOTA JAKARTA BARAT'],
                ['id' => '3175', 'nama' => 'KOTA JAKARTA UTARA'],
                ['id' => '3101', 'nama' => 'KABUPATEN KEPULAUAN SERIBU'],
            ],
            '32' => [ // Jawa Barat
                ['id' => '3273', 'nama' => 'KOTA BANDUNG'],
                ['id' => '3275', 'nama' => 'KOTA BEKASI'],
                ['id' => '3276', 'nama' => 'KOTA DEPOK'],
                ['id' => '3271', 'nama' => 'KOTA BOGOR'],
                ['id' => '3277', 'nama' => 'KOTA CIMAHI'],
                ['id' => '3204', 'nama' => 'KABUPATEN BANDUNG'],
                ['id' => '3217', 'nama' => 'KABUPATEN BANDUNG BARAT'],
                ['id' => '3216', 'nama' => 'KABUPATEN BEKASI'],
                ['id' => '3201', 'nama' => 'KABUPATEN BOGOR'],
            ],
            '33' => [ // Jawa Tengah
                ['id' => '3374', 'nama' => 'KOTA SEMARANG'],
                ['id' => '3372', 'nama' => 'KOTA SURAKARTA (SOLO)'],
                ['id' => '3371', 'nama' => 'KOTA MAGELANG'],
                ['id' => '3302', 'nama' => 'KABUPATEN BANYUMAS'],
                ['id' => '3322', 'nama' => 'KABUPATEN SEMARANG'],
                ['id' => '3310', 'nama' => 'KABUPATEN KLATEN'],
            ],
            '34' => [ // DI Yogyakarta
                ['id' => '3471', 'nama' => 'KOTA YOGYAKARTA'],
                ['id' => '3404', 'nama' => 'KABUPATEN SLEMAN'],
                ['id' => '3402', 'nama' => 'KABUPATEN BANTUL'],
                ['id' => '3401', 'nama' => 'KABUPATEN KULON PROGO'],
                ['id' => '3403', 'nama' => 'KABUPATEN GUNUNGKIDUL'],
            ],
            '35' => [ // Jawa Timur
                ['id' => '3578', 'nama' => 'KOTA SURABAYA'],
                ['id' => '3573', 'nama' => 'KOTA MALANG'],
                ['id' => '3515', 'nama' => 'KABUPATEN SIDOARJO'],
                ['id' => '3525', 'nama' => 'KABUPATEN GRESIK'],
                ['id' => '3507', 'nama' => 'KABUPATEN MALANG'],
            ],
            '36' => [ // Banten
                ['id' => '3674', 'nama' => 'KOTA TANGERANG SELATAN'],
                ['id' => '3671', 'nama' => 'KOTA TANGERANG'],
                ['id' => '3673', 'nama' => 'KOTA SERANG'],
                ['id' => '3672', 'nama' => 'KOTA CILEGON'],
                ['id' => '3603', 'nama' => 'KABUPATEN TANGERANG'],
                ['id' => '3604', 'nama' => 'KABUPATEN SERANG'],
            ],
            '51' => [ // Bali
                ['id' => '5171', 'nama' => 'KOTA DENPASAR'],
                ['id' => '5103', 'nama' => 'KABUPATEN BADUNG'],
                ['id' => '5104', 'nama' => 'KABUPATEN GIANYAR'],
                ['id' => '5102', 'nama' => 'KABUPATEN TABANAN'],
                ['id' => '5108', 'nama' => 'KABUPATEN BULELENG'],
            ],
            '12' => [ // Sumatera Utara
                ['id' => '1271', 'nama' => 'KOTA MEDAN'],
                ['id' => '1272', 'nama' => 'KOTA PEMATANGSIANTAR'],
                ['id' => '1275', 'nama' => 'KOTA BINJAI'],
                ['id' => '1207', 'nama' => 'KABUPATEN DELI SERDANG'],
            ],
            '73' => [ // Sulawesi Selatan
                ['id' => '7371', 'nama' => 'KOTA MAKASSAR'],
                ['id' => '7372', 'nama' => 'KOTA PAREPARE'],
                ['id' => '7306', 'nama' => 'KABUPATEN GOWA'],
                ['id' => '7309', 'nama' => 'KABUPATEN MAROS'],
            ],
            '64' => [ // Kalimantan Timur
                ['id' => '6471', 'nama' => 'KOTA BALIKPAPAN'],
                ['id' => '6472', 'nama' => 'KOTA SAMARINDA'],
                ['id' => '6402', 'nama' => 'KABUPATEN KUTAI KARTANEGARA'],
            ],
        ];

        if (isset($all[$provinsiId])) {
            return $all[$provinsiId];
        }

        // Fallback untuk provinsi lainnya
        $province = collect($this->getProvinces())->firstWhere('id', $provinsiId);
        $namaProv = $province ? $province['nama'] : 'PROVINSI ' . $provinsiId;

        return [
            ['id' => $provinsiId . '71', 'nama' => 'KOTA UTAMA ' . $namaProv],
            ['id' => $provinsiId . '01', 'nama' => 'KABUPATEN WILAYAH ' . $namaProv],
        ];
    }

    /**
     * Daftar Kecamatan berdasarkan Kode Kota/Kabupaten
     */
    public function getDistricts(string $regencyId): array
    {
        $all = [
            // Jakarta Selatan
            '3171' => [
                ['id' => '317101', 'nama' => 'TEBET'],
                ['id' => '317102', 'nama' => 'SETIABUDI'],
                ['id' => '317103', 'nama' => 'MAMPANG PRAPATAN'],
                ['id' => '317104', 'nama' => 'PASAR MINGGU'],
                ['id' => '317105', 'nama' => 'KEBAYORAN BARU'],
                ['id' => '317106', 'nama' => 'KEBAYORAN LAMA'],
                ['id' => '317107', 'nama' => 'CILANDAK'],
                ['id' => '317108', 'nama' => 'JAGAKARSA'],
                ['id' => '317109', 'nama' => 'PANCORAN'],
                ['id' => '317110', 'nama' => 'PESANGGRAHAN'],
            ],
            // Jakarta Pusat
            '3173' => [
                ['id' => '317301', 'nama' => 'GAMBIR'],
                ['id' => '317302', 'nama' => 'SAWAH BESAR'],
                ['id' => '317303', 'nama' => 'KEMAYORAN'],
                ['id' => '317304', 'nama' => 'SENEN'],
                ['id' => '317305', 'nama' => 'CEMPAKA PUTIH'],
                ['id' => '317306', 'nama' => 'MENTENG'],
                ['id' => '317307', 'nama' => 'TANAH ABANG'],
                ['id' => '317308', 'nama' => 'JOHAR BARU'],
            ],
            // Kota Bandung
            '3273' => [
                ['id' => '327301', 'nama' => 'COBLONG'],
                ['id' => '327302', 'nama' => 'SUKAJADI'],
                ['id' => '327303', 'nama' => 'CICENDO'],
                ['id' => '327304', 'nama' => 'LENGKONG'],
                ['id' => '327305', 'nama' => 'SUMUR BANDUNG'],
                ['id' => '327306', 'nama' => 'BUAHBATU'],
            ],
            // Kota Surabaya
            '3578' => [
                ['id' => '357801', 'nama' => 'GUBENG'],
                ['id' => '357802', 'nama' => 'TEGALSARI'],
                ['id' => '357803', 'nama' => 'WONOKROMO'],
                ['id' => '357804', 'nama' => 'RUNGKUT'],
                ['id' => '357805', 'nama' => 'SUKOLILO'],
            ],
            // Kota Yogyakarta
            '3471' => [
                ['id' => '347101', 'nama' => 'DANUREJAN'],
                ['id' => '347102', 'nama' => 'GEDONGTENGEN'],
                ['id' => '347103', 'nama' => 'GONDOMANAN'],
                ['id' => '347104', 'nama' => 'KOTAGEDE'],
                ['id' => '347105', 'nama' => 'KRATON'],
                ['id' => '347106', 'nama' => 'MANTRIJERON'],
                ['id' => '347107', 'nama' => 'MERGANGSAN'],
                ['id' => '347108', 'nama' => 'NGAMPILAN'],
                ['id' => '347109', 'nama' => 'PAKUALAMAN'],
                ['id' => '347110', 'nama' => 'TEGALREJO'],
                ['id' => '347111', 'nama' => 'UMBULHARJO'],
                ['id' => '347112', 'nama' => 'WIROBRAJAN'],
            ],
            // Sleman
            '3404' => [
                ['id' => '340401', 'nama' => 'DEPOK'],
                ['id' => '340402', 'nama' => 'MLATI'],
                ['id' => '340403', 'nama' => 'GAMPING'],
                ['id' => '340404', 'nama' => 'NGAGLIK'],
                ['id' => '340405', 'nama' => 'KALASAN'],
            ],
        ];

        if (isset($all[$regencyId])) {
            return $all[$regencyId];
        }

        return [
            ['id' => $regencyId . '01', 'nama' => 'KECAMATAN PUSAT'],
            ['id' => $regencyId . '02', 'nama' => 'KECAMATAN UTARA'],
            ['id' => $regencyId . '03', 'nama' => 'KECAMATAN SELATAN'],
            ['id' => $regencyId . '04', 'nama' => 'KECAMATAN BARAT'],
            ['id' => $regencyId . '05', 'nama' => 'KECAMATAN TIMUR'],
        ];
    }

    /**
     * Daftar Kelurahan berdasarkan Kode Kecamatan
     */
    public function getVillages(string $districtId): array
    {
        $all = [
            // Tebet (Jakarta Selatan)
            '317101' => [
                ['id' => '3171011001', 'nama' => 'TEBET TIMUR', 'kode_pos' => '12820'],
                ['id' => '3171011002', 'nama' => 'TEBET BARAT', 'kode_pos' => '12810'],
                ['id' => '3171011003', 'nama' => 'MENTENG DALAM', 'kode_pos' => '12870'],
                ['id' => '3171011004', 'nama' => 'KEBON BARU', 'kode_pos' => '12830'],
                ['id' => '3171011005', 'nama' => 'BUKIT DURI', 'kode_pos' => '12840'],
                ['id' => '3171011006', 'nama' => 'MANGGARAI', 'kode_pos' => '12850'],
                ['id' => '3171011007', 'nama' => 'MANGGARAI SELATAN', 'kode_pos' => '12860'],
            ],
            // Depok (Sleman, DIY)
            '340401' => [
                ['id' => '3404012001', 'nama' => 'CATURTUNGGAL', 'kode_pos' => '55281'],
                ['id' => '3404012002', 'nama' => 'MAGUWOHARJO', 'kode_pos' => '55282'],
                ['id' => '3404012003', 'nama' => 'CONDONGCATUR', 'kode_pos' => '55283'],
            ],
            // Coblong (Bandung)
            '327301' => [
                ['id' => '3273011001', 'nama' => 'DAGO', 'kode_pos' => '40135'],
                ['id' => '3273011002', 'nama' => 'LEBAKGEDE', 'kode_pos' => '40132'],
                ['id' => '3273011003', 'nama' => 'SEKELOA', 'kode_pos' => '40134'],
                ['id' => '3273011004', 'nama' => 'SADANG SERANG', 'kode_pos' => '40133'],
            ],
            // Gubeng (Surabaya)
            '357801' => [
                ['id' => '3578011001', 'nama' => 'GUBENG', 'kode_pos' => '60281'],
                ['id' => '3578011002', 'nama' => 'AIRLANGGA', 'kode_pos' => '60286'],
                ['id' => '3578011003', 'nama' => 'BARATAJAYA', 'kode_pos' => '60284'],
                ['id' => '3578011004', 'nama' => 'PUCANGCSEWU', 'kode_pos' => '60283'],
            ],
        ];

        if (isset($all[$districtId])) {
            return $all[$districtId];
        }

        return [
            ['id' => $districtId . '1001', 'nama' => 'KELURAHAN SATU', 'kode_pos' => '10001'],
            ['id' => $districtId . '1002', 'nama' => 'KELURAHAN DUA', 'kode_pos' => '10002'],
            ['id' => $districtId . '1003', 'nama' => 'KELURAHAN TIGA', 'kode_pos' => '10003'],
        ];
    }
}
