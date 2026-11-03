<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\ScheduleStatus;
use App\Enums\UserRole;
use App\Models\AsesiProfile;
use App\Models\AsesorProfile;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Models\Standard;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteCrawlTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $asesor;
    private User $asesi;
    private AssessmentSchedule $schedule;
    private AssessmentApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'email' => 'admin@crawl.test',
        ]);

        $this->asesor = User::factory()->create([
            'role' => UserRole::Asesor,
            'email' => 'asesor@crawl.test',
        ]);
        AsesorProfile::create([
            'user_id' => $this->asesor->id,
            'no_met' => 'MET.000.12345',
            'is_active' => true,
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi,
            'email' => 'asesi@crawl.test',
        ]);
        AsesiProfile::create([
            'user_id' => $this->asesi->id,
            'nama_lengkap' => $this->asesi->name,
            'nik' => '3201010101010001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'alamat' => 'Jl. Merdeka No. 1',
        ]);

        $standard = Standard::create([
            'kode_standar' => 'SKKNI-CRAWL-001',
            'judul_standar' => 'Standar Uji Otomatis',
            'jenis_standar' => 'SKKNI',
        ]);

        $scheme = CertificationScheme::create([
            'standard_id' => $standard->id,
            'kode_skema' => 'SKEMA-CRAWL-01',
            'nama_skema' => 'Skema Otomatisasi Sistem',
            'jenis_skema' => 'KKNI',
            'biaya' => 500000,
            'is_active' => true,
        ]);

        $tuk = Tuk::create([
            'kode_tuk' => 'TUK-CRAWL-01',
            'nama_tuk' => 'TUK Utama LSP',
            'jenis_tuk' => 'sewaktu',
            'is_active' => true,
        ]);

        $event = Event::create([
            'kode_event' => 'EVT-CRAWL-2026',
            'nama_event' => 'Event Asesmen Nasional',
            'tahun_anggaran' => 2026,
            'sumber_anggaran' => 'APBN',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        $this->schedule = AssessmentSchedule::create([
            'event_id' => $event->id,
            'scheme_id' => $scheme->id,
            'tuk_id' => $tuk->id,
            'kode_jadwal' => 'JADWAL-CRAWL-01',
            'nama_jadwal' => 'Uji Kompetensi Nasional',
            'tgl_asesmen' => now()->addDay()->toDateString(),
            'kuota_peserta' => 30,
            'status' => ScheduleStatus::Asesmen,
        ]);

        $this->schedule->asesors()->attach($this->asesor->id);

        $this->application = AssessmentApplication::create([
            'nomor_pendaftaran' => AssessmentApplication::generateRegistrationNumber(),
            'user_id' => $this->asesi->id,
            'scheme_id' => $scheme->id,
            'schedule_id' => $this->schedule->id,
            'asesor_id' => $this->asesor->id,
            'tujuan_asesmen' => 'Sertifikasi Baru',
            'status' => ApplicationStatus::Diterima,
            'status_kelulusan' => 'kompeten',
            'no_sertifikat' => 'BNSP-2026-000123',
            'tgl_terbit_sertifikat' => now()->toDateString(),
            'masa_berlaku_sertifikat' => now()->addYears(3)->toDateString(),
        ]);
    }

    public function test_admin_routes_without_sql_errors(): void
    {
        $this->withoutExceptionHandling();
        $routes = [
            route('admin.dashboard'),
            route('admin.asesi.unscheduled'),
            route('admin.asesi.scheduled'),
            route('admin.asesi.competent'),
            route('admin.asesi.incompetent'),
            route('admin.reports.general'),
            route('admin.reports.monthly'),
            route('admin.reports.annual'),
            route('admin.reports.asesor-performance'),
            route('admin.reports.tuk-performance'),
            route('admin.reports.certificates'),
            route('admin.appeals.index'),
            route('admin.impartiality.index'),
            route('admin.surveillance.index'),
            route('admin.recertification.index'),
            route('admin.schemes.index'),
            route('admin.standards.index'),
            route('admin.tuks.index'),
            route('admin.asesors.index'),
            route('admin.events.index'),
            route('admin.schedules.index'),
            route('admin.verifications.index'),
            route('admin.pleno.index'),
            route('admin.jadwal.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->admin)->get($url);
            $this->assertLessThan(400, $response->getStatusCode(), "Failed accessing admin route: {$url}");
        }
    }

    public function test_asesor_routes_without_sql_errors(): void
    {
        $this->withoutExceptionHandling();
        $routes = [
            route('asesor.dashboard'),
            route('asesor.schedules.index'),
            route('asesor.schedules.show', $this->schedule),
            route('asesor.tuks.index'),
            route('asesor.instruments.index'),
            route('asesor.mkva.index'),
            route('asesor.profile.show'),
            route('asesor.apl02.index'),
            route('asesor.impartiality.commitment'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->asesor)->get($url);
            $this->assertLessThan(400, $response->getStatusCode(), "Failed accessing asesor route: {$url}");
        }
    }

    public function test_asesi_routes_without_sql_errors(): void
    {
        $this->withoutExceptionHandling();
        $routes = [
            route('asesi.dashboard'),
            route('asesi.applications.index'),
            route('asesi.applications.create'),
            route('asesi.applications.show', $this->application),
            route('asesi.certificates.index'),
            route('asesi.surveillance.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->asesi)->get($url);
            $this->assertLessThan(400, $response->getStatusCode(), "Failed accessing asesi route: {$url}");
        }
    }

    public function test_public_routes_without_sql_errors(): void
    {
        $this->withoutExceptionHandling();
        $routes = [
            route('home'),
            route('login'),
            route('register'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $this->assertLessThan(400, $response->getStatusCode(), "Failed accessing public route: {$url}");
        }
    }

    public function test_retired_routes_are_unavailable(): void
    {
        $retiredPaths = [
            '/pengaduan/create',
            '/pengaduan/tracking',
            '/surveillance',
            '/admin/quality',
            '/admin/finance',
            '/admin/letters',
            '/admin/messages',
            '/sertifikat/1',
            '/admin/exports/bnsp',
        ];

        foreach ($retiredPaths as $path) {
            $response = $this->actingAs($this->admin)->get($path);
            $this->assertEquals(404, $response->status(), "Path {$path} should return 404");
        }
    }
}
