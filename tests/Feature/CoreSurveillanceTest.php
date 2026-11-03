<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\CertificationDecision;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\SurveillancePlan;
use App\Models\SurveillanceReport;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoreSurveillanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $evaluator;
    private User $asesi1;
    private User $asesi2;
    private Tuk $tuk;
    private Scheme $scheme;
    private SchemeVersion $schemeVersion;
    private Certificate $certificate1;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('private');

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Admin LSP ProIndo',
            'email' => 'admin.srv@lsp.test',
        ]);

        $this->evaluator = User::factory()->create([
            'role' => UserRole::Admin->value,
            'name' => 'Evaluator Surveilans',
            'email' => 'evaluator.srv@lsp.test',
        ]);

        $this->asesi1 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Pemegang Sertifikat',
            'email' => 'asesi1.srv@lsp.test',
        ]);

        $this->asesi2 = User::factory()->create([
            'role' => UserRole::Asesi->value,
            'name' => 'Asesi Lain',
            'email' => 'asesi2.srv@lsp.test',
        ]);

        $this->tuk = Tuk::create([
            'kode_tuk' => 'TUK-SRV-01',
            'nama_tuk' => 'TUK Mandiri Surveilans',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->scheme = Scheme::firstOrCreate(
            ['code' => 'SKM-SRV-01'],
            ['name' => 'Teknisi Sepeda Motor Injeksi', 'category' => 'Automotive', 'is_active' => true]
        );

        $this->schemeVersion = SchemeVersion::firstOrCreate(
            ['scheme_id' => $this->scheme->id, 'version' => 1],
            ['code' => 'SKM-SRV-01-V1', 'title' => 'SKKNI 2020', 'is_active' => true]
        );

        $schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'tuk_id' => $this->tuk->id,
            'code' => 'SCH-SRV-01',
            'name' => 'Jadwal Reguler',
            'date' => now()->subYear()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'tuk_name' => $this->tuk->nama_tuk,
            'created_by' => $this->admin->id,
            'is_active' => true,
        ]);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'scheme_version_id' => $this->schemeVersion->id,
            'asesi_id' => $this->asesi1->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
            'rekomendasi_asesor' => 'K',
            'pleno_decision' => 'KOMPETEN',
            'status' => 'COMPLETED',
        ]);

        $decision = CertificationDecision::create([
            'assessment_id' => $assessment->id,
            'schedule_id' => $schedule->id,
            'decision' => 'KOMPETEN',
            'decided_by' => $this->admin->id,
            'authority_role' => 'Ketua Komite Teknis',
            'decided_at' => now()->subYear(),
            'status' => 'FINAL',
        ]);

        $this->certificate1 = Certificate::create([
            'assessment_id' => $assessment->id,
            'decision_id' => $decision->id,
            'certificate_number' => 'CERT-SRV-2026-001',
            'blanko_number' => 'BLK-SRV-001',
            'issue_date' => now()->subYear()->toDateString(),
            'valid_from' => now()->subYear()->toDateString(),
            'valid_until' => now()->addYears(2)->toDateString(),
            'private_file_path' => 'secure/certificates/test_cert.pdf',
            'file_hash' => hash('sha256', 'dummy'),
            'uploader_id' => $this->admin->id,
            'status' => 'ACTIVE',
            'revision' => 1,
        ]);
    }

    public function test_admin_can_create_periodic_surveillance_plan(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.surveillance.store-plan', $this->certificate1->id), [
            'title' => 'Surveilans Tahun ke-1: Pemeliharaan Kompetensi',
            'surveillance_year' => 1,
            'due_date' => now()->addMonths(3)->toDateString(),
            'required_evidence_description' => 'Logbook pekerjaan praktik minimal 10 kali dalam 6 bulan terakhir.',
        ]);

        $response->assertRedirect(route('admin.surveillance.index'));
        $response->assertSessionHas('success');

        $plan = SurveillancePlan::where('certificate_id', $this->certificate1->id)->firstOrFail();
        $this->assertEquals('SCHEDULED', $plan->status);
        $this->assertEquals(1, $plan->surveillance_year);
        $this->assertEquals($this->admin->id, $plan->created_by);
    }

    public function test_certificate_holder_submits_report_and_unauthorized_user_is_denied(): void
    {
        $plan = SurveillancePlan::create([
            'certificate_id' => $this->certificate1->id,
            'title' => 'Surveilans Tahun ke-1',
            'surveillance_year' => 1,
            'due_date' => now()->addMonths(2)->toDateString(),
            'required_evidence_description' => 'Logbook pekerjaan.',
            'status' => 'SCHEDULED',
            'created_by' => $this->admin->id,
        ]);

        // 1. Asesi 2 (bukan pemilik sertifikat) mencoba melihat/mengirim laporan -> Ditolak 403
        $this->actingAs($this->asesi2);
        $this->get(route('asesi.surveillance.show', $plan->id))->assertForbidden();

        $fakeFile = UploadedFile::fake()->create('logbook.pdf', 300, 'application/pdf');
        $this->post(route('asesi.surveillance.submit-report', $plan->id), [
            'activity_summary' => 'Mencoba submit untuk orang lain.',
            'evidence_file' => $fakeFile,
        ])->assertForbidden();

        // 2. Asesi 1 (pemilik sertifikat yang sah) dapat melihat dan mengirim laporan
        $this->actingAs($this->asesi1);
        $this->get(route('asesi.surveillance.show', $plan->id))->assertOk();

        $validFile = UploadedFile::fake()->create('logbook_asesi1.pdf', 350, 'application/pdf');
        $response = $this->post(route('asesi.surveillance.submit-report', $plan->id), [
            'activity_summary' => 'Telah melakukan perawatan berkala pada 15 unit sepeda motor injeksi di Bengkel Resmi.',
            'evidence_file' => $validFile,
        ]);

        $response->assertRedirect(route('asesi.surveillance.show', $plan->id));
        $response->assertSessionHas('success');

        $report = SurveillanceReport::where('surveillance_plan_id', $plan->id)->firstOrFail();
        $this->assertEquals($this->asesi1->id, $report->asesi_id);
        $this->assertEquals('SUBMITTED', $report->status);
        $this->assertNotNull($report->evidence_file_path);

        $plan->refresh();
        $this->assertEquals('REPORTED', $plan->status);
    }

    public function test_independent_evaluator_records_findings_and_explicit_decision(): void
    {
        $plan = SurveillancePlan::create([
            'certificate_id' => $this->certificate1->id,
            'title' => 'Surveilans Tahun ke-1',
            'surveillance_year' => 1,
            'due_date' => now()->addMonths(2)->toDateString(),
            'required_evidence_description' => 'Logbook pekerjaan.',
            'status' => 'REPORTED',
            'created_by' => $this->admin->id,
        ]);

        $report = SurveillanceReport::create([
            'surveillance_plan_id' => $plan->id,
            'certificate_id' => $this->certificate1->id,
            'asesi_id' => $this->asesi1->id,
            'activity_summary' => 'Kegiatan pemeliharaan berkala selama periode 1 tahun.',
            'submitted_at' => now(),
            'status' => 'SUBMITTED',
        ]);

        $this->actingAs($this->evaluator);

        $response = $this->post(route('admin.surveillance.evaluate-report', $report->id), [
            'evaluator_notes' => 'Portofolio pekerjaan logbook dan surat keterangan bekerja diverifikasi sah dan relevan.',
            'decision' => 'COMPLIANT',
            'decision_notes' => 'Kompetensi teknisi terpelihara dengan baik.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $report->refresh();
        $this->assertEquals('VERIFIED', $report->status);
        $this->assertEquals('COMPLIANT', $report->decision);
        $this->assertEquals($this->evaluator->id, $report->evaluator_id);
        $this->assertEquals($this->evaluator->id, $report->decided_by);

        $plan->refresh();
        $this->assertEquals('COMPLETED', $plan->status);

        // Sertifikat tetap aktif
        $this->certificate1->refresh();
        $this->assertEquals('ACTIVE', $this->certificate1->status);

        // Asesi 1 dapat melihat hasil keputusan tersebut
        $this->actingAs($this->asesi1);
        $resAsesi = $this->get(route('asesi.surveillance.show', $plan->id));
        $resAsesi->assertOk();
        $resAsesi->assertSee('COMPLIANT');
        $resAsesi->assertSee('Portofolio pekerjaan logbook');
    }

    public function test_suspension_decision_updates_certificate_status(): void
    {
        $plan = SurveillancePlan::create([
            'certificate_id' => $this->certificate1->id,
            'title' => 'Surveilans Tahun ke-1',
            'surveillance_year' => 1,
            'due_date' => now()->addMonths(2)->toDateString(),
            'required_evidence_description' => 'Logbook.',
            'status' => 'REPORTED',
            'created_by' => $this->admin->id,
        ]);

        $report = SurveillanceReport::create([
            'surveillance_plan_id' => $plan->id,
            'certificate_id' => $this->certificate1->id,
            'asesi_id' => $this->asesi1->id,
            'activity_summary' => 'Tidak ada aktivitas pemeliharaan.',
            'submitted_at' => now(),
            'status' => 'SUBMITTED',
        ]);

        $this->actingAs($this->admin);

        // Evaluator menetapkan pembekuan sertifikat (SUSPENDED)
        $this->post(route('admin.surveillance.evaluate-report', $report->id), [
            'evaluator_notes' => 'Asesi tidak menjalankan praktik profesi selama lebih dari 12 bulan.',
            'decision' => 'SUSPENDED',
            'decision_notes' => 'Sertifikat dibekukan sementara sesuai SOP 19.',
        ]);

        $report->refresh();
        $this->assertEquals('SUSPENDED', $report->decision);

        // Status sertifikat terupdate menjadi SUSPENDED
        $this->certificate1->refresh();
        $this->assertEquals('SUSPENDED', $this->certificate1->status);
    }
}
