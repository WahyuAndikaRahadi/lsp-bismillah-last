<?php

namespace Tests\Feature;

use Tests\TestCase;

class Scheme7ReferenceCoverageTest extends TestCase
{
    private array $dataset;
    private array $fieldMap;

    protected function setUp(): void
    {
        parent::setUp();

        $dataPath = base_path('database/seeders/data/SKM-07-2026.json');
        $this->assertFileExists($dataPath, 'Berkas dataset SKM-07-2026.json wajib tersedia.');
        $this->dataset = json_decode(file_get_contents($dataPath), true);

        $mapPath = base_path('database/seeders/data/field-maps/S7.json');
        $this->assertFileExists($mapPath, 'Berkas field mapping S7.json wajib tersedia.');
        $this->fieldMap = json_decode(file_get_contents($mapPath), true);
    }

    public function test_s7_dataset_is_marked_needs_review_and_never_published(): void
    {
        $this->assertEquals('needs-review', $this->dataset['scheme']['status'] ?? null);
        $this->assertEquals('needs-review', $this->fieldMap['status'] ?? null);
    }

    public function test_s7_exact_inventory_counts_match_field_map(): void
    {
        // 1. Units count
        $units = $this->dataset['units'] ?? [];
        $this->assertCount(19, $units);
        $this->assertEquals(19, $this->fieldMap['counts']['units']);

        // 2. Elements and KUKs count
        $elemCount = 0;
        $kukCount = 0;
        foreach ($units as $u) {
            $elements = $u['elements'] ?? [];
            $elemCount += count($elements);
            foreach ($elements as $e) {
                $kukCount += count($e['kuks'] ?? []);
            }
        }
        $this->assertEquals(53, $elemCount);
        $this->assertEquals(271, $kukCount);
        $this->assertEquals(53, $this->fieldMap['counts']['elements']);
        $this->assertEquals(271, $this->fieldMap['counts']['kuks']);

        // 3. Questions and options
        $questions = $this->dataset['questions'] ?? [];
        $this->assertCount(65, $questions);
        $this->assertEquals(65, $this->fieldMap['counts']['questions']);

        $totalOptions = 0;
        foreach ($questions as $q) {
            $opts = array_filter([
                $q['option_a'] ?? null,
                $q['option_b'] ?? null,
                $q['option_c'] ?? null,
                $q['option_d'] ?? null,
            ]);
            $totalOptions += count($opts);
            $this->assertNotEmpty($q['correct_answer'] ?? null);
        }
        $this->assertEquals(260, $totalOptions);
        $this->assertEquals(260, $this->fieldMap['counts']['options']);

        // 4. Practical & Observation instruments
        $pmo = $this->dataset['pmo'] ?? [];
        $this->assertCount(16, $pmo);
        $this->assertEquals(16, $this->fieldMap['counts']['pmo']);

        $reportSheet = $this->dataset['report_sheet'] ?? [];
        $this->assertCount(18, $reportSheet);
        $this->assertEquals(18, $this->fieldMap['counts']['report_sheet_cells']);
    }

    public function test_s7_family_slots_and_blocked_instruments_are_explicitly_tracked(): void
    {
        $forms = $this->fieldMap['forms'];

        // Available core forms
        $this->assertEquals('available', $forms['FR.APL.01']['status']);
        $this->assertEquals('available', $forms['FR.APL.02']['status']);
        $this->assertEquals('available', $forms['FR.MAPA.01']['status']);
        $this->assertEquals('available', $forms['FR.MAPA.02']['status']);
        $this->assertEquals('available', $forms['FR.IA.01']['status']);
        $this->assertEquals('available', $forms['FR.IA.02']['status']);
        $this->assertEquals('available', $forms['FR.IA.03']['status']);
        $this->assertEquals('available', $forms['FR.IA.05']['status']);

        // Blocked source instruments (Q2)
        $this->assertEquals('blocked', $forms['FR.IA.04.A']['status']);
        $this->assertEquals('blocked', $forms['FR.IA.04.B']['status']);
        $this->assertEquals('blocked', $forms['FR.IA.06']['status']);
        $this->assertEquals('blocked', $forms['FR.IA.07']['status']);
        $this->assertEquals('blocked', $forms['FR.IA.08']['status']);
    }
}
