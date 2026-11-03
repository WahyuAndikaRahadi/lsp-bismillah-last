<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AsesorProfile;
use App\Models\Assessment;
use App\Models\AttendanceEvent;
use App\Models\AuditEvent;
use App\Models\Schedule;
use App\Models\ScheduleConsent;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AttendanceService;
use App\Services\ScheduleConsentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CoreConsentAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor;
    private User $asesi;
    private User $otherAsesi;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;
    private ScheduleConsentService $consentService;
    private AttendanceService $attendanceService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consentService = app(ScheduleConsentService::class);
        $this->attendanceService = app(AttendanceService::class);

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'full_name' => 'Admin LSP Utama',
            'email' => 'admin@proindo.id',
            'status' => 'ACTIVE',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'full_name' => 'Asesor Senior',
            'email' => 'asesor@proindo.id',
            'status' => 'ACTIVE',
        ]);
        AsesorProfile::create([
            'user_id' => $this->assessor->id,
            'no_met' => 'MET.77788.2026',
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Budi Asesi',
            'email' => 'budi@gmail.com',
            'status' => 'ACTIVE',
        ]);
        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201012345670001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'alamat_rumah' => 'Jl. Merdeka No. 10',
            'phone' => '+628123456789',
            'signature_data' => 'SIG-INITIAL-BUDI-BASE64',
            'signature_version' => 1,
        ]);

        $this->otherAsesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Siti Asesi',
            'email' => 'siti@gmail.com',
            'status' => 'ACTIVE',
        ]);

        $this->tuk = Tuk::firstOrCreate(
            ['kode_tuk' => 'TUK-CONSENT-01'],
            ['nama_tuk' => 'TUK Mandiri Otomotif', 'jenis_tuk' => 'Mandiri', 'is_active' => true]
        );

        $this->scheme = Scheme::firstOrCreate(
            ['code' => 'SKM-01-2026'],
            ['name' => 'Sepeda Motor Karburator', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->schemeVersion = SchemeVersion::firstOrCreate(
            ['scheme_id' => $this->scheme->id, 'version' => 1],
            ['code' => 'SKM-01-2026-V1', 'title' => 'SKKNI 2018', 'is_active' => true]
        );
    }

    private function createSchedule(string $startDate, string $endDate): Schedule
    {
        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-TEST-' . rand(1000, 9999),
            'name' => 'Jadwal Uji Asesmen',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'date' => $startDate,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->name ?? 'TUK Mandiri',
            'created_by' => $this->admin->id,
            'status' => 'ACTIVE',
            'is_active' => true,
        ]);

        $schedule->assessors()->attach($this->assessor->id);

        return $schedule;
    }

    private function createEligibleAssessment(Schedule $schedule): Assessment
    {
        return Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => now(),
            'status' => 'scheduled',
            'registered_at' => now(),
        ]);
    }

    public function test_apl_approval_does_not_create_consent(): void
    {
        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'apl_status' => 'SUBMITTED',
            'status' => 'scheduled',
        ]);

        $this->actingAs($this->assessor);
        $this->post(route('asesor.apl.decide', $assessment->id), [
            'action' => 'approve',
            'rekomendasi' => 'Diterima sebagai peserta sertifikasi',
        ]);

        $assessment->refresh();
        $this->assertEquals('APPROVED', $assessment->apl_status);

        // Approval does NOT create consent!
        $this->assertEquals(0, ScheduleConsent::where('schedule_id', $schedule->id)->count());
    }

    public function test_user_cannot_grant_consent_for_another_user(): void
    {
        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');

        $this->actingAs($this->otherAsesi);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tidak dapat memberikan persetujuan atas nama pengguna lain');

        $this->consentService->recordConsent(
            $schedule,
            $this->asesi, // Siti attempts to grant consent for Budi
            'join',
            'Pernyataan Integritas Palsu',
            'SIG-FAKE'
        );
    }

    public function test_signature_profile_update_does_not_mutate_existing_consents_or_documents(): void
    {
        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');

        $this->actingAs($this->asesi);

        $consent = $this->consentService->recordConsent(
            $schedule,
            $this->asesi,
            'join',
            'Pernyataan persetujuan bergabung jadwal asesmen',
            'SIG-INITIAL-BUDI-BASE64'
        );

        $originalHash = $consent->text_hash;
        $originalSig = $consent->signature_data;
        $originalGrantedAt = $consent->granted_at;

        // Budi later updates his profile signature
        $profile = $this->asesi->profile;
        $profile->update([
            'signature_data' => 'SIG-MUTATED-NEW-SIGNATURE',
            'signature_version' => 2,
        ]);

        $consent->refresh();

        // Old consent remains immutable
        $this->assertEquals($originalHash, $consent->text_hash);
        $this->assertEquals($originalSig, $consent->signature_data);
        $this->assertEquals($originalGrantedAt->toDateTimeString(), $consent->granted_at->toDateTimeString());
    }

    public function test_get_ruang_uji_twice_does_not_record_attendance(): void
    {
        Carbon::setTestNow('2026-11-11 09:00:00');

        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');
        $assessment = $this->createEligibleAssessment($schedule);

        $this->actingAs($this->asesi);

        // GET once
        $res1 = $this->get(route('asesi.assessment.ruang-uji', $assessment->id));
        $res1->assertStatus(200);

        // GET twice
        $res2 = $this->get(route('asesi.assessment.ruang-uji', $assessment->id));
        $res2->assertStatus(200);

        // GET / login does NOT create attendance
        $this->assertEquals(0, AttendanceEvent::count());
        $this->assertNull($assessment->fresh()->attendance_at);

        Carbon::setTestNow();
    }

    public function test_early_and_late_entry_are_rejected(): void
    {
        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');
        $assessment = $this->createEligibleAssessment($schedule);

        $this->actingAs($this->asesi);

        // Give join consent
        $this->consentService->recordConsent(
            $schedule,
            $this->asesi,
            'join',
            'Persetujuan kehadiran',
            'SIG-BUDI'
        );

        // 1. Early entry test (Before 2026-11-10)
        Carbon::setTestNow('2026-11-09 10:00:00');

        $earlyResponse = $this->post(route('asesi.assessment.enter-exam', $assessment->id));
        $earlyResponse->assertSessionHas('error');
        $this->assertEquals(0, AttendanceEvent::count());

        // 2. Late entry test (After 2026-11-12)
        Carbon::setTestNow('2026-11-13 10:00:00');

        $lateResponse = $this->post(route('asesi.assessment.enter-exam', $assessment->id));
        $lateResponse->assertSessionHas('error');
        $this->assertEquals(0, AttendanceEvent::count());

        Carbon::setTestNow();
    }

    public function test_valid_enter_exam_records_attendance_and_duplicate_post_is_idempotent(): void
    {
        Carbon::setTestNow('2026-11-11 09:30:00');

        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');
        $assessment = $this->createEligibleAssessment($schedule);

        $this->actingAs($this->asesi);

        $this->consentService->recordConsent(
            $schedule,
            $this->asesi,
            'join',
            'Persetujuan pelaksanaan',
            'SIG-BUDI'
        );

        // First POST enter exam
        $response1 = $this->post(route('asesi.assessment.enter-exam', $assessment->id));
        $response1->assertRedirect(route('asesi.assessment.ruang-uji', $assessment->id));
        $response1->assertSessionHas('success');

        $this->assertEquals(1, AttendanceEvent::count());
        $event = AttendanceEvent::first();
        $this->assertEquals($schedule->id, $event->schedule_id);
        $this->assertEquals($this->asesi->id, $event->user_id);
        $this->assertEquals('asesi_enter_exam', $event->event_type);
        $this->assertFalse($event->is_reversed);
        $this->assertEquals('2026-11-11 09:30:00', $event->occurred_at->toDateTimeString());

        $assessment->refresh();
        $this->assertNotNull($assessment->attendance_at);

        // Duplicate POST enter exam (e.g. page refresh / double click)
        $response2 = $this->post(route('asesi.assessment.enter-exam', $assessment->id));
        $response2->assertRedirect(route('asesi.assessment.ruang-uji', $assessment->id));

        // Must remain exactly 1 event (idempotent!)
        $this->assertEquals(1, AttendanceEvent::count());

        Carbon::setTestNow();
    }

    public function test_assessor_attendance_recorded_on_active_session(): void
    {
        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');

        $event1 = $this->attendanceService->recordAssessorActive(
            $schedule,
            $this->assessor,
            'session_supervision:start'
        );

        $this->assertEquals('assessor_session_active', $event1->event_type);
        $this->assertEquals($this->assessor->id, $event1->user_id);
        $this->assertEquals(1, AttendanceEvent::count());

        // Repeated call is idempotent
        $event2 = $this->attendanceService->recordAssessorActive(
            $schedule,
            $this->assessor,
            'instrument:save:IA01'
        );

        $this->assertEquals($event1->id, $event2->id);
        $this->assertEquals(1, AttendanceEvent::count());
    }

    public function test_attendance_reversal_is_auditable_and_preserves_provenance(): void
    {
        Carbon::setTestNow('2026-11-11 09:00:00');

        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');
        $assessment = $this->createEligibleAssessment($schedule);

        $this->consentService->recordConsent($schedule, $this->asesi, 'join', 'Pernyataan', 'SIG');
        $this->attendanceService->recordAsesiEnterExam($assessment);

        $event = AttendanceEvent::first();
        $this->assertFalse($event->is_reversed);

        // Non-admin cannot reverse -> redirected with error
        $this->actingAs($this->asesi);
        $failResponse = $this->post(route('admin.schedules.attendance.reverse', [$schedule->id, $event->id]), [
            'reason' => 'Salah sesi',
        ]);
        $failResponse->assertRedirect(route('asesi.dashboard'));

        // Admin reverses attendance
        $this->actingAs($this->admin);
        $okResponse = $this->post(route('admin.schedules.attendance.reverse', [$schedule->id, $event->id]), [
            'reason' => 'Peserta salah memasuki jadwal ruang uji',
        ]);
        $okResponse->assertSessionHas('success');

        $event->refresh();
        $this->assertTrue($event->is_reversed);
        $this->assertEquals('Peserta salah memasuki jadwal ruang uji', $event->reversal_reason);
        $this->assertEquals($this->admin->id, $event->reversed_by);
        $this->assertNotNull($event->reversed_at);

        // Provenance preserved: record was NOT deleted
        $this->assertEquals(1, AttendanceEvent::count());

        // Assessment attendance_at cleared
        $assessment->refresh();
        $this->assertNull($assessment->attendance_at);

        // Audit event recorded
        $this->assertTrue(
            AuditEvent::where('action', 'attendance.reversed')->exists()
        );

        Carbon::setTestNow();
    }

    public function test_consent_revocation_is_auditable(): void
    {
        $schedule = $this->createSchedule('2026-11-10', '2026-11-12');

        $consent = $this->consentService->recordConsent(
            $schedule,
            $this->asesi,
            'exam_entry',
            'Persetujuan masuk ruang ujian',
            'SIG-BUDI'
        );

        $this->assertTrue($this->consentService->hasActiveConsent($schedule, $this->asesi, 'exam_entry'));

        // Revoke consent
        $this->consentService->revokeConsent(
            $consent,
            $this->admin,
            'Ditemukan indikasi ketidaksesuaian identitas'
        );

        $consent->refresh();
        $this->assertNotNull($consent->revoked_at);
        $this->assertEquals($this->admin->id, $consent->revoked_by);
        $this->assertEquals('Ditemukan indikasi ketidaksesuaian identitas', $consent->revocation_reason);

        // Active consent is now false
        $this->assertFalse($this->consentService->hasActiveConsent($schedule, $this->asesi, 'exam_entry'));

        $this->assertTrue(
            AuditEvent::where('action', 'consent.revoked')->exists()
        );
    }
}
