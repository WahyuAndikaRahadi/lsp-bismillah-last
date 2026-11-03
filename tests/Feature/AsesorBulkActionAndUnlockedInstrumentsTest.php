<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsesorBulkActionAndUnlockedInstrumentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $asesor;
    protected User $asesi1;
    protected User $asesi2;
    protected Scheme $scheme;
    protected Schedule $schedule;
    protected Assessment $assessment1;
    protected Assessment $assessment2;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_bulk@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ADMIN_LSP',
            'is_active' => true,
        ]);

        $this->asesor = User::create([
            'name' => 'Asesor Penguji',
            'email' => 'asesor_bulk@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESOR',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesor->id,
            'nik' => '3201010101010011',
            'signature_data' => 'data:image/png;base64,asesorSignature==',
        ]);

        $this->asesi1 = User::create([
            'name' => 'Asesi Budi',
            'email' => 'budi_bulk@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesi1->id,
            'nik' => '3201010101010022',
            'signature_data' => 'data:image/png;base64,budiSignature==',
        ]);

        $this->asesi2 = User::create([
            'name' => 'Asesi Siti',
            'email' => 'siti_bulk@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesi2->id,
            'nik' => '3201010101010033',
            'signature_data' => 'data:image/png;base64,sitiSignature==',
        ]);

        $this->scheme = Scheme::create([
            'code' => 'SKM-OTO-BULK',
            'name' => 'Teknisi Injeksi Otomotif',
            'scheme_type' => 'KLASTER',
        ]);

        $this->schedule = Schedule::create([
            'code' => 'SCH-BLK-01',
            'name' => 'Jadwal Asesmen Bulk',
            'scheme_id' => $this->scheme->id,
            'tuk_name' => 'TUK Otomotif',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '17:00',
            'duration_minutes' => 540,
            'created_by' => $admin->id,
            'status' => 'ACTIVE',
        ]);

        $this->schedule->assessors()->attach($this->asesor->id);

        $this->assessment1 = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi1->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'SUBMITTED',
            'status' => 'REGISTERED',
        ]);

        $this->assessment2 = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi2->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'SUBMITTED',
            'status' => 'REGISTERED',
        ]);
    }

    public function test_asesor_can_bulk_approve_and_unlock_candidates(): void
    {
        $response = $this->actingAs($this->asesor)
            ->post(route('asesor.jadwal.asesi.bulk', $this->schedule->id), [
                'bulk_action' => 'approve_and_unlock',
                'assessment_ids' => [$this->assessment1->id, $this->assessment2->id],
            ]);

        $response->assertRedirect(route('asesor.jadwal.asesi', $this->schedule->id));
        $response->assertSessionHas('success');

        $this->assessment1->refresh();
        $this->assessment2->refresh();

        $this->assertEquals('APPROVED', $this->assessment1->apl_status);
        $this->assertEquals('APPROVED', $this->assessment2->apl_status);

        $this->assertNotNull($this->assessment1->ak01_agreed_at);
        $this->assertNotNull($this->assessment2->ak01_agreed_at);

        $this->assertEquals('data:image/png;base64,budiSignature==', $this->assessment1->asesi_signature);
        $this->assertEquals('data:image/png;base64,sitiSignature==', $this->assessment2->asesi_signature);
        $this->assertEquals('data:image/png;base64,asesorSignature==', $this->assessment1->assessor_signature);
        $this->assertEquals('data:image/png;base64,asesorSignature==', $this->assessment2->assessor_signature);

        $this->assertDatabaseHas('t_assessment_ak07_items', [
            'assessment_id' => $this->assessment1->id,
            'adjustment_category' => 'Standar',
        ]);
    }

    public function test_asesor_can_bulk_set_unlocked_instruments(): void
    {
        $response = $this->actingAs($this->asesor)
            ->post(route('asesor.jadwal.asesi.bulk', $this->schedule->id), [
                'bulk_action' => 'set_instruments',
                'assessment_ids' => [$this->assessment1->id],
                'instruments' => ['IA05'],
            ]);

        $response->assertRedirect(route('asesor.jadwal.asesi', $this->schedule->id));

        $this->assessment1->refresh();
        $this->assertEquals(['IA05'], $this->assessment1->unlocked_instruments);
        $this->assertEquals(['IA05'], $this->assessment1->getUnlockedInstruments());
        $this->assertTrue($this->assessment1->isInstrumentUnlocked('IA05'));
        $this->assertFalse($this->assessment1->isInstrumentUnlocked('IA01'));
    }

    public function test_asesi_ruang_uji_only_displays_unlocked_instruments(): void
    {
        // Approve candidate and only unlock IA05 (CBT)
        $this->assessment1->update([
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => now(),
            'unlocked_instruments' => ['IA05'],
        ]);

        $response = $this->actingAs($this->asesi1)
            ->get(route('asesi.assessment.ruang-uji', $this->assessment1));

        $response->assertStatus(200);
        $response->assertSee('FR.IA.05 Ujian Tertulis (CBT)');
        $response->assertDontSee('FR.IA.01 Ceklis Observasi Praktik');
        $response->assertDontSee('FR.IA.03 Pertanyaan Mendukung Observasi');
    }

    public function test_asesi_with_profile_signature_enters_ruang_uji_seamlessly(): void
    {
        // When asesor approves candidate via bulk action, AK01 is auto-agreed
        $this->actingAs($this->asesor)
            ->post(route('asesor.jadwal.asesi.bulk', $this->schedule->id), [
                'bulk_action' => 'approve_and_unlock',
                'assessment_ids' => [$this->assessment1->id],
            ]);

        $this->assessment1->refresh();
        $this->assertEquals('APPROVED', $this->assessment1->apl_status);
        $this->assertNotNull($this->assessment1->ak01_agreed_at);

        // Asesi enters Ruang Uji directly without any manual AK01 signature barrier
        $response = $this->actingAs($this->asesi1)
            ->get(route('asesi.assessment.ruang-uji', $this->assessment1));

        $response->assertStatus(200);
        $response->assertSee('Ruang Uji Asesi');
    }
}
