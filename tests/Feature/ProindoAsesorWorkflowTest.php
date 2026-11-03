<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAk07Item;
use App\Models\AssessmentIaResponse;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\ProindoSchemesAndMukSeeder;
use Database\Seeders\ProindoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProindoAsesorWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $asesor;

    protected User $asesi;

    protected Scheme $scheme;

    protected Schedule $schedule;

    protected Assessment $assessment;

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
            'email' => 'asesi.pro@test.com',
            'full_name' => 'Budi Peserta Test',
        ]);

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201998877665544',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1995-02-10',
            'alamat_rumah' => 'Jl. Merdeka No 45',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ]);

        $this->scheme = Scheme::first();

        $this->schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'name' => 'Jadwal Sertifikasi Asesor Test',
            'code' => 'SCH-ASESOR-01',
            'tuk_name' => 'TUK Otomotif Presisi',
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'selected_instruments' => ['IA01', 'IA01B', 'IA02', 'IA03', 'IA05'],
            'status' => 'ACTIVE',
            'is_closed' => false,
            'created_by' => $this->admin->id,
        ]);
        $this->schedule->assessors()->attach($this->asesor->id);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'SUBMITTED',
            'registered_at' => now(),
        ]);
    }

    public function test_asesor_can_view_assigned_schedules_and_candidates(): void
    {
        $response = $this->actingAs($this->asesor)->get(route('asesor.jadwal.index'));
        $response->assertOk();
        $response->assertSee($this->schedule->name);

        $responseDetail = $this->actingAs($this->asesor)->get(route('asesor.jadwal.asesi', $this->schedule));
        $responseDetail->assertOk();
        $responseDetail->assertSee($this->asesi->full_name);
    }

    public function test_asesor_can_review_and_approve_apl(): void
    {
        $response = $this->actingAs($this->asesor)->post(route('asesor.apl.decide', $this->assessment), [
            'action' => 'approve',
            'notes' => 'Berkas persyaratan lengkap dan valid.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('t_assessments', [
            'id' => $this->assessment->id,
            'apl_status' => 'APPROVED',
        ]);
    }

    public function test_asesor_can_store_ak07_reasonable_adjustment(): void
    {
        $this->assessment->update(['apl_status' => 'APPROVED']);

        $response = $this->actingAs($this->asesor)->post(route('asesor.schedule.ak07.store', $this->schedule), [
            'assessment_id' => $this->assessment->id,
            'timing' => 'saat_pra_asesmen',
            'items' => [
                [
                    'category_number' => 1,
                    'title' => 'Standar',
                    'decision' => 'tidak',
                ],
            ],
            'agreement_status' => 'ya',
            'agreed_benchmark' => 'Standar',
        ]);

        $response->assertRedirect(route('asesor.schedule.ak01', $this->schedule));
        $this->assertDatabaseHas('t_assessment_ak07_items', [
            'assessment_id' => $this->assessment->id,
            'adjustment_category' => 'Standar',
        ]);
    }

    public function test_asesor_can_view_ruang_uji_and_assess_instrument(): void
    {
        $this->assessment->update(['apl_status' => 'APPROVED']);

        AssessmentAk07Item::create([
            'assessment_id' => $this->assessment->id,
            'adjustment_category' => 'Standar',
            'adjustment_detail' => 'Sesuai SOP',
            'assessor_id' => $this->asesor->id,
        ]);

        $response = $this->actingAs($this->asesor)->get(route('asesor.assessment.ruang-uji', $this->assessment));
        $response->assertOk();
        $response->assertSee('FR.IA.01');
    }

    public function test_asesor_can_finalize_assessment_with_ak02_sync(): void
    {
        $this->assessment->update(['apl_status' => 'APPROVED']);

        $response = $this->actingAs($this->asesor)->post(route('asesor.assessment.complete', $this->assessment));

        $response->assertRedirect(route('asesor.assessment.ak02', $this->assessment));
        $this->assertDatabaseHas('t_assessment_ak02_items', [
            'assessment_id' => $this->assessment->id,
        ]);

        AssessmentIaResponse::create([
            'assessment_id' => $this->assessment->id,
            'instrument_code' => 'IA01',
            'started_at' => now(),
            'duration_seconds' => 3600,
            'time_remaining_seconds' => 0,
            'attempt_version' => 1,
            'data_json' => ['observations' => []],
            'is_expired' => false,
            'submitted_at' => now(),
        ]);

        $unitsPayload = [];
        foreach ($this->scheme->competencyUnits as $unit) {
            $unitsPayload[$unit->id] = [
                'is_competent' => 1,
                'observasi_demonstrasi' => 1,
            ];
        }

        $responseUpdate = $this->actingAs($this->asesor)->post(route('asesor.assessment.ak02.update', $this->assessment), [
            'final_result' => 'KOMPETEN',
            'final_notes' => 'Seluruh unit kompeten.',
            'follow_up_action' => 'Diusulkan sertifikat BNSP.',
            'units' => $unitsPayload,
        ]);

        $responseUpdate->assertRedirect(route('asesor.jadwal.asesi', $this->schedule));
        $this->assertDatabaseHas('t_assessments', [
            'id' => $this->assessment->id,
            'final_result' => 'KOMPETEN',
        ]);
    }

    public function test_asesor_can_submit_ak05_and_ak06_reports_and_close_schedule(): void
    {
        // Set candidate as completed
        $this->assessment->update([
            'apl_status' => 'APPROVED',
            'status' => 'COMPLETED',
            'final_result' => 'KOMPETEN',
        ]);

        $responseView = $this->actingAs($this->asesor)->get(route('asesor.schedule.reports', $this->schedule));
        $responseView->assertOk();
        $responseView->assertSee('FR.AK.05');
        $responseView->assertSee('FR.AK.06');

        $responseSubmit = $this->actingAs($this->asesor)->post(route('asesor.schedule.reports.submit', $this->schedule), [
            'ak05_aspek_positif' => 'Sarana prasarana bengkel TUK lengkap dan APD memadai.',
            'ak05_aspek_negatif' => 'Tidak ditemukan kendala teknis yang menghambat asesmen.',
            'ak05_rekomendasi' => 'Peningkatan durasi pengenalan SST sebelum unjuk kerja.',
            'ak06_valid' => 'YA',
            'ak06_valid_notes' => 'Bukti berpatokan langsung pada KUK.',
            'ak06_reliabel' => 'YA',
            'ak06_reliabel_notes' => 'Rubrik penilaian diterapkan secara objektif dan seragam.',
            'ak06_fleksibel' => 'YA',
            'ak06_fleksibel_notes' => 'Tersedia instrumen penyesuaian wajar FR.AK.07.',
            'ak06_adil' => 'YA',
            'ak06_adil_notes' => 'Setiap asesi diperlakukan secara transparan dan adil.',
            'ak06_catatan_peninjau' => 'Seluruh tahapan asesmen telah memenuhi SOP BNSP.',
            'confirm_draft_review' => '1',
        ]);

        $responseSubmit->assertRedirect(route('asesor.jadwal.index'));
        $this->schedule->refresh();
        $this->assertTrue((bool) $this->schedule->is_closed);
        $this->assertEquals('FINISHED', $this->schedule->status);
        $this->assertNotNull($this->schedule->ak05_report);
        $this->assertNotNull($this->schedule->ak06_review);
    }
}
