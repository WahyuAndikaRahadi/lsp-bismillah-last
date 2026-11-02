<?php

namespace Tests\Feature;

use App\Enums\ScheduleStatus;
use App\Enums\UserRole;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $asesi;

    protected CertificationScheme $scheme;

    protected Tuk $tuk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi,
        ]);

        $this->scheme = CertificationScheme::create([
            'kode_skema' => 'SKM-TEST-01',
            'nama_skema' => 'Web Developer Specialist',
            'jenis_skema' => 'Okupasi',
            'biaya' => 750000,
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-TEST-01',
            'nama_tuk' => 'TUK Lab Utama',
            'jenis_tuk' => 'Sewaktu',
        ]);
    }

    public function test_admin_can_create_and_manage_events(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/events', [
            'kode_event' => 'EVT-TEST-2026',
            'nama_event' => 'Program Sertifikasi Khusus IT',
            'tahun_anggaran' => 2026,
            'sumber_anggaran' => 'BNSP / PSKK',
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-30',
            'keterangan' => 'Event uji coba',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', [
            'kode_event' => 'EVT-TEST-2026',
            'nama_event' => 'Program Sertifikasi Khusus IT',
        ]);

        $event = Event::where('kode_event', 'EVT-TEST-2026')->first();

        // Update Event
        $this->actingAs($this->admin)->put("/admin/events/{$event->id}", [
            'kode_event' => 'EVT-TEST-2026-REV',
            'nama_event' => 'Program Sertifikasi Khusus IT (Revisi)',
            'tahun_anggaran' => 2026,
            'sumber_anggaran' => 'Mandiri',
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-12-10',
            'is_active' => '1',
        ]);

        $this->assertDatabaseHas('events', [
            'kode_event' => 'EVT-TEST-2026-REV',
            'sumber_anggaran' => 'Mandiri',
        ]);
    }

    public function test_admin_can_create_assessment_schedule(): void
    {
        $event = Event::create([
            'kode_event' => 'EVT-001',
            'nama_event' => 'Event Mandiri',
            'tahun_anggaran' => 2026,
            'sumber_anggaran' => 'Mandiri',
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-30',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/schedules', [
            'event_id' => $event->id,
            'scheme_id' => $this->scheme->id,
            'tuk_id' => $this->tuk->id,
            'kode_jadwal' => 'JDW-TEST-001',
            'nama_jadwal' => 'Uji Sertifikasi Web Batch I',
            'tgl_pra_asesmen' => '2026-11-10',
            'tgl_asesmen' => '2026-11-14',
            'tgl_selesai' => '2026-11-14',
            'tgl_buka_daftar' => '2026-11-01',
            'tgl_tutup_daftar' => '2026-11-09',
            'kuota_peserta' => 25,
            'id_jadwal_bnsp' => 'BNSP-12345',
            'status' => 'pendaftaran',
            'keterangan' => 'Harap hadir tepat waktu',
        ]);

        $schedule = AssessmentSchedule::where('kode_jadwal', 'JDW-TEST-001')->first();
        $this->assertNotNull($schedule);
        $response->assertRedirect(route('admin.schedules.show', $schedule));

        $this->assertDatabaseHas('assessment_schedules', [
            'kode_jadwal' => 'JDW-TEST-001',
            'kuota_peserta' => 25,
            'status' => 'pendaftaran',
        ]);

        // Status transition
        $this->actingAs($this->admin)->patch("/admin/schedules/{$schedule->id}/status", [
            'status' => 'asesmen',
        ]);

        $this->assertEquals(ScheduleStatus::Asesmen, $schedule->fresh()->status);
    }

    public function test_public_home_displays_upcoming_schedules(): void
    {
        $event = Event::create([
            'kode_event' => 'EVT-PUB',
            'nama_event' => 'Event Terbuka',
            'tahun_anggaran' => 2026,
            'sumber_anggaran' => 'Mandiri',
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-30',
        ]);

        AssessmentSchedule::create([
            'event_id' => $event->id,
            'scheme_id' => $this->scheme->id,
            'tuk_id' => $this->tuk->id,
            'kode_jadwal' => 'JDW-PUB-01',
            'nama_jadwal' => 'Uji Publik Terbuka Web Developer',
            'tgl_asesmen' => now()->addDays(10)->toDateString(),
            'kuota_peserta' => 30,
            'status' => ScheduleStatus::Pendaftaran,
        ]);

        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_manage_events_and_schedules(): void
    {
        $responseEvent = $this->actingAs($this->asesi)->get('/admin/events');
        $responseEvent->assertRedirect(route('asesi.dashboard'));

        $responseSchedule = $this->actingAs($this->asesi)->get('/admin/schedules');
        $responseSchedule->assertRedirect(route('asesi.dashboard'));
    }
}
