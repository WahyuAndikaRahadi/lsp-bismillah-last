<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentProof;
use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\QuestionBank;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\UnitElement;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProindoModelAndSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_proindo_user_and_profile_relationship(): void
    {
        $user = User::create([
            'name' => 'Budi Asesor',
            'email' => 'budi@proindo.test',
            'password' => 'secret123',
            'role' => 'ASESOR',
        ]);

        $profile = UserProfile::create([
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'tempat_lahir' => 'Karawang',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'alamat_rumah' => 'Jl. Pemuda No. 10',
            'signature_data' => 'data:image/png;base64,sample_signature',
        ]);

        $this->assertTrue($user->isRole('ASESOR'));
        $this->assertEquals('Budi Asesor', $user->full_name);
        $this->assertNotNull($user->profile);
        $this->assertEquals('Karawang', $user->profile->tempat_lahir);
    }

    public function test_proindo_scheme_units_elements_and_kuks_hierarchy(): void
    {
        $scheme = Scheme::create([
            'code' => 'SKM-TEST-01',
            'name' => 'Test Scheme Junior Technician',
            'scheme_type' => 'KLASTER',
        ]);

        $unit = CompetencyUnit::create([
            'scheme_id' => $scheme->id,
            'code' => 'G.45OTO01.001.2',
            'title' => 'Melaksanakan Pemeliharaan Mesin',
        ]);

        $element = UnitElement::create([
            'competency_unit_id' => $unit->id,
            'title' => 'Menyiapkan peralatan',
        ]);

        $kuk = Kuk::create([
            'unit_element_id' => $element->id,
            'code_number' => '1.1',
            'statement' => 'Peralatan disiapkan sesuai SOP',
        ]);

        $question = QuestionBank::create([
            'scheme_id' => $scheme->id,
            'competency_unit_id' => $unit->id,
            'question_text' => 'Apa alat pelindung diri utama?',
            'option_a' => 'Helm',
            'option_b' => 'Kacamata',
            'option_c' => 'Sepatu Safety',
            'option_d' => 'Sarung Tangan',
            'correct_answer' => 'C',
        ]);

        $this->assertCount(1, $scheme->competencyUnits);
        $this->assertCount(1, $unit->elements);
        $this->assertCount(1, $element->kuks);
        $this->assertEquals('SKM-TEST-01', $kuk->scheme->code);
    }

    public function test_proindo_schedule_and_assessment_relationship(): void
    {
        $admin = User::create([
            'name' => 'Admin LSP',
            'email' => 'admin@proindo.test',
            'password' => 'secret123',
            'role' => 'ADMIN_LSP',
        ]);

        $asesor = User::create([
            'name' => 'Asesor Satu',
            'email' => 'asesor1@proindo.test',
            'password' => 'secret123',
            'role' => 'ASESOR',
        ]);

        $asesi = User::create([
            'name' => 'Asesi Uji',
            'email' => 'asesi1@proindo.test',
            'password' => 'secret123',
            'role' => 'ASESI',
        ]);

        $scheme = Scheme::create([
            'code' => 'SKM-TEST-02',
            'name' => 'Engine Specialist',
            'scheme_type' => 'OKUPASI',
        ]);

        $schedule = Schedule::create([
            'code' => 'LSP-TEST-99',
            'name' => 'Sertifikasi SMK Karawang',
            'scheme_id' => $scheme->id,
            'tuk_name' => 'Bengkel Otomotif SMKN 3 Karawang',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['AK01', 'IA01', 'IA02', 'IA05'],
            'created_by' => $admin->id,
        ]);

        $schedule->assessors()->attach($asesor->id);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $asesi->id,
            'assessor_id' => $asesor->id,
            'apl_status' => 'DRAFT',
        ]);

        $this->assertCount(1, $schedule->assessors);
        $this->assertCount(1, $schedule->assessments);
        $this->assertEquals('LSP-TEST-99', $assessment->schedule->code);
        $this->assertEquals('Asesi Uji', $assessment->asesi->name);
    }
}
