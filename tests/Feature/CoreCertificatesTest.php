<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\CertificateReceiptEvent;
use App\Models\CertificationDecision;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoreCertificatesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assessor;
    private User $asesi1;
    private User $asesi2;
    private Tuk $tuk;
    private Scheme $scheme1;
    private Scheme $scheme2;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('private');

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP ProIndo',
            'email' => 'admin.cert@lsp.test',
        ]);

        $this->assessor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'name' => 'Asesor Kompetensi',
            'email' => 'assessor.cert@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Satu',
            'email' => 'asesi1.cert@lsp.test',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Dua',
            'email' => 'asesi2.cert@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-CERT-01',
            'nama_tuk' => 'TUK Uji Mandiri',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->scheme1 = Scheme::firstOrCreate(
            ['code' => 'SKM-CERT-01'],
            ['name' => 'Pemeliharaan Engine Otomotif', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->scheme2 = Scheme::firstOrCreate(
            ['code' => 'SKM-CERT-02'],
            ['name' => 'Teknisi Sistem Kelistrikan', 'category' => 'Automotive', 'is_active' => true]
        );
    }

    private function createCompletedAssessment(User $asesi, Scheme $scheme, string $decisionResult = 'KOMPETEN'): array
    {
        $version = SchemeVersion::firstOrCreate(
            ['scheme_id' => $scheme->id, 'version' => 1],
            ['code' => $scheme->code . '-V1', 'title' => 'SKKNI 2020', 'is_active' => true]
        );

        $schedule = Schedule::create([
            'scheme_id' => $scheme->id,
            'scheme_version_id' => $version->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-' . rand(1000, 9999),
            'name' => 'Jadwal ' . $scheme->name,
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $asesi->id,
            'scheme_id' => $scheme->id,
            'assessor_id' => $this->assessor->id,
            'apl_status' => 'APPROVED',
            'final_result' => $decisionResult,
            'rekomendasi_asesor' => $decisionResult === 'KOMPETEN' ? 'K' : 'BK',
            'pleno_decision' => $decisionResult,
            'status' => 'COMPLETED',
        ]);

        $decision = CertificationDecision::create([
            'assessment_id' => $assessment->id,
            'schedule_id' => $schedule->id,
            'decision' => $decisionResult,
            'decided_by' => $this->admin->id,
            'authority_role' => 'Ketua Komite Teknis',
            'decided_at' => now(),
            'is_superseded' => false,
            'status' => 'FINAL',
        ]);

        return [$schedule, $assessment, $decision];
    }

    public function test_multi_certificate_ownership_across_different_schemes_and_private_access_control(): void
    {
        // Asesi 1 lulus di 2 skema berbeda
        [$sch1, $assessment1, $dec1] = $this->createCompletedAssessment($this->asesi1, $this->scheme1, 'KOMPETEN');
        [$sch2, $assessment2, $dec2] = $this->createCompletedAssessment($this->asesi1, $this->scheme2, 'KOMPETEN');

        // Admin mengunggah sertifikat untuk kedua assessment
        $this->actingAs($this->admin);

        $file1 = UploadedFile::fake()->create('sertifikat_1.pdf', 300, 'application/pdf');
        $file2 = UploadedFile::fake()->create('sertifikat_2.pdf', 400, 'application/pdf');

        $this->post(route('admin.assessment.certificate.store', $assessment1->id), [
            'certificate_number' => 'CERT-SKM1-001',
            'blanko_number' => 'BLK-001',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $file1,
        ])->assertRedirect(route('admin.certificates.index'));

        $this->post(route('admin.assessment.certificate.store', $assessment2->id), [
            'certificate_number' => 'CERT-SKM2-002',
            'blanko_number' => 'BLK-002',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $file2,
        ])->assertRedirect(route('admin.certificates.index'));

        $cert1 = Certificate::where('certificate_number', 'CERT-SKM1-001')->firstOrFail();
        $cert2 = Certificate::where('certificate_number', 'CERT-SKM2-002')->firstOrFail();

        // Asesi 1 login: melihat kedua sertifikat di halamannya
        $this->actingAs($this->asesi1);
        $response = $this->get(route('asesi.certificates.index'));
        $response->assertOk();
        $response->assertSee('CERT-SKM1-001');
        $response->assertSee('CERT-SKM2-002');
        $response->assertSee('Pemeliharaan Engine Otomotif');
        $response->assertSee('Teknisi Sistem Kelistrikan');

        // Asesi 1 dapat mengunduh sertifikat miliknya
        $download1 = $this->get(route('asesi.certificates.download', $cert1->id));
        $download1->assertOk();

        // Asesi 2 mencoba mengunduh sertifikat milik Asesi 1 -> Ditolak 403
        $this->actingAs($this->asesi2);
        $downloadFail = $this->get(route('asesi.certificates.download', $cert1->id));
        $downloadFail->assertForbidden();

        // Asesor tidak berhak mengunduh sertifikat (Gate policy menolak)
        $this->assertFalse(\Illuminate\Support\Facades\Gate::forUser($this->assessor)->allows('download', $cert1));
        $this->assertFalse(\Illuminate\Support\Facades\Gate::forUser($this->assessor)->allows('view', $cert1));

        // Asesor mengakses rute asesi juga dialihkan oleh role protection
        $this->actingAs($this->assessor);
        $this->get(route('asesi.certificates.download', $cert1->id))->assertRedirect();
    }

    public function test_upload_blocked_for_assessment_without_valid_kompeten_decision(): void
    {
        // Assessment dengan hasil BELUM_KOMPETEN
        [$sch, $assessment, $dec] = $this->createCompletedAssessment($this->asesi1, $this->scheme1, 'BELUM_KOMPETEN');

        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->create('sertifikat_bk.pdf', 300, 'application/pdf');

        $response = $this->post(route('admin.assessment.certificate.store', $assessment->id), [
            'certificate_number' => 'CERT-ILLEGAL-001',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Tidak ada sertifikat atau event tanda terima yang tercipta
        $this->assertEquals(0, Certificate::where('assessment_id', $assessment->id)->count());
        $this->assertEquals(0, CertificateReceiptEvent::count());
    }

    public function test_duplicate_certificate_number_is_rejected(): void
    {
        [$sch1, $assessment1] = $this->createCompletedAssessment($this->asesi1, $this->scheme1, 'KOMPETEN');
        [$sch2, $assessment2] = $this->createCompletedAssessment($this->asesi2, $this->scheme2, 'KOMPETEN');

        $this->actingAs($this->admin);

        $file1 = UploadedFile::fake()->create('cert1.pdf', 200, 'application/pdf');
        $this->post(route('admin.assessment.certificate.store', $assessment1->id), [
            'certificate_number' => 'CERT-DUPLICATE-TEST',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $file1,
        ])->assertRedirect(route('admin.certificates.index'));

        // Coba unggah nomor yang sama untuk assessment2
        $file2 = UploadedFile::fake()->create('cert2.pdf', 200, 'application/pdf');
        $response = $this->post(route('admin.assessment.certificate.store', $assessment2->id), [
            'certificate_number' => 'CERT-DUPLICATE-TEST',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $file2,
        ]);

        $response->assertSessionHasErrors('certificate_number');
        $this->assertEquals(1, Certificate::where('certificate_number', 'CERT-DUPLICATE-TEST')->count());
    }

    public function test_mime_spoofing_and_dangerous_file_rejected_without_fake_receipt(): void
    {
        [$sch, $assessment] = $this->createCompletedAssessment($this->asesi1, $this->scheme1, 'KOMPETEN');

        $this->actingAs($this->admin);

        // Berkas teks disamarkan jadi .exe atau mime berbahaya
        $fakeFile = UploadedFile::fake()->create('malicious.exe', 100);

        $response = $this->post(route('admin.assessment.certificate.store', $assessment->id), [
            'certificate_number' => 'CERT-MALICIOUS-001',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $fakeFile,
        ]);

        $response->assertSessionHasErrors('certificate_file');
        $this->assertEquals(0, Certificate::count());
        $this->assertEquals(0, CertificateReceiptEvent::count());
    }

    public function test_certificate_replacement_supersedes_prior_certificate_retaining_history(): void
    {
        [$sch, $assessment] = $this->createCompletedAssessment($this->asesi1, $this->scheme1, 'KOMPETEN');

        $this->actingAs($this->admin);

        // 1. Unggah awal (Revisi 1)
        $file1 = UploadedFile::fake()->create('cert_rev1.pdf', 300, 'application/pdf');
        $this->post(route('admin.assessment.certificate.store', $assessment->id), [
            'certificate_number' => 'CERT-REV-001',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $file1,
        ]);

        $cert1 = Certificate::where('certificate_number', 'CERT-REV-001')->firstOrFail();
        $this->assertEquals('ACTIVE', $cert1->status);
        $this->assertEquals(1, $cert1->revision);

        // 2. Penggantian sertifikat (Revisi 2)
        $file2 = UploadedFile::fake()->create('cert_rev2.pdf', 350, 'application/pdf');
        $this->post(route('admin.assessment.certificate.store', $assessment->id), [
            'certificate_number' => 'CERT-REV-002',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $file2,
        ]);

        $cert1->refresh();
        $cert2 = Certificate::where('certificate_number', 'CERT-REV-002')->firstOrFail();

        // Cert 1 menjadi SUPERSEDED
        $this->assertEquals('SUPERSEDED', $cert1->status);
        $this->assertEquals(1, $cert1->revision);

        // Cert 2 menjadi ACTIVE dan revision 2
        $this->assertEquals('ACTIVE', $cert2->status);
        $this->assertEquals(2, $cert2->revision);

        // Total 2 record di database tersimpan utuh
        $this->assertEquals(2, Certificate::where('assessment_id', $assessment->id)->count());
    }

    public function test_download_records_audit_event_and_explicit_acknowledgment_fr102(): void
    {
        [$sch, $assessment] = $this->createCompletedAssessment($this->asesi1, $this->scheme1, 'KOMPETEN');

        $this->actingAs($this->admin);
        $file = UploadedFile::fake()->create('cert_audit.pdf', 250, 'application/pdf');
        $this->post(route('admin.assessment.certificate.store', $assessment->id), [
            'certificate_number' => 'CERT-AUDIT-001',
            'issue_date' => now()->toDateString(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYears(3)->toDateString(),
            'certificate_file' => $file,
        ]);

        $cert = Certificate::where('certificate_number', 'CERT-AUDIT-001')->firstOrFail();

        // 1. Asesi download -> menghasilkan event download
        $this->actingAs($this->asesi1);
        $this->get(route('asesi.certificates.download', $cert->id))->assertOk();

        $this->assertDatabaseHas('t_certificate_receipt_events', [
            'certificate_id' => $cert->id,
            'recipient_id' => $this->asesi1->id,
            'event_type' => 'download',
        ]);

        // 2. Asesi submit tanda terima (FR-10.2: Terima sertifikat)
        $this->assertFalse($cert->isAcknowledged());

        $response = $this->post(route('asesi.certificates.acknowledge', $cert->id), [
            'signature_data' => 'data:image/png;base64,sample_signature',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $cert->refresh();
        $this->assertTrue($cert->isAcknowledged());
        $this->assertNotNull($cert->acknowledgment());
        $this->assertEquals($this->asesi1->id, $cert->acknowledgment()->recipient_id);
        $this->assertEquals('acknowledge', $cert->acknowledgment()->event_type);
    }
}
