<?php

namespace Tests\Feature;

use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\QuestionBank;
use App\Models\Scheme;
use App\Models\UnitElement;
use App\Models\User;
use Database\Seeders\ProindoSchemesAndMukSeeder;
use Database\Seeders\ProindoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProindoMasterDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_schemes_and_muk_seeder_loads_all_7_schemes(): void
    {
        $this->seed(ProindoSchemesAndMukSeeder::class);

        $expectedSchemes = [
            'SKM-01-2026',
            'SKM-02-2026',
            'SKM-03-2026',
            'SKM-04-2026',
            'SKM-05-2026',
            'SKM-06-2026',
            'SKM-07-2026',
        ];

        foreach ($expectedSchemes as $code) {
            $scheme = Scheme::where('code', $code)->first();
            $this->assertNotNull($scheme, "Scheme {$code} must be seeded.");
            $this->assertGreaterThan(0, $scheme->competencyUnits()->count(), "Scheme {$code} must have units.");
            $this->assertGreaterThan(0, QuestionBank::where('scheme_id', $scheme->id)->count(), "Scheme {$code} must have CBT questions.");
        }

        $totalUnits = CompetencyUnit::count();
        $totalElements = UnitElement::count();
        $totalKuks = Kuk::count();
        $totalQuestions = QuestionBank::count();

        $this->assertGreaterThan(20, $totalUnits);
        $this->assertGreaterThan(50, $totalElements);
        $this->assertGreaterThan(100, $totalKuks);
        $this->assertGreaterThan(50, $totalQuestions);
    }

    public function test_proindo_user_seeder_loads_admin_and_registered_asesors(): void
    {
        $this->seed(ProindoUserSeeder::class);

        $admin = User::where('email', 'adminlsp@gmail.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isRole('ADMIN_LSP') || $admin->isAdmin());

        $asesor = User::where('email', 'asesor.randi@gmail.com')->first();
        $this->assertNotNull($asesor);
        $this->assertEquals('MET.000.002498 2013', $asesor->assessor_registration_number);
    }
}
