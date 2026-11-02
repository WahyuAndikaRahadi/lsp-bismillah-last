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

class AsesorUnifiedWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $asesor;
    protected User $otherAsesor;
    protected User $asesi;
    protected Scheme $scheme;
    protected Schedule $schedule;
    protected Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin LSP',
            'email' => 'admin_ws@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ADMIN_LSP',
            'is_active' => true,
        ]);

        $this->asesor = User::create([
            'name' => 'Asesor Penguji',
            'email' => 'asesor_ws@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESOR',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesor->id,
            'nik' => '3201010101010001',
            'signature_data' => 'data:image/png;base64,asesorSignatureData',
        ]);

        $this->otherAsesor = User::create([
            'name' => 'Asesor Lain',
            'email' => 'asesor_other@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESOR',
            'is_active' => true,
        ]);

        $this->asesi = User::create([
            'name' => 'Budi Peserta Uji',
            'email' => 'budi_ws@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201010101019999',
            'signature_data' => 'data:image/png;base64,asesiSignatureData',
        ]);

        $this->scheme = Scheme::create([
            'code' => 'SKM-OTO-WS',
            'name' => 'Pemeliharaan Kendaraan Ringan',
            'scheme_type' => 'KLASTER',
        ]);

        CompetencyUnit::create([
            'scheme_id' => $this->scheme->id,
            'code' => 'OTO.KR01.001.01',
            'title' => 'Melaksanakan Pemeliharaan Berkala 10.000 KM',
        ]);

        $this->schedule = Schedule::create([
            'code' => 'SCH-WS-01',
            'name' => 'Jadwal Asesmen Otomotif TUK Mandiri',
            'scheme_id' => $this->scheme->id,
            'tuk_name' => 'TUK Mandiri Otomotif',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['AK01', 'IA01', 'IA05'],
            'created_by' => $this->admin->id,
            'status' => 'ACTIVE',
        ]);

        // Assign asesor to schedule
        $this->schedule->assessors()->attach($this->asesor->id);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'SUBMITTED',
            'status' => 'REGISTERED',
            'asesi_signature' => 'data:image/png;base64,asesiSignatureData',
            'ak01_agreed_at' => now(),
        ]);
    }

    public function test_assigned_asesor_can_access_workspace(): void
    {
        $response = $this->actingAs($this->asesor)->get(route('asesor.assessment.workspace', $this->assessment->id));

        $response->assertStatus(200);
        $response->assertSee('Budi Peserta Uji');
        $response->assertSee('3201010101019999');
        $response->assertSee('SKM-OTO-WS');
        $response->assertSee('1. Review FR.APL (01 & 02)');
        $response->assertSee('2. FR.AK.01 Persetujuan');
        $response->assertSee('3. Teori CBT (FR.IA.05)');
        $response->assertSee('4. Observasi Praktik & PMO');
        $response->assertSee('5. FR.AK.02 Rekaman Asesmen');
    }

    public function test_unassigned_asesor_cannot_access_workspace(): void
    {
        $response = $this->actingAs($this->otherAsesor)->get(route('asesor.assessment.workspace', $this->assessment->id));

        $response->assertStatus(403);
    }

    public function test_asesor_can_approve_apl_from_workspace_action(): void
    {
        $response = $this->actingAs($this->asesor)->post(route('asesor.review-apl.update', $this->assessment->id), [
            'apl_status' => 'APPROVED',
            'notes' => 'Seluruh dokumen persyaratan lengkap dan valid.',
        ]);

        $response->assertRedirect();

        $this->assessment->refresh();
        $this->assertEquals('APPROVED', $this->assessment->apl_status);
        $this->assertNotNull($this->assessment->approved_at);
    }

    public function test_asesor_can_sign_ak01_from_workspace_action(): void
    {
        $response = $this->actingAs($this->asesor)->post(route('asesor.ak01.sign', $this->assessment->id), [
            'assessor_signature' => 'data:image/png;base64,asesorSignedCustom',
        ]);

        $response->assertRedirect();

        $this->assessment->refresh();
        $this->assertEquals('data:image/png;base64,asesorSignedCustom', $this->assessment->assessor_signature);
        $this->assertNotNull($this->assessment->assessor_signed_at);
    }

    public function test_asesor_can_update_ak02_recommendation_from_workspace_action(): void
    {
        $unit = $this->scheme->competencyUnits->first();

        // Must be approved first
        $this->assessment->update([
            'apl_status' => 'APPROVED',
            'status' => 'IN_PROGRESS',
        ]);

        $response = $this->actingAs($this->asesor)->post(route('asesor.assessment.ak02.update', $this->assessment->id), [
            'final_result' => 'KOMPETEN',
            'final_notes' => 'Kandidat menunjukkan unjuk kerja sangat baik.',
            'follow_up_action' => 'Diusulkan penerbitan sertifikat BNSP.',
            'units' => [
                $unit->id => [
                    'observasi_demonstrasi' => 1,
                    'tulis' => 1,
                    'is_competent' => 1,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assessment->refresh();
        $this->assertEquals('KOMPETEN', $this->assessment->final_result);
        $this->assertEquals('COMPLETED', $this->assessment->status);
    }
}
