<?php

namespace Tests\Unit;

use Tests\TestCase;

class FormRegistryTest extends TestCase
{
    private string $registryPath;
    private array $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registryPath = base_path('database/seeders/data/form-registry.json');
        if (file_exists($this->registryPath)) {
            $this->registry = json_decode(file_get_contents($this->registryPath), true) ?: [];
        } else {
            $this->registry = [];
        }
    }

    public function test_form_registry_file_exists_and_is_valid_json(): void
    {
        $this->assertFileExists($this->registryPath, 'form-registry.json must exist');
        $this->assertIsArray($this->registry, 'form-registry.json must be valid JSON object');
        $this->assertArrayHasKey('version', $this->registry);
        $this->assertArrayHasKey('forms', $this->registry);
        $this->assertArrayHasKey('blocked_sources', $this->registry);
    }

    public function test_all_seven_scheme_slots_are_represented(): void
    {
        $this->assertArrayHasKey('schemes', $this->registry);
        $expectedSchemes = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7'];
        foreach ($expectedSchemes as $scheme) {
            $this->assertArrayHasKey($scheme, $this->registry['schemes'], "Scheme slot {$scheme} must be represented in registry");
        }
    }

    public function test_no_duplicate_field_keys_within_form_sections(): void
    {
        foreach ($this->registry['forms'] as $formCode => $formData) {
            $seenKeys = [];
            foreach ($formData['sections'] ?? [] as $sectionKey => $section) {
                foreach ($section['fields'] ?? [] as $field) {
                    $fieldKey = $field['field_key'];
                    $this->assertFalse(
                        in_array($fieldKey, $seenKeys, true),
                        "Duplicate field_key [{$fieldKey}] in form [{$formCode}] section [{$sectionKey}]"
                    );
                    $seenKeys[] = $fieldKey;
                }
            }
        }
    }

    public function test_printed_fields_must_have_non_empty_labels_and_valid_page_numbers(): void
    {
        foreach ($this->registry['forms'] as $formCode => $formData) {
            foreach ($formData['sections'] ?? [] as $section) {
                foreach ($section['fields'] ?? [] as $field) {
                    if (($field['type'] ?? '') === 'printed' || ($field['printed'] ?? false) === true) {
                        $this->assertNotEmpty(
                            $field['original_label'] ?? '',
                            "Printed field [{$field['field_key']}] in form [{$formCode}] must have non-empty original_label"
                        );
                        $this->assertGreaterThan(
                            0,
                            $field['page'] ?? 0,
                            "Printed field [{$field['field_key']}] in form [{$formCode}] must have page > 0"
                        );
                    }
                }
            }
        }
    }

    public function test_blocked_sources_are_explicitly_marked_and_not_approved(): void
    {
        $expectedBlocked = [
            'IA-04.A',
            'IA-04.B',
            'IA-06.A',
            'IA-06.B',
            'IA-06.C',
            'IA-07',
            'IA-08',
            'IA-09',
            'IA-10',
            'IA-11',
        ];

        $blockedSources = $this->registry['blocked_sources'] ?? [];

        foreach ($expectedBlocked as $code) {
            $this->assertArrayHasKey($code, $blockedSources, "Instrument {$code} must be explicitly registered as blocked-source");
            $this->assertEquals('BLOCKED-SOURCE', $blockedSources[$code]['status']);
            $this->assertNotEmpty($blockedSources[$code]['reason']);
            $this->assertEquals('Q2', $blockedSources[$code]['owner_question']);
            $this->assertFalse($blockedSources[$code]['approved'] ?? true);
        }
    }

    public function test_core_sop_and_muk_common_forms_mapped_to_valid_sources(): void
    {
        $commonForms = [
            'FR-SOP-04.1',
            'FR-SOP-04.2',
            'FR-SOP-04.3',
            'FR-SOP-04.4',
            'FR-SOP-10.1',
            'FR-SOP-10.2',
            'FR-SOP-07.1',
            'FR-SOP-07.2',
            'FR-SOP-01.1',
            'FR-SOP-01.4',
            'FR-SOP-01.5',
            'FR-SOP-01.7',
            'FR-APL-01',
            'FR-APL-02',
            'FR-MAPA-01',
            'FR-MAPA-02',
            'FR-AK-01',
            'FR-AK-02',
            'FR-AK-03',
            'FR-AK-04',
            'FR-AK-05',
            'FR-AK-06',
            'FR-AK-07',
        ];

        foreach ($commonForms as $code) {
            $this->assertArrayHasKey($code, $this->registry['forms'], "Common form {$code} must be defined in form-registry");
            $this->assertNotEmpty($this->registry['forms'][$code]['sections'], "Form {$code} must have sections");
        }
    }

    public function test_no_transactional_data_or_sample_names_in_registry(): void
    {
        $raw = file_get_contents($this->registryPath);
        $this->assertStringNotContainsString('John Doe', $raw);
        $this->assertStringNotContainsString('Contoh Asesi', $raw);
        $this->assertStringNotContainsString('Kompeten Otomatis', $raw);
    }
}
