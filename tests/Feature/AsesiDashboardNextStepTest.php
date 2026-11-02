<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\CompetencyUnit;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsesiDashboardNextStepTest extends TestCase
{
    use RefreshDatabase;

    protected User $asesi;
    protected User $asesor;
    protected Scheme $scheme;
    protected Schedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Admin LSP',
            'email' => 'admin_dash@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ADMIN_LSP',
            'is_active' => true,
        ]);

        $this->asesor = User::create([
            'name' => 'Asesor Dash',
            'email' => 'asesor_dash@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESOR',
            'is_active' => true,
        ]);

        $this->asesi = User::create([
            'name' => 'Asesi Fokus',
            'email' => 'asesi_dash@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201010101010099',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1998-08-17',
            'jenis_kelamin' => 'L',
            'signature_data' => 'data:image/png;base64,signature123',
        ]);

        $this->scheme = Scheme::create([
            'code' => 'SKM-OTO-DASH',
            'name' => 'Teknisi Kendaraan Ringan',
            'scheme_type' => 'KLASTER',
        ]);

        $this->schedule = Schedule::create([
            'code' => 'SCH-DASH-01',
            'name' => 'Jadwal Reguler LSP',
            'scheme_id' => $this->scheme->id,
            'tuk_name' => 'TUK Mandiri',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['AK01', 'IA01', 'IA05'],
            'created_by' => $admin->id,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_dashboard_shows_no_schedule_step_when_no_assessments(): void
    {
        $response = $this->actingAs($this->asesi)->get(route('asesi.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Langkah Berikutnya');
        $response->assertSee('Belum Terdaftar di Jadwal Asesmen');
        $response->assertSee('Gabung Jadwal Sekarang');
    }

    public function test_dashboard_shows_fill_apl_step_when_draft(): void
    {
        Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'DRAFT',
            'status' => 'REGISTERED',
        ]);

        $response = $this->actingAs($this->asesi)->get(route('asesi.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Langkah Berikutnya');
        $response->assertSee('Lengkapi Formulir APL-01 & APL-02');
        $response->assertSee('Isi Formulir APL-01 & APL-02');
        $response->assertSee('Sistem Asesmen Berbasis Modul MUK');
        $response->assertSee('FR.APL.01 & 02');
    }

    public function test_dashboard_shows_verification_step_when_submitted(): void
    {
        Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'SUBMITTED',
            'status' => 'REGISTERED',
        ]);

        $response = $this->actingAs($this->asesi)->get(route('asesi.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Langkah Berikutnya');
        $response->assertSee('Berkas Pendaftaran Sedang Diverifikasi');
        $response->assertSee('FR.APL.01 & 02');
    }

    public function test_dashboard_shows_ruang_uji_step_when_approved(): void
    {
        Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => now(),
            'status' => 'IN_PROGRESS',
        ]);

        $response = $this->actingAs($this->asesi)->get(route('asesi.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Langkah Berikutnya');
        $response->assertSee('Ruang Uji Kompetensi Aktif');
        $response->assertSee('Masuk Ruang Uji Kompetensi');
        $response->assertSee('Ruang Uji MUK');
        $response->assertSee('FR.AK.01');
    }

    public function test_dashboard_shows_completed_step_when_competent(): void
    {
        Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => now(),
            'final_result' => 'KOMPETEN',
            'status' => 'COMPLETED',
            'no_sertifikat' => 'BNSP-2026-9999',
        ]);

        $response = $this->actingAs($this->asesi)->get(route('asesi.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Langkah Berikutnya');
        $response->assertSee('Selamat! Anda Dinyatakan KOMPETEN');
        $response->assertSee('BNSP-2026-9999');
        $response->assertSee('Hasil & Sertifikat');
        $response->assertSee('Unduh Sertifikat');
    }
}
