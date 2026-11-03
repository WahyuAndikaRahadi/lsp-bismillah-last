<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\PrivateFile;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoreAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $asesorA;
    private User $asesorB;
    private User $asesi1;
    private User $asesi2;
    private Scheme $scheme;
    private Schedule $schedule;
    private Assessment $assessment1;
    private Assessment $assessment2;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        Storage::fake('local');

        $this->admin = User::factory()->create([
            'name' => 'Admin LSP',
            'email' => 'admin@lsp.test',
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->asesorA = User::factory()->create([
            'name' => 'Asesor A',
            'email' => 'asesor_a@lsp.test',
            'role' => UserRole::Asesor,
            'is_active' => true,
        ]);

        $this->asesorB = User::factory()->create([
            'name' => 'Asesor B',
            'email' => 'asesor_b@lsp.test',
            'role' => UserRole::Asesor,
            'is_active' => true,
        ]);

        $this->asesi1 = User::factory()->create([
            'name' => 'Asesi Satu',
            'email' => 'asesi1@lsp.test',
            'role' => UserRole::Asesi,
            'is_active' => true,
        ]);

        $this->asesi2 = User::factory()->create([
            'name' => 'Asesi Dua',
            'email' => 'asesi2@lsp.test',
            'role' => UserRole::Asesi,
            'is_active' => true,
        ]);

        $this->scheme = Scheme::create([
            'code' => 'SKM-AUTH-01',
            'name' => 'Skema Autorisasi',
            'scheme_type' => 'KLASTER',
        ]);

        $this->schedule = Schedule::create([
            'code' => 'SCH-AUTH-01',
            'name' => 'Jadwal Bersama',
            'scheme_id' => $this->scheme->id,
            'tuk_name' => 'TUK Utama',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '17:00',
            'duration_minutes' => 540,
            'selected_instruments' => ['IA01'],
            'created_by' => $this->admin->id,
            'status' => 'ACTIVE',
        ]);

        // Attach both assessors to the schedule
        $this->schedule->assessors()->attach([$this->asesorA->id, $this->asesorB->id]);

        // Assessment 1: Asesi 1 assigned to Asesor A
        $this->assessment1 = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi1->id,
            'assessor_id' => $this->asesorA->id,
            'status' => 'REGISTERED',
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_SELESAI',
        ]);

        // Assessment 2: Asesi 2 assigned to Asesor B
        $this->assessment2 = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi2->id,
            'assessor_id' => $this->asesorB->id,
            'status' => 'REGISTERED',
            'apl_status' => 'APPROVED',
            'final_result' => 'BELUM_SELESAI',
        ]);
    }

    public function test_guest_is_redirected_or_unauthorized_for_file_access(): void
    {
        $file = PrivateFile::create([
            'disk' => 'private',
            'path' => 'secure/test/sample.pdf',
            'original_name' => 'sample.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'file_hash' => hash('sha256', 'dummy'),
            'owner_type' => Assessment::class,
            'owner_id' => $this->assessment1->id,
            'uploaded_by' => $this->asesi1->id,
            'status' => 'available',
        ]);
        Storage::disk('private')->put('secure/test/sample.pdf', 'dummy');

        // Unauthenticated access redirects to login
        $response = $this->get(route('secure-file.show', $file->id));
        $response->assertRedirect(route('login'));
    }

    public function test_two_assessors_in_same_schedule_cannot_edit_each_others_assessments(): void
    {
        // Asesor A is authorized to update Assessment 1
        $this->assertTrue(Gate::forUser($this->asesorA)->allows('update', $this->assessment1));

        // Asesor B is NOT authorized to update Assessment 1 (even though they share the same schedule)
        $this->assertFalse(Gate::forUser($this->asesorB)->allows('update', $this->assessment1));

        // Asesor B is authorized to update Assessment 2
        $this->assertTrue(Gate::forUser($this->asesorB)->allows('update', $this->assessment2));

        // Asesor A is NOT authorized to update Assessment 2
        $this->assertFalse(Gate::forUser($this->asesorA)->allows('update', $this->assessment2));
    }

    public function test_matrix_authorization_for_private_assessment_files(): void
    {
        $file = PrivateFile::create([
            'disk' => 'private',
            'path' => 'secure/docs/ktp_asesi1.pdf',
            'original_name' => 'ktp_asesi1.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'file_hash' => hash('sha256', 'ktp-content'),
            'owner_type' => Assessment::class,
            'owner_id' => $this->assessment1->id,
            'uploaded_by' => $this->asesi1->id,
            'status' => 'available',
        ]);
        Storage::disk('private')->put('secure/docs/ktp_asesi1.pdf', 'ktp-content');

        // 1. Admin: OK
        $this->actingAs($this->admin);
        $this->get(route('secure-file.show', $file->id))->assertOk();

        // 2. Asesi 1 (Owner): OK
        $this->actingAs($this->asesi1);
        $this->get(route('secure-file.show', $file->id))->assertOk();

        // 3. Asesi 2 (Other candidate): Forbidden
        $this->actingAs($this->asesi2);
        $this->get(route('secure-file.show', $file->id))->assertForbidden();

        // 4. Asesor A (Assigned assessor): OK
        $this->actingAs($this->asesorA);
        $this->get(route('secure-file.show', $file->id))->assertOk();

        // 5. Asesor B (Schedule assessor, but not directly assigned to this assessment): Can view audit as schedule member
        $this->actingAs($this->asesorB);
        $this->get(route('secure-file.show', $file->id))->assertOk();
    }

    public function test_asesi_is_strictly_forbidden_from_viewing_answer_keys(): void
    {
        // 1. Policy direct check
        $this->assertFalse(Gate::forUser($this->asesi1)->allows('viewAnswerKey', $this->assessment1));
        $this->assertTrue(Gate::forUser($this->asesorA)->allows('viewAnswerKey', $this->assessment1));
        $this->assertTrue(Gate::forUser($this->admin)->allows('viewAnswerKey', $this->assessment1));

        // 2. Private file containing answer key
        $keyFile = PrivateFile::create([
            'disk' => 'private',
            'path' => 'secure/instruments/ia05_answer_key.pdf',
            'original_name' => 'ia05_answer_key.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'file_hash' => hash('sha256', 'answers'),
            'owner_type' => Assessment::class,
            'owner_id' => $this->assessment1->id,
            'uploaded_by' => $this->asesorA->id,
            'status' => 'available',
        ]);
        Storage::disk('private')->put('secure/instruments/ia05_answer_key.pdf', 'answers');

        // Asesi is forbidden even though it's on their assessment
        $this->actingAs($this->asesi1);
        $this->get(route('secure-file.show', $keyFile->id))->assertForbidden();

        // Asesor is allowed
        $this->actingAs($this->asesorA);
        $this->get(route('secure-file.show', $keyFile->id))->assertOk();
    }

    public function test_unknown_role_is_deactivated_or_rejected_and_cannot_access_portals(): void
    {
        $unknownUser = User::factory()->create([
            'name' => 'Mystery User',
            'email' => 'mystery@lsp.test',
            'role' => 'auditor_eksternal', // Not in {admin, asesor, asesi}
            'is_active' => true,
        ]);

        $this->assertFalse($unknownUser->isCanonicalRoleValid());
        $this->assertNull($unknownUser->canonicalRole());

        // Attempting to access admin portal is rejected
        $this->actingAs($unknownUser);
        $response = $this->get(route('admin.dashboard'));
        $this->assertTrue($response->isRedirect(route('login')));
    }

    public function test_public_registration_rejects_role_elevation_and_defaults_to_asesi(): void
    {
        $payload = [
            'name' => 'Pendaftar Baru',
            'email' => 'pendaftar_baru@example.com',
            'nik' => '3201010101010099',
            'phone' => '081299998888',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'terms' => '1',
            'role' => 'admin', // Malicious attempt to elevate role
            'user_id' => 1,
        ];

        $response = $this->post(route('register'), $payload);
        $response->assertRedirect(route('asesi.dashboard'));

        $newUser = User::where('email', 'pendaftar_baru@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals(UserRole::Asesi, $newUser->canonicalRole());
        $this->assertTrue($newUser->isAsesi());
        $this->assertFalse($newUser->isAdmin());
    }
}
