<?php

namespace Tests\Unit;

use App\Models\Scheme;
use App\Services\SchemeInstrumentService;
use Tests\TestCase;

class RemainingInstrumentSourceGateTest extends TestCase
{
    private array $blockedInstrumentCodes = [
        'FR.IA.04A',
        'FR.IA.04B',
        'FR.IA.06',
        'FR.IA.07',
        'FR.IA.08',
        'FR.IA.09',
        'FR.IA.10',
        'FR.IA.11',
    ];

    public function test_blocked_source_families_are_inactive_in_mapa02_template(): void
    {
        $service = new SchemeInstrumentService();
        $scheme = new Scheme(['id' => 1, 'code' => 'SKM-01-2026', 'name' => 'Sepeda Motor Karburator']);

        $template = $service->getMapa02Template($scheme);

        $this->assertNotEmpty($template);

        foreach ($template as $group) {
            foreach ($group['instruments'] as $inst) {
                if (in_array($inst['code'], $this->blockedInstrumentCodes, true)) {
                    $this->assertFalse(
                        $inst['is_active'],
                        "Instrumen {$inst['code']} harus berstatus tidak aktif (is_active = false) karena sumber dokumen fisiknya belum tersedia (blocked-source)."
                    );
                }
            }
        }
    }

    public function test_form_registry_does_not_contain_fabricated_fields_for_missing_instruments(): void
    {
        $registryPath = database_path('seeders/data/form-registry.json');
        if (file_exists($registryPath)) {
            $registry = json_decode(file_get_contents($registryPath), true) ?: [];

            foreach ($this->blockedInstrumentCodes as $code) {
                if (isset($registry[$code])) {
                    $this->assertNotEquals(
                        'published',
                        $registry[$code]['status'] ?? 'blocked',
                        "Formulir {$code} tidak boleh berstatus published sebelum dokumen original disahkan."
                    );
                }
            }
        } else {
            $this->assertTrue(true);
        }
    }
}
