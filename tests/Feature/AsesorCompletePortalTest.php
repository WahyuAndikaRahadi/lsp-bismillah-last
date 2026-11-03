<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AsesorProfile;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Models\Standard;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsesorCompletePortalTest extends TestCase
{
    use RefreshDatabase;

    private User $asesorUser;

    private AsesorProfile $asesorProfile;

    private CertificationScheme $scheme;

    private Tuk $tuk;

    private AssessmentSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->asesorUser = User::factory()->create([
            'role' => UserRole::Asesor,
            'name' => 'Abu, S.T.',
            'email' => 'abu.asesor@lsp.test',
            'nik' => '3504010710670002',
            'phone' => '081234567890',
        ]);

        $this->asesorProfile = AsesorProfile::create([
            'user_id' => $this->asesorUser->id,
            'no_met' => 'MET.000.00000.0020',
            'is_active' => true,
            'masa_berlaku' => now()->addYears(3),
        ]);

        $standard = Standard::create([
            'kode_standar' => 'SKKNI-2020-001',
            'judul_standar' => 'Standar Kompetensi TKJ',
            'jenis_standar' => 'SKKNI',
        ]);

        $this->scheme = CertificationScheme::create([
            'standard_id' => $standard->id,
            'kode_skema' => 'LEVEL II TKJ',
            'nama_skema' => 'Teknik Komputer dan Jaringan',
            'jenis_skema' => 'KKNI',
            'is_active' => true,
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-TKJ-01',
            'nama_tuk' => 'TUK Lab Komputer Jaringan',
            'jenis_tuk' => 'Sewaktu',
            'alamat' => 'Jl. Uji Kompetensi No. 1',
            'is_active' => true,
        ]);

        $event = Event::create([
            'kode_event' => 'EVT-2026-001',
            'nama_event' => 'Sertifikasi Keahlian TKJ Siswa SMK',
            'tahun_anggaran' => 2026,
            'sumber_anggaran' => 'Mandiri',
            'tgl_mulai' => now()->addDays(5),
            'tgl_selesai' => now()->addDays(7),
        ]);

        $this->schedule = AssessmentSchedule::create([
            'event_id' => $event->id,
            'scheme_id' => $this->scheme->id,
            'tuk_id' => $this->tuk->id,
            'kode_jadwal' => 'SCH-TKJ-2026',
            'nama_jadwal' => 'Uji TKJ SMK',
            'tgl_asesmen' => now()->addDays(5),
            'kuota_peserta' => 20,
            'status' => 'direncanakan',
        ]);

        // Assign asesor to schedule
        $this->schedule->asesors()->attach($this->asesorUser->id, ['peran' => 'Ketua']);
    }

    public function test_asesor_can_access_dashboard_with_full_metrics(): void
    {
        $response = $this->actingAs($this->asesorUser)->get(route('asesor.dashboard'));
        $response->assertOk();
        $response->assertSee('MET.000.00000.0020');
        $response->assertSee('Abu, S.T.');
        $response->assertSee('Jadwal Uji Kompetensi');
    }

    public function test_asesor_can_view_assigned_schedules(): void
    {
        $response = $this->actingAs($this->asesorUser)->get(route('asesor.schedules.index'));
        $response->assertOk();
        $response->assertSee('SCH-TKJ-2026');
        $response->assertSee('Teknik Komputer dan Jaringan');
    }

    public function test_asesor_can_view_tuk_verifications_list_and_create_verification(): void
    {
        $response = $this->actingAs($this->asesorUser)->get(route('asesor.tuks.index'));
        $response->assertOk();

        // Submit verification
        $postResponse = $this->actingAs($this->asesorUser)->post(route('asesor.tuks.store'), [
            'tuk_id' => $this->tuk->id,
            'verification_date' => now()->toDateString(),
            'verification_type' => 'mandiri',
            'recommendation' => 'layak',
            'notes' => 'Fasilitas dan alat peraga lengkap sesuai skema.',
        ]);
        $postResponse->assertRedirect();
        $this->assertDatabaseHas('tuk_verifications', [
            'tuk_id' => $this->tuk->id,
            'recommendation' => 'layak',
        ]);
    }

    public function test_asesor_can_view_instrument_reviews_and_submit(): void
    {
        $response = $this->actingAs($this->asesorUser)->get(route('asesor.instruments.index'));
        $response->assertOk();

        $postResponse = $this->actingAs($this->asesorUser)->post(route('asesor.instruments.store'), [
            'scheme_id' => $this->scheme->id,
            'instrument_type' => 'mapa_01',
            'review_date' => now()->toDateString(),
            'recommendation' => 'disetujui',
            'notes' => 'Instrumen telah memenuhi kaidah VRFK.',
        ]);
        $postResponse->assertRedirect();
        $this->assertDatabaseHas('instrument_reviews', [
            'scheme_id' => $this->scheme->id,
            'instrument_type' => 'mapa_01',
        ]);
    }

    public function test_asesor_can_view_mkva_assignments(): void
    {
        $response = $this->actingAs($this->asesorUser)->get(route('asesor.mkva.index'));
        $response->assertOk();

        $postResponse = $this->actingAs($this->asesorUser)->post(route('asesor.mkva.store'), [
            'scheme_id' => $this->scheme->id,
            'schedule_id' => $this->schedule->id,
            'assignment_date' => now()->toDateString(),
            'findings' => 'Perangkat asesmen efektif mengukur kompetensi asesi.',
            'improvement_plan' => 'Perlu penambahan variasi soal studi kasus.',
        ]);
        $postResponse->assertRedirect();
        $this->assertDatabaseHas('mkva_assignments', [
            'scheme_id' => $this->scheme->id,
            'findings' => 'Perangkat asesmen efektif mengukur kompetensi asesi.',
        ]);
    }

    public function test_asesor_messages_route_is_retired(): void
    {
        $response = $this->actingAs($this->asesorUser)->get('/asesor/messages');
        $response->assertNotFound();
    }

    public function test_asesor_can_view_and_update_profile(): void
    {
        $response = $this->actingAs($this->asesorUser)->get(route('asesor.profile.show'));
        $response->assertOk();
        $response->assertSee('MET.000.00000.0020');

        $updateResponse = $this->actingAs($this->asesorUser)->put(route('asesor.profile.update'), [
            'phone' => '089999888777',
            'nik' => '3504010710670002',
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->asesorUser->id,
            'phone' => '089999888777',
        ]);
    }
}
