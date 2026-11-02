<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\QuestionBank;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\ProindoSchemesAndMukSeeder;
use Database\Seeders\ProindoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProindoAsesiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $asesi;

    protected User $asesor;

    protected User $admin;

    protected Scheme $scheme;

    protected Schedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProindoSchemesAndMukSeeder::class);
        $this->seed(ProindoUserSeeder::class);

        $this->admin = User::where('role', 'ADMIN_LSP')->first()
            ?? User::factory()->create(['role' => 'ADMIN_LSP', 'email' => 'admin@test.com']);

        $this->asesor = User::where('role', 'ASESOR')->first()
            ?? User::factory()->create(['role' => 'ASESOR', 'email' => 'asesor@test.com']);

        $this->asesi = User::factory()->create([
            'role' => 'ASESI',
            'email' => 'asesi.baru@example.com',
            'full_name' => 'Budi Asesi Tes',
            'phone' => '081234567890',
        ]);

        $this->scheme = Scheme::first();

        $this->schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'name' => 'Uji Kompetensi Otomotif Gelombang 1',
            'code' => 'TEST-SCH-01',
            'tuk_name' => 'TUK Mandiri Test',
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'selected_instruments' => ['IA01', 'IA01B', 'IA02', 'IA03', 'IA05'],
            'status' => 'ACTIVE',
            'is_closed' => false,
            'created_by' => $this->admin->id,
        ]);
        $this->schedule->assessors()->attach($this->asesor->id);
    }

    public function test_asesi_profile_update_with_signature(): void
    {
        $dummySig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->asesi)->post(route('asesi.profile.update'), [
            'full_name' => 'Budi Santoso',
            'nik' => '3201234567890001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1998-05-12',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'SMK Otomotif',
            'alamat_rumah' => 'Jl. Kebon Sirih No. 10',
            'jabatan' => 'Teknisi Junior',
            'phone' => '081234567890',
            'signature_data' => $dummySig,
        ]);

        $response->assertRedirect(route('asesi.profile.edit'));
        $this->assertDatabaseHas('m_user_profiles', [
            'user_id' => $this->asesi->id,
            'nik' => '3201234567890001',
            'tempat_lahir' => 'Jakarta',
        ]);

        $this->assertTrue($this->asesi->fresh()->isProfileComplete());
    }

    public function test_asesi_cannot_join_without_complete_profile(): void
    {
        $response = $this->actingAs($this->asesi)->post(route('asesi.jadwal.join'), [
            'code' => 'TEST-SCH-01',
        ]);

        $response->assertRedirect(route('asesi.profile.edit'));
    }

    public function test_asesi_can_join_active_schedule_with_valid_code(): void
    {
        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201234567890001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1998-05-12',
            'alamat_rumah' => 'Jl. Kebon Jeruk No 1',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ]);

        $response = $this->actingAs($this->asesi)->post(route('asesi.jadwal.join'), [
            'code' => 'TEST-SCH-01',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('t_assessments', [
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'apl_status' => 'DRAFT',
        ]);
    }

    public function test_apl_approval_gate_prevents_unauthorized_access_to_ruang_uji(): void
    {
        $assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'apl_status' => 'DRAFT',
        ]);

        $response = $this->actingAs($this->asesi)->get(route('asesi.assessment.ruang-uji', $assessment));
        $response->assertRedirect(route('asesi.assessment.apl', $assessment));
    }

    public function test_asesi_can_submit_apl01_and_apl02_items(): void
    {
        Storage::fake('local');

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201234567890001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1998-05-12',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'SMK',
            'alamat_rumah' => 'Jl. Kebon Jeruk No 1',
            'jabatan' => 'Mekanik',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ]);

        $assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'apl_status' => 'DRAFT',
        ]);

        $kuks = $this->scheme->competencyUnits()->first()->elements()->first()->kuks;
        $kukAnswers = [];
        foreach ($kuks as $kuk) {
            $kukAnswers[$kuk->id] = 'K';
        }

        $dummyPdf = UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf');
        $dummyFoto = UploadedFile::fake()->image('pasfoto.jpg');
        $dummyIjazah = UploadedFile::fake()->create('ijazah.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->asesi)->post(route('asesi.assessment.apl.store', $assessment), [
            'assessor_id' => $this->asesor->id,
            'tujuan_asesmen' => 'Sertifikasi',
            'statement_integrity' => '1',
            'kuk' => $kukAnswers,
            'ktp' => $dummyPdf,
            'pas_foto' => $dummyFoto,
            'ijazah_smk' => $dummyIjazah,
        ]);

        $response->assertRedirect(route('asesi.dashboard'));
        $this->assertDatabaseHas('t_assessments', [
            'id' => $assessment->id,
            'apl_status' => 'SUBMITTED',
            'assessor_id' => $this->asesor->id,
        ]);
    }

    public function test_asesi_can_access_and_submit_ia05_cbt(): void
    {
        $assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'apl_status' => 'APPROVED',
        ]);

        $questions = QuestionBank::where('scheme_id', $this->scheme->id)->take(5)->get();
        if ($questions->isEmpty()) {
            $unit = $this->scheme->competencyUnits()->first();
            $q = QuestionBank::create([
                'scheme_id' => $this->scheme->id,
                'competency_unit_id' => $unit->id,
                'question' => 'Contoh soal uji tertulis otomotif?',
                'option_a' => 'Jawaban A',
                'option_b' => 'Jawaban B',
                'option_c' => 'Jawaban C',
                'option_d' => 'Jawaban D',
                'correct_answer' => 'A',
            ]);
            $questions = collect([$q]);
        }

        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $response = $this->actingAs($this->asesi)->post(route('asesi.assessment.instrument.ia05.submit', $assessment), [
            'answers' => $answers,
        ]);

        $response->assertRedirect(route('asesi.assessment.ruang-uji', $assessment));
        $this->assertDatabaseHas('t_assessment_ia_responses', [
            'assessment_id' => $assessment->id,
            'instrument_code' => 'IA05',
        ]);
    }
}
