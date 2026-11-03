<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AsesorProfile;
use App\Models\Assessment;
use App\Models\AssessmentApl02Item;
use App\Models\AssessmentProof;
use App\Models\AuditEvent;
use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\Schedule;
use App\Models\ScheduleConsent;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\UnitElement;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoreAplWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_SIG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    private User $admin;
    private User $assessor;
    private User $asesi;
    private User $otherAsesi;
    private Tuk $tuk;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'full_name' => 'Admin LSP',
            'email' => 'admin@proindo.id',
            'status' => 'ACTIVE',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'full_name' => 'Asesor Senior Otomotif',
            'email' => 'asesor@proindo.id',
            'status' => 'ACTIVE',
        ]);
        AsesorProfile::create([
            'user_id' => $this->assessor->id,
            'no_met' => 'MET.66677.2026',
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Agus Pratama',
            'email' => 'agus@gmail.com',
            'status' => 'ACTIVE',
        ]);
        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201019901010001',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1998-01-01',
            'jenis_kelamin' => 'L',
            'alamat_rumah' => 'Jl. Otomotif No. 1 Bandung',
            'phone' => '+628111222333',
            'signature_data' => self::VALID_SIG,
        ]);

        $this->otherAsesi = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Rina Asesi',
            'email' => 'rina@gmail.com',
            'status' => 'ACTIVE',
        ]);
        UserProfile::create([
            'user_id' => $this->otherAsesi->id,
            'nik' => '3201019901010002',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1999-02-02',
            'jenis_kelamin' => 'P',
            'alamat_rumah' => 'Jl. Melati No. 2',
            'phone' => '+628222333444',
            'signature_data' => self::VALID_SIG,
        ]);

        $this->tuk = Tuk::firstOrCreate(
            ['kode_tuk' => 'TUK-APL-01'],
            ['nama_tuk' => 'TUK Pusat Otomotif', 'jenis_tuk' => 'Sewaktu', 'is_active' => true]
        );
    }

    private function createSchemeWithKuk(string $code, string $name): array
    {
        $scheme = Scheme::firstOrCreate(
            ['code' => $code],
            ['name' => $name, 'category' => 'Automotive', 'is_active' => true]
        );

        $version = SchemeVersion::firstOrCreate(
            ['scheme_id' => $scheme->id, 'version' => 1],
            ['code' => $code . '-V1', 'title' => 'SKKNI 2018', 'is_active' => true]
        );

        $unit = CompetencyUnit::firstOrCreate(
            ['code' => 'G.45OTO01.001.2'],
            ['scheme_id' => $scheme->id, 'title' => 'Melaksanakan Keselamatan dan Kesehatan Kerja Lingkungan', 'unit_type' => 'core']
        );

        $element = UnitElement::firstOrCreate(
            ['competency_unit_id' => $unit->id, 'sequence' => 1],
            ['title' => 'Standar K3 Pabrikan']
        );

        $kuk = Kuk::firstOrCreate(
            ['unit_element_id' => $element->id, 'code_number' => '1.1'],
            ['statement' => 'Peralatan pelindung diri disiapkan dan digunakan sesuai SOP.', 'sequence' => 1]
        );

        $schedule = Schedule::create([
            'scheme_id' => $scheme->id,
            'scheme_version_id' => $version->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-' . $code,
            'name' => 'Jadwal ' . $name,
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->name,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $schedule->assessors()->attach($this->assessor->id);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $scheme->id,
            'apl_status' => 'DRAFT',
            'status' => 'scheduled',
        ]);

        return [$scheme, $schedule, $assessment, $kuk];
    }

    public function test_apl_submission_with_alternative_basic_requirements(): void
    {
        [$scheme, $schedule, $assessment, $kuk] = $this->createSchemeWithKuk('SKM-01-2026', 'Sepeda Motor Karburator');

        $this->actingAs($this->asesi);

        // Alternative 1: IJAZAH_SMK
        $response1 = $this->post(route('asesi.assessment.apl.store', $assessment->id), [
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 300, 'application/pdf'),
            'pas_foto' => UploadedFile::fake()->image('foto.jpg'),
            'ijazah_smk' => UploadedFile::fake()->create('ijazah_smk.pdf', 500, 'application/pdf'),
            'kuks' => [
                $kuk->id => ['assessment_value' => 'K', 'selected_proof_type' => 'IJAZAH_SMK'],
            ],
            'statement_integrity' => '1',
            'signature_data' => self::VALID_SIG,
        ]);

        $response1->assertRedirect(route('asesi.dashboard'));
        $assessment->refresh();
        $this->assertEquals('SUBMITTED', $assessment->apl_status);
        $this->assertTrue($assessment->proofs()->where('proof_type', 'IJAZAH_SMK')->exists());

        // Alternative 2 on new assessment: SURAT_SISWA_AKTIF
        $assessment2 = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->otherAsesi->id,
            'scheme_id' => $scheme->id,
            'apl_status' => 'DRAFT',
        ]);

        $this->actingAs($this->otherAsesi);
        $response2 = $this->post(route('asesi.assessment.apl.store', $assessment2->id), [
            'ktp' => UploadedFile::fake()->create('ktp2.pdf', 300, 'application/pdf'),
            'pas_foto' => UploadedFile::fake()->image('foto2.jpg'),
            'surat_siswa_aktif' => UploadedFile::fake()->create('surat_aktif.pdf', 400, 'application/pdf'),
            'kuks' => [
                $kuk->id => ['assessment_value' => 'K', 'selected_proof_type' => 'SURAT_SISWA_AKTIF'],
            ],
            'statement_integrity' => '1',
            'signature_data' => self::VALID_SIG,
        ]);

        $response2->assertRedirect(route('asesi.dashboard'));
        $assessment2->refresh();
        $this->assertEquals('SUBMITTED', $assessment2->apl_status);
        $this->assertTrue($assessment2->proofs()->where('proof_type', 'SURAT_SISWA_AKTIF')->exists());
    }

    public function test_apl_missing_required_evidence_rejected_with_per_field_errors(): void
    {
        [$scheme, $schedule, $assessment, $kuk] = $this->createSchemeWithKuk('SKM-01-2026', 'Sepeda Motor Karburator');

        $this->actingAs($this->asesi);

        // Submit without KTP and without basic requirement
        $response = $this->post(route('asesi.assessment.apl.store', $assessment->id), [
            'pas_foto' => UploadedFile::fake()->image('foto.jpg'),
            'kuks' => [
                $kuk->id => ['assessment_value' => 'K'],
            ],
            'statement_integrity' => '1',
            'signature_data' => self::VALID_SIG,
        ]);

        $response->assertSessionHasErrors(['ktp', 'proofs_dasar']);
        $assessment->refresh();
        $this->assertEquals('DRAFT', $assessment->apl_status);
    }

    public function test_apl_cross_user_file_reference_rejected(): void
    {
        [$scheme, $schedule, $assessment, $kuk] = $this->createSchemeWithKuk('SKM-01-2026', 'Sepeda Motor Karburator');

        // Other asesi has a proof on their own assessment
        $otherAssessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $this->otherAsesi->id,
            'scheme_id' => $scheme->id,
            'apl_status' => 'DRAFT',
        ]);

        $foreignProof = AssessmentProof::create([
            'assessment_id' => $otherAssessment->id,
            'proof_type' => 'KTP',
            'proof_category' => 'ADMINISTRATIF',
            'file_path' => 'proofs/foreign_ktp.pdf',
            'original_filename' => 'foreign_ktp.pdf',
        ]);

        $this->actingAs($this->asesi);

        // Agus attempts to inject Rina's proof_id
        $response = $this->post(route('asesi.assessment.apl.store', $assessment->id), [
            'proof_ids' => [$foreignProof->id],
            'pas_foto' => UploadedFile::fake()->image('foto.jpg'),
            'ijazah_smk' => UploadedFile::fake()->create('smk.pdf', 300, 'application/pdf'),
            'statement_integrity' => '1',
            'signature_data' => self::VALID_SIG,
        ]);

        $response->assertSessionHasErrors(['proof_ids']);
    }

    public function test_self_assessment_kbk_stored_distinct_from_assessor_finding(): void
    {
        [$scheme, $schedule, $assessment, $kuk] = $this->createSchemeWithKuk('SKM-01-2026', 'Sepeda Motor Karburator');

        $this->actingAs($this->asesi);

        $this->post(route('asesi.assessment.apl.store', $assessment->id), [
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 300, 'application/pdf'),
            'pas_foto' => UploadedFile::fake()->image('foto.jpg'),
            'ijazah_smk' => UploadedFile::fake()->create('ijazah.pdf', 400, 'application/pdf'),
            'kuks' => [
                $kuk->id => ['assessment_value' => 'K', 'selected_proof_type' => 'IJAZAH_SMK'],
            ],
            'statement_integrity' => '1',
            'signature_data' => self::VALID_SIG,
        ]);

        $apl02Item = AssessmentApl02Item::where('assessment_id', $assessment->id)->where('kuk_id', $kuk->id)->first();
        $this->assertNotNull($apl02Item);
        $this->assertEquals('K', $apl02Item->assessment_value);

        // Competency decision on Assessment remains untouched
        $assessment->refresh();
        $this->assertNotEquals('K', $assessment->final_result);
        $this->assertNull($assessment->rekomendasi_asesor);
    }

    public function test_assessor_review_and_revision_request_allows_resubmit(): void
    {
        [$scheme, $schedule, $assessment, $kuk] = $this->createSchemeWithKuk('SKM-01-2026', 'Sepeda Motor Karburator');

        // Asesi submits APL
        $assessment->update(['apl_status' => 'SUBMITTED', 'asesi_signature' => self::VALID_SIG]);
        AssessmentProof::create([
            'assessment_id' => $assessment->id,
            'proof_type' => 'KTP',
            'proof_category' => 'ADMINISTRATIF',
            'file_path' => 'proofs/ktp.pdf',
            'original_filename' => 'ktp.pdf',
        ]);
        AssessmentProof::create([
            'assessment_id' => $assessment->id,
            'proof_type' => 'PAS_FOTO',
            'proof_category' => 'ADMINISTRATIF',
            'file_path' => 'proofs/foto.jpg',
            'original_filename' => 'foto.jpg',
        ]);
        AssessmentProof::create([
            'assessment_id' => $assessment->id,
            'proof_type' => 'IJAZAH_SMK',
            'proof_category' => 'PERSYARATAN_DASAR',
            'file_path' => 'proofs/ijazah.pdf',
            'original_filename' => 'ijazah.pdf',
        ]);

        // Assessor reviews and requests revision
        $this->actingAs($this->assessor);
        $revResponse = $this->post(route('asesor.apl.decide', $assessment->id), [
            'action' => 'revision',
            'catatan' => 'KTP kurang jelas dan tidak terbaca nomor NIK-nya.',
        ]);

        $revResponse->assertRedirect(route('asesor.jadwal.asesi', $schedule->id));
        $assessment->refresh();
        $this->assertEquals('REVISION_REQUESTED', $assessment->apl_status);
        $this->assertStringContainsString('KTP kurang jelas', $assessment->apl_revision_notes);

        // Asesi re-submits with clearer KTP without losing earlier documents
        $this->actingAs($this->asesi);
        $reSubmitResponse = $this->post(route('asesi.assessment.apl.store', $assessment->id), [
            'ktp' => UploadedFile::fake()->create('ktp_jelas.pdf', 300, 'application/pdf'),
            'statement_integrity' => '1',
            'signature_data' => self::VALID_SIG,
        ]);

        $reSubmitResponse->assertRedirect(route('asesi.dashboard'));
        $assessment->refresh();
        $this->assertEquals('SUBMITTED', $assessment->apl_status);

        // Previous documents still preserved
        $this->assertTrue($assessment->proofs()->where('proof_type', 'IJAZAH_SMK')->exists());
        $this->assertTrue($assessment->proofs()->where('proof_type', 'PAS_FOTO')->exists());
    }

    public function test_assessor_approval_does_not_fabricate_ak01_consent_or_ak07(): void
    {
        [$scheme, $schedule, $assessment, $kuk] = $this->createSchemeWithKuk('SKM-01-2026', 'Sepeda Motor Karburator');

        $assessment->update(['apl_status' => 'SUBMITTED']);

        $this->actingAs($this->assessor);
        $appResponse = $this->post(route('asesor.apl.decide', $assessment->id), [
            'action' => 'approve',
            'rekomendasi' => 'Diterima sebagai peserta sertifikasi',
        ]);

        $appResponse->assertRedirect(route('asesor.jadwal.asesi', $schedule->id));
        $assessment->refresh();

        $this->assertEquals('APPROVED', $assessment->apl_status);
        $this->assertNotNull($assessment->approved_at);

        // MUST NOT fabricate AK01 consent or agreement
        $this->assertNull($assessment->ak01_agreed_at);
        $this->assertNull($assessment->esign_consent_at);
        $this->assertEquals(0, ScheduleConsent::where('schedule_id', $schedule->id)->count());

        // MUST NOT auto-create AK-07 records
        $this->assertEquals(0, $assessment->ak07Items()->count());

        // Audit event recorded
        $this->assertTrue(
            AuditEvent::where('action', 'apl.approved')->where('subject_id', $assessment->id)->exists()
        );
    }

    public function test_seven_schemes_apl_workflow_compatibility(): void
    {
        $schemes = [
            'SKM-01-2026' => 'Sepeda Motor Karburator',
            'SKM-02-2026' => 'Sepeda Motor Injeksi',
            'SKM-03-2026' => 'Sepeda Motor CVT',
            'SKM-04-2026' => 'Sepeda Motor Sistem Rem',
            'SKM-05-2026' => 'Sepeda Motor Kelistrikan',
            'SKM-06-2026' => 'Sepeda Motor Tune Up',
            'SKM-07-2026' => 'Sepeda Motor Rangka & Kemudi',
        ];

        foreach ($schemes as $code => $name) {
            [$scheme, $schedule, $assessment, $kuk] = $this->createSchemeWithKuk($code, $name);

            $this->actingAs($this->asesi);
            $res = $this->post(route('asesi.assessment.apl.store', $assessment->id), [
                'ktp' => UploadedFile::fake()->create('ktp.pdf', 300, 'application/pdf'),
                'pas_foto' => UploadedFile::fake()->image('foto.jpg'),
                'ijazah_smk' => UploadedFile::fake()->create('ijazah.pdf', 400, 'application/pdf'),
                'kuks' => [
                    $kuk->id => ['assessment_value' => 'K', 'selected_proof_type' => 'IJAZAH_SMK'],
                ],
                'statement_integrity' => '1',
                'signature_data' => self::VALID_SIG,
            ]);
            $res->assertRedirect(route('asesi.dashboard'));

            $assessment->refresh();
            $this->assertEquals('SUBMITTED', $assessment->apl_status);

            $this->actingAs($this->assessor);
            $decRes = $this->post(route('asesor.apl.decide', $assessment->id), [
                'action' => 'approve',
            ]);
            $decRes->assertRedirect(route('asesor.jadwal.asesi', $schedule->id));

            $assessment->refresh();
            $this->assertEquals('APPROVED', $assessment->apl_status);
            $this->assertNull($assessment->ak01_agreed_at);
        }
    }
}
