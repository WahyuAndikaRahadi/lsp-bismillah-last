<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\CertificationDecision;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreRecertificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $asesi1;
    private User $asesi2;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;
    private Schedule $oldSchedule;
    private Schedule $newSchedule;
    private Assessment $oldAssessment;
    private Certificate $oldCertificate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP ProIndo',
            'email' => 'admin.recert@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Pemilik Sertifikat',
            'email' => 'asesi1.recert@lsp.test',
        ]);

        UserProfile::create([
            'user_id' => $this->asesi1->id,
            'nik' => '3201234567890001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Otomotif No. 10 Jakarta',
            'phone' => '081234567890',
            'pekerjaan' => 'Teknisi Senior',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Lain',
            'email' => 'asesi2.recert@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-RCT-01',
            'nama_tuk' => 'TUK Mandiri Resertifikasi',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->scheme = Scheme::firstOrCreate(
            ['code' => 'SKM-RCT-01'],
            ['name' => 'Pemeliharaan Engine Injeksi', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->schemeVersion = SchemeVersion::firstOrCreate(
            ['scheme_id' => $this->scheme->id, 'version' => 1],
            ['code' => 'SKM-RCT-01-V1', 'title' => 'SKKNI 2020', 'is_active' => true]
        );

        // Jadwal lama yang telah selesai 3 tahun lalu
        $this->oldSchedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-OLD-01',
            'name' => 'Jadwal Uji 2023',
            'date' => now()->subYears(3)->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => false,
        ]);

        $this->oldAssessment = Assessment::create([
            'schedule_id' => $this->oldSchedule->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'asesi_id' => $this->asesi1->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
            'rekomendasi_asesor' => 'K',
            'pleno_decision' => 'KOMPETEN',
            'status' => 'COMPLETED',
        ]);

        $oldDecision = CertificationDecision::create([
            'assessment_id' => $this->oldAssessment->id,
            'schedule_id' => $this->oldSchedule->id,
            'decision' => 'KOMPETEN',
            'decided_by' => $this->admin->id,
            'authority_role' => 'Ketua Komite Teknis',
            'decided_at' => now()->subYears(3),
            'status' => 'FINAL',
        ]);

        $this->oldCertificate = Certificate::create([
            'assessment_id' => $this->oldAssessment->id,
            'decision_id' => $oldDecision->id,
            'certificate_number' => 'CERT-EXP-2023-999',
            'blanko_number' => 'BLK-2023-999',
            'issue_date' => now()->subYears(3)->toDateString(),
            'valid_from' => now()->subYears(3)->toDateString(),
            'valid_until' => now()->subDay()->toDateString(), // Baru saja kedaluwarsa
            'private_file_path' => 'secure/certificates/old_cert.pdf',
            'file_hash' => hash('sha256', 'dummy_old'),
            'uploader_id' => $this->admin->id,
            'status' => 'ACTIVE',
            'revision' => 1,
        ]);

        // Jadwal baru untuk resertifikasi
        $this->newSchedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-NEW-2026',
            'name' => 'Jadwal Resertifikasi 2026',
            'date' => now()->addMonth()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);
    }

    public function test_candidate_can_apply_for_recertification_from_prior_certificate(): void
    {
        $this->actingAs($this->asesi1);

        // 1. Tampilkan form resertifikasi
        $resShow = $this->get(route('asesi.certificates.recertify', $this->oldCertificate->id));
        $resShow->assertOk();
        $resShow->assertSee('CERT-EXP-2023-999');
        $resShow->assertSee('SCH-NEW-2026');

        // 2. Submit permohonan resertifikasi
        $response = $this->post(route('asesi.certificates.recertify.store', $this->oldCertificate->id), [
            'schedule_id' => $this->newSchedule->id,
            'notes' => 'Permohonan perpanjangan masa berlaku sertifikat kompetensi.',
        ]);

        $response->assertRedirect(route('asesi.jadwal.index'));
        $response->assertSessionHas('success');

        // 3. Verifikasi record assessment baru tercipta tertaut ke prior certificate
        $newAssessment = Assessment::where('prior_certificate_id', $this->oldCertificate->id)->firstOrFail();
        $this->assertEquals($this->asesi1->id, $newAssessment->asesi_id);
        $this->assertEquals($this->newSchedule->id, $newAssessment->schedule_id);
        $this->assertEquals('SUBMITTED', $newAssessment->apl_status);
        $this->assertEquals('PENDING', $newAssessment->status);

        // Snapshot profil menggunakan profil yang sudah ada (reusable profile)
        $this->assertNotNull($newAssessment->snapshot_profile);
        $this->assertEquals('3201234567890001', $newAssessment->snapshot_profile['nik']);
        $this->assertEquals('Jakarta', $newAssessment->snapshot_profile['tempat_lahir']);

        // 4. Sertifikat lama tetap utuh di database (tidak tertimpa atau terhapus)
        $this->oldCertificate->refresh();
        $this->assertEquals('CERT-EXP-2023-999', $this->oldCertificate->certificate_number);
        $this->assertEquals(2, Assessment::where('asesi_id', $this->asesi1->id)->count());
    }

    public function test_unauthorized_user_cannot_apply_recertification_for_others_certificate(): void
    {
        // Asesi 2 mencoba mendaftar resertifikasi menggunakan sertifikat milik Asesi 1 -> Ditolak 403
        $this->actingAs($this->asesi2);

        $responseView = $this->get(route('asesi.certificates.recertify', $this->oldCertificate->id));
        $responseView->assertForbidden();

        $responseSubmit = $this->post(route('asesi.certificates.recertify.store', $this->oldCertificate->id), [
            'schedule_id' => $this->newSchedule->id,
        ]);
        $responseSubmit->assertForbidden();

        // Tidak ada assessment baru yang tercipta untuk Asesi 2
        $this->assertEquals(0, Assessment::where('asesi_id', $this->asesi2->id)->count());
    }

    public function test_new_recertification_assessment_requires_formal_process_not_instant_bypass(): void
    {
        $this->actingAs($this->asesi1);

        $this->post(route('asesi.certificates.recertify.store', $this->oldCertificate->id), [
            'schedule_id' => $this->newSchedule->id,
        ]);

        $newAssessment = Assessment::where('prior_certificate_id', $this->oldCertificate->id)->firstOrFail();

        // Asesmen baru TIDAK langsung lulus (no auto-approval)
        $this->assertNotEquals('KOMPETEN', $newAssessment->final_result);
        $this->assertNotEquals('K', $newAssessment->rekomendasi_asesor);
        $this->assertNull($newAssessment->pleno_decision);
        $this->assertEquals('PENDING', $newAssessment->status);
        $this->assertEquals('SUBMITTED', $newAssessment->apl_status);
    }
}
