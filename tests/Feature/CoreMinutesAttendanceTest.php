<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\AttendanceEvent;
use App\Models\Schedule;
use App\Models\ScheduleMinutes;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use App\Services\ScheduleMinutesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreMinutesAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $coordinator;
    private User $assessor;
    private User $asesi1;
    private User $asesi2;
    private User $asesi3;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP',
            'email' => 'admin.min@lsp.test',
        ]);

        $this->coordinator = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Koordinator TUK Resmi',
            'email' => 'coord@lsp.test',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Penguji',
            'email' => 'assessor.min@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Satu',
            'email' => 'asesi1.min@lsp.test',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Dua',
            'email' => 'asesi2.min@lsp.test',
        ]);

        $this->asesi3 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Tiga',
            'email' => 'asesi3.min@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-MIN-01',
            'nama_tuk' => 'TUK Otomotif Berita Acara',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->scheme = Scheme::firstOrCreate(
            ['code' => 'SKM-01-2026'],
            ['name' => 'Sepeda Motor Karburator', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->schemeVersion = SchemeVersion::firstOrCreate(
            ['scheme_id' => $this->scheme->id, 'version' => 1],
            ['code' => 'SKM-01-2026-V1', 'title' => 'SKKNI 2018', 'is_active' => true]
        );
    }

    private function createScheduleWithMixedCandidates(): array
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-MIN-' . rand(100, 999),
            'name' => 'Jadwal Uji Berita Acara',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $schedule->assessors()->attach($this->assessor->id);

        // Candidate 1: Present, KOMPETEN
        $ass1 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi1->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->assessor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
            'rekomendasi_asesor' => 'K',
            'status' => 'COMPLETED',
        ]);
        $ev1 = AttendanceEvent::create([
            'schedule_id' => $schedule->id,
            'user_id' => $this->asesi1->id,
            'event_type' => 'asesi_hadir',
            'recorded_by' => $this->assessor->id,
            'occurred_at' => now(),
        ]);

        // Candidate 2: Present, BELUM_KOMPETEN
        $ass2 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi2->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->assessor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_KOMPETEN',
            'rekomendasi_asesor' => 'BK',
            'status' => 'COMPLETED',
        ]);
        $ev2 = AttendanceEvent::create([
            'schedule_id' => $schedule->id,
            'user_id' => $this->asesi2->id,
            'event_type' => 'asesi_hadir',
            'recorded_by' => $this->assessor->id,
            'occurred_at' => now(),
        ]);

        // Candidate 3: Absent (no attendance event), result BELUM_SELESAI (pending)
        $ass3 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi3->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->assessor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_SELESAI',
            'rekomendasi_asesor' => null,
            'status' => 'scheduled',
        ]);

        return [$schedule, [$ass1, $ass2, $ass3], [$ev1, $ev2]];
    }

    public function test_minutes_get_does_not_mutate_state(): void
    {
        [$schedule] = $this->createScheduleWithMixedCandidates();

        $this->actingAs($this->admin);

        $countBefore = ScheduleMinutes::count();

        $response = $this->get(route('admin.schedules.minutes.show', $schedule->id));
        $response->assertStatus(200);

        // Verification: No record created on GET
        $this->assertEquals($countBefore, ScheduleMinutes::count(), 'GET request ke minutes.show dilarang memutasi database.');
    }

    public function test_mixed_attendance_and_result_aggregation_preserves_enrolled_equals_present_plus_absent(): void
    {
        [$schedule] = $this->createScheduleWithMixedCandidates();

        $service = app(ScheduleMinutesService::class);
        $facts = $service->aggregateScheduleFacts($schedule);

        // Enrolled = 3
        $this->assertEquals(3, $facts['enrolled_count']);
        // Present = 2 (Asesi 1 and 2), Absent = 1 (Asesi 3)
        $this->assertEquals(2, $facts['present_count']);
        $this->assertEquals(1, $facts['absent_count']);
        // Strict boundary: Enrolled == Present + Absent
        $this->assertEquals($facts['enrolled_count'], $facts['present_count'] + $facts['absent_count']);

        // Results: 1 K, 1 BK, 1 Pending
        $this->assertEquals(1, $facts['competent_count']);
        $this->assertEquals(1, $facts['not_competent_count']);
        $this->assertEquals(1, $facts['pending_count']);
    }

    public function test_attendance_reversal_updates_present_count_accurately(): void
    {
        [$schedule, $assessments, $events] = $this->createScheduleWithMixedCandidates();

        $service = app(ScheduleMinutesService::class);

        // Reverse attendance for Asesi 2
        $events[1]->update(['is_reversed' => true]);

        $facts = $service->aggregateScheduleFacts($schedule);

        // Now only Asesi 1 is present; Asesi 2 and 3 are absent
        $this->assertEquals(3, $facts['enrolled_count']);
        $this->assertEquals(1, $facts['present_count']);
        $this->assertEquals(2, $facts['absent_count']);
        $this->assertEquals($facts['enrolled_count'], $facts['present_count'] + $facts['absent_count']);
    }

    public function test_missing_coordinator_or_invalid_actual_times_is_rejected(): void
    {
        [$schedule] = $this->createScheduleWithMixedCandidates();

        $this->actingAs($this->admin);

        // Case A: Missing coordinator
        $postDataNoCoord = [
            'actual_date' => now()->toDateString(),
            'actual_start_time' => '08:30',
            'actual_end_time' => '15:30',
            'status' => 'DRAFT',
            // coordinator_id omitted
        ];
        $resA = $this->post(route('admin.schedules.minutes.store', $schedule->id), $postDataNoCoord);
        $resA->assertSessionHasErrors(['coordinator_id']);

        // Case B: actual_start_time >= actual_end_time
        $postDataBadTimes = [
            'coordinator_id' => $this->coordinator->id,
            'actual_date' => now()->toDateString(),
            'actual_start_time' => '16:00',
            'actual_end_time' => '08:00', // start is after end!
            'status' => 'DRAFT',
        ];
        $resB = $this->post(route('admin.schedules.minutes.store', $schedule->id), $postDataBadTimes);
        $resB->assertSessionHasErrors();
    }

    public function test_finalization_blocked_if_pending_results_remain(): void
    {
        [$schedule] = $this->createScheduleWithMixedCandidates();

        $this->actingAs($this->admin);

        // Asesi 3 is still pending! Attempting status = FINAL must be blocked.
        $postDataFinal = [
            'coordinator_id' => $this->coordinator->id,
            'actual_date' => now()->toDateString(),
            'actual_start_time' => '08:30',
            'actual_end_time' => '15:30',
            'status' => 'FINAL',
        ];

        $response = $this->post(route('admin.schedules.minutes.store', $schedule->id), $postDataFinal);
        $response->assertSessionHasErrors(['minutes']);

        $this->assertEquals(0, ScheduleMinutes::where('status', 'FINAL')->count());
    }

    public function test_successful_draft_and_final_minutes_recording(): void
    {
        [$schedule, $assessments] = $this->createScheduleWithMixedCandidates();

        // Finalize Asesi 3 as BELUM_KOMPETEN so 0 pending remains
        $assessments[2]->update([
            'final_result' => 'BELUM_KOMPETEN',
            'rekomendasi_asesor' => 'BK',
            'status' => 'COMPLETED',
        ]);

        $this->actingAs($this->admin);

        // 1. Save DRAFT
        $postDataDraft = [
            'coordinator_id' => $this->coordinator->id,
            'actual_date' => now()->toDateString(),
            'actual_start_time' => '08:30',
            'actual_end_time' => '15:30',
            'location' => 'TUK Otomotif Berita Acara',
            'notes' => 'Catatan draft awal',
            'status' => 'DRAFT',
        ];

        $resDraft = $this->post(route('admin.schedules.minutes.store', $schedule->id), $postDataDraft);
        $resDraft->assertSessionHasNoErrors();

        $draft = ScheduleMinutes::where('schedule_id', $schedule->id)->latest('revision')->first();
        $this->assertNotNull($draft);
        $this->assertEquals('DRAFT', $draft->status);
        $this->assertEquals(1, $draft->revision);

        // 2. Save FINAL with revision 2
        $postDataFinal = [
            'coordinator_id' => $this->coordinator->id,
            'actual_date' => now()->toDateString(),
            'actual_start_time' => '08:30',
            'actual_end_time' => '15:30',
            'location' => 'TUK Otomotif Berita Acara',
            'notes' => 'Catatan final berita acara disahkan',
            'status' => 'FINAL',
            'lock_version' => $draft->lock_version,
        ];

        $resFinal = $this->post(route('admin.schedules.minutes.store', $schedule->id), $postDataFinal);
        $resFinal->assertSessionHasNoErrors();

        $final = ScheduleMinutes::where('schedule_id', $schedule->id)->latest('revision')->first();
        $this->assertNotNull($final);
        $this->assertEquals('FINAL', $final->status);
        $this->assertEquals(2, $final->revision);
        $this->assertEquals(3, $final->enrolled_count);
        $this->assertEquals(2, $final->present_count);
        $this->assertEquals(1, $final->absent_count);
        $this->assertEquals(1, $final->competent_count);
        $this->assertEquals(2, $final->not_competent_count);
        $this->assertEquals(0, $final->pending_count);
    }
}
