<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentIaResponse;
use App\Models\CompetencyUnit;
use App\Models\QuestionBank;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\UnitElement;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectAuditAndLoopEliminationTest extends TestCase
{
    use RefreshDatabase;

    protected User $asesi;
    protected User $asesor;
    protected Scheme $scheme;
    protected Schedule $schedule;
    protected Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Admin LSP',
            'email' => 'admin_test@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ADMIN_LSP',
            'is_active' => true,
        ]);

        $this->asesor = User::create([
            'name' => 'Asesor Penguji',
            'email' => 'asesor_test@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESOR',
            'is_active' => true,
        ]);

        $this->asesi = User::create([
            'name' => 'Calon Asesi',
            'email' => 'asesi_test@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201010101010009',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'alamat_rumah' => 'Jl. Merdeka No. 12',
            'signature_data' => 'data:image/png;base64,validsignaturedata123',
        ]);

        $this->scheme = Scheme::create([
            'code' => 'SKM-OTO-01',
            'name' => 'Engine Junior Technician',
            'scheme_type' => 'KLASTER',
        ]);

        $unit = CompetencyUnit::create([
            'scheme_id' => $this->scheme->id,
            'code' => 'G.45OTO01.001.2',
            'title' => 'Perbaikan Sistem Mesin',
        ]);

        $elem = UnitElement::create([
            'competency_unit_id' => $unit->id,
            'title' => 'Mengidentifikasi kerusakan mesin',
        ]);

        QuestionBank::create([
            'scheme_id' => $this->scheme->id,
            'competency_unit_id' => $unit->id,
            'question_text' => 'Fungsi kompresi pada ruang bakar adalah?',
            'option_a' => 'Menaikkan tekanan udara',
            'option_b' => 'Menurunkan suhu',
            'option_c' => 'Mengurangi oli',
            'option_d' => 'Menghentikan mesin',
            'correct_answer' => 'A',
        ]);

        $this->schedule = Schedule::create([
            'code' => 'SCH-2026-001',
            'name' => 'Uji Sertifikasi Otomotif 2026',
            'scheme_id' => $this->scheme->id,
            'tuk_name' => 'Bengkel Otomotif Pusat',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['AK01', 'IA01', 'IA02', 'IA05'],
            'created_by' => $admin->id,
            'status' => 'ACTIVE',
        ]);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'DRAFT',
            'status' => 'REGISTERED',
        ]);
    }

    public function test_ruang_uji_redirects_to_apl_when_apl_not_approved_without_loop(): void
    {
        // Assessment has apl_status = 'DRAFT'
        $response = $this->actingAs($this->asesi)
            ->get(route('asesi.assessment.ruang-uji', $this->assessment));

        // Must redirect directly to APL page with informative message
        $response->assertRedirect(route('asesi.assessment.apl', $this->assessment));

        // Following the redirect must return HTTP 200 (no secondary redirect loop)
        $followResponse = $this->actingAs($this->asesi)->get(route('asesi.assessment.apl', $this->assessment));
        $followResponse->assertStatus(200);
    }

    public function test_ruang_uji_redirects_to_ak01_when_apl_approved_but_ak01_not_agreed(): void
    {
        $this->assessment->update([
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => null,
        ]);

        $response = $this->actingAs($this->asesi)
            ->get(route('asesi.assessment.ruang-uji', $this->assessment));

        $response->assertRedirect(route('asesi.assessment.ak01', $this->assessment));

        // Following the redirect to AK01 must render 200 OK without getting stuck on AK07
        $followResponse = $this->actingAs($this->asesi)->get(route('asesi.assessment.ak01', $this->assessment));
        $followResponse->assertStatus(200);
    }

    public function test_ak01_can_be_signed_and_redirects_to_ruang_uji(): void
    {
        $this->assessment->update([
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => null,
        ]);

        $response = $this->actingAs($this->asesi)
            ->post(route('asesi.assessment.ak01.sign', $this->assessment), [
                'esign_consent' => '1',
                'signature_data' => 'data:image/png;base64,dGVzdA==',
            ]);

        $response->assertRedirect(route('asesi.assessment.ruang-uji', $this->assessment));
        $this->assessment->refresh();
        $this->assertNotNull($this->assessment->ak01_agreed_at);

        // Accessing ruang uji now returns 200 OK
        $ruangUjiResponse = $this->actingAs($this->asesi)->get(route('asesi.assessment.ruang-uji', $this->assessment));
        $ruangUjiResponse->assertStatus(200);
    }

    public function test_apl_submission_redirects_directly_to_dashboard_with_success(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $dummyPdf = \Illuminate\Http\UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf');
        $dummyFoto = \Illuminate\Http\UploadedFile::fake()->image('pasfoto.jpg');
        $dummyIjazah = \Illuminate\Http\UploadedFile::fake()->create('ijazah.pdf', 100, 'application/pdf');

        $postData = [
            'assessor_id' => $this->asesor->id,
            'tujuan_asesmen' => 'Sertifikasi',
            'statement_integrity' => '1',
            'ktp' => $dummyPdf,
            'pas_foto' => $dummyFoto,
            'ijazah_smk' => $dummyIjazah,
            'kuk' => [],
        ];

        $response = $this->actingAs($this->asesi)
            ->post(route('asesi.assessment.apl.store', $this->assessment), $postData);

        // Must redirect directly to dashboard (not bouncing back to APL form)
        $response->assertRedirect(route('asesi.dashboard'));
        $response->assertSessionHas('success');

        $this->assessment->refresh();
        $this->assertEquals('SUBMITTED', $this->assessment->apl_status);
    }
}
