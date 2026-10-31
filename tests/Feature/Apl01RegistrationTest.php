<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentVerificationStatus;
use App\Enums\ScheduleStatus;
use App\Enums\UserRole;
use App\Models\ApplicationDocument;
use App\Models\AsesiProfile;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Models\SchemeRequirement;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Apl01RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $asesi;
    protected CertificationScheme $scheme;
    protected AssessmentSchedule $schedule;
    protected SchemeRequirement $requirement;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Mail::fake();

        // 1. Create Admin
        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'email' => 'admin@test.localhost',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Asesi
        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi,
            'name' => 'Budi Pratama',
            'nik' => '3171012304950001',
            'phone' => '081234567890',
            'email' => 'budi@test.localhost',
            'password' => Hash::make('password123'),
        ]);

        // 3. Create Scheme & Requirement
        $this->scheme = CertificationScheme::create([
            'kode_skema' => 'SKM-001',
            'nama_skema' => 'Junior Web Developer',
            'jenis_skema' => 'Okupasi',
            'is_active' => true,
        ]);

        $this->requirement = SchemeRequirement::create([
            'scheme_id' => $this->scheme->id,
            'nama_dokumen' => 'Kartu Tanda Penduduk (KTP)',
            'is_required' => true,
            'urutan' => 1,
        ]);

        // 4. Create Event, TUK, Schedule
        $event = Event::create([
            'kode_event' => 'EVT-2026-001',
            'nama_event' => 'PSKK BNSP Batch 1',
            'tahun_anggaran' => 2026,
            'sumber_anggaran' => 'BNSP / PSKK',
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-30',
        ]);

        $tuk = Tuk::create([
            'kode_tuk' => 'TUK-001',
            'nama_tuk' => 'Lab Komputer LSP',
            'jenis_tuk' => 'Sewaktu',
            'is_active' => true,
        ]);

        $this->schedule = AssessmentSchedule::create([
            'event_id' => $event->id,
            'scheme_id' => $this->scheme->id,
            'tuk_id' => $tuk->id,
            'kode_jadwal' => 'JDW-001',
            'nama_jadwal' => 'Uji Kompetensi Web Dev Nov 2026',
            'tgl_asesmen' => '2026-11-15',
            'kuota_peserta' => 20,
            'status' => ScheduleStatus::Pendaftaran,
        ]);
    }

    public function test_cascading_wilayah_api_endpoints(): void
    {
        // 1. Provinces
        $res = $this->getJson(route('api.wilayah.provinces'));
        $res->assertOk()
            ->assertJsonFragment(['id' => '31', 'nama' => 'DKI JAKARTA']);

        // 2. Regencies
        $res = $this->getJson(route('api.wilayah.regencies', ['province_id' => '31']));
        $res->assertOk()
            ->assertJsonFragment(['id' => '3171', 'nama' => 'KOTA JAKARTA SELATAN']);

        // 3. Districts
        $res = $this->getJson(route('api.wilayah.districts', ['regency_id' => '3171']));
        $res->assertOk()
            ->assertJsonFragment(['id' => '317101', 'nama' => 'TEBET']);

        // 4. Villages
        $res = $this->getJson(route('api.wilayah.villages', ['district_id' => '317101']));
        $res->assertOk()
            ->assertJsonFragment(['id' => '3171011001', 'nama' => 'TEBET TIMUR']);
    }

    public function test_asesi_can_access_application_create_page(): void
    {
        $response = $this->actingAs($this->asesi)->get(route('asesi.applications.create'));
        $response->assertOk()
            ->assertSee('FR-APL-01')
            ->assertSee('Junior Web Developer');
    }

    public function test_asesi_can_submit_apl01_application_with_documents(): void
    {
        $ktpFile = UploadedFile::fake()->create('ktp_budi.pdf', 350, 'application/pdf');

        $payload = [
            'nama_lengkap' => 'Budi Pratama S.Kom',
            'nik' => '3171012304950001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-04-23',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'alamat' => 'Jl. Tebet Raya No. 10',
            'rt_rw' => '001/002',
            'provinsi_kode' => '31',
            'provinsi_nama' => 'DKI JAKARTA',
            'kota_kode' => '3171',
            'kota_nama' => 'KOTA JAKARTA SELATAN',
            'kecamatan_kode' => '317101',
            'kecamatan_nama' => 'TEBET',
            'kelurahan_kode' => '3171011001',
            'kelurahan_nama' => 'TEBET TIMUR',
            'kode_pos' => '12820',
            'telepon' => '081234567890',
            'pendidikan_terakhir' => 'D4 / S1',
            'pekerjaan' => 'Programmer',
            'nama_perusahaan' => 'PT Teknologi Jaya',
            'jabatan' => 'Junior Web Dev',
            'scheme_id' => $this->scheme->id,
            'schedule_id' => $this->schedule->id,
            'tujuan_asesmen' => 'Sertifikasi Baru',
            'setuju_ketentuan' => '1',
            'ttd_asesi' => 'data:image/png;base64,sample-signature',
            'documents' => [
                $this->requirement->id => $ktpFile,
            ],
        ];

        $response = $this->actingAs($this->asesi)->post(route('asesi.applications.store'), $payload);

        $this->assertDatabaseHas('asesi_profiles', [
            'user_id' => $this->asesi->id,
            'nama_lengkap' => 'Budi Pratama S.Kom',
            'tempat_lahir' => 'Jakarta',
            'provinsi_nama' => 'DKI JAKARTA',
        ]);

        $this->assertDatabaseHas('assessment_applications', [
            'user_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'schedule_id' => $this->schedule->id,
            'status' => ApplicationStatus::Diajukan->value,
            'tujuan_asesmen' => 'Sertifikasi Baru',
        ]);

        $application = AssessmentApplication::where('user_id', $this->asesi->id)->first();
        $this->assertNotNull($application);
        $this->assertStringStartsWith('REG-', $application->nomor_pendaftaran);

        $this->assertDatabaseHas('application_documents', [
            'application_id' => $application->id,
            'requirement_id' => $this->requirement->id,
            'nama_dokumen' => 'Kartu Tanda Penduduk (KTP)',
            'status_verifikasi' => DocumentVerificationStatus::Pending->value,
        ]);

        $response->assertRedirect(route('asesi.applications.show', $application));
    }

    public function test_asesi_can_view_own_apl01_and_forbidden_for_other(): void
    {
        $application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-202610-0099',
            'user_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'status' => ApplicationStatus::Diajukan,
            'tujuan_asesmen' => 'Sertifikasi Baru',
        ]);

        // Own application: 200
        $response = $this->actingAs($this->asesi)->get(route('asesi.applications.show', $application));
        $response->assertOk()->assertSee($application->nomor_pendaftaran);

        // Other asesi: 403
        $otherAsesi = User::factory()->create(['role' => UserRole::Asesi]);
        $response2 = $this->actingAs($otherAsesi)->get(route('asesi.applications.show', $application));
        $response2->assertForbidden();
    }

    public function test_admin_can_view_verification_queue(): void
    {
        $application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-202610-0088',
            'user_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'status' => ApplicationStatus::Diajukan,
            'tujuan_asesmen' => 'Sertifikasi Baru',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.verifications.index', ['status' => 'diajukan']));
        $response->assertOk()
            ->assertSee($application->nomor_pendaftaran)
            ->assertSee($this->asesi->name);
    }

    public function test_admin_can_verify_document_via_ajax(): void
    {
        $application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-202610-0077',
            'user_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'status' => ApplicationStatus::Diajukan,
            'tujuan_asesmen' => 'Sertifikasi Baru',
        ]);

        $document = ApplicationDocument::create([
            'application_id' => $application->id,
            'requirement_id' => $this->requirement->id,
            'nama_dokumen' => 'KTP',
            'file_path' => 'documents/sample.pdf',
            'file_name' => 'ktp.pdf',
            'status_verifikasi' => DocumentVerificationStatus::Pending,
        ]);

        $response = $this->actingAs($this->admin)->postJson(
            route('admin.verifications.verify-document', $document),
            [
                'status_verifikasi' => 'memenuhi',
                'catatan' => 'Berkas KTP jelas dan masih berlaku.',
            ]
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'document' => [
                    'id' => $document->id,
                    'status' => 'memenuhi',
                    'catatan' => 'Berkas KTP jelas dan masih berlaku.',
                ],
            ]);

        $document->refresh();
        $this->assertEquals(DocumentVerificationStatus::Memenuhi, $document->status_verifikasi);
        $this->assertEquals($this->admin->id, $document->verified_by);
    }

    public function test_admin_can_decide_revisi_and_asesi_can_reupload(): void
    {
        $application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-202610-0066',
            'user_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'status' => ApplicationStatus::Diajukan,
            'tujuan_asesmen' => 'Sertifikasi Baru',
        ]);

        // 1. Admin marks as revisi
        $response = $this->actingAs($this->admin)->post(
            route('admin.verifications.update-status', $application),
            [
                'status' => 'revisi',
                'catatan_revisi' => 'KTP terpotong bagian NIK, mohon unggah ulang.',
            ]
        );

        $application->refresh();
        $this->assertEquals(ApplicationStatus::Revisi, $application->status);
        $this->assertEquals('KTP terpotong bagian NIK, mohon unggah ulang.', $application->catatan_revisi);

        // 2. Asesi re-uploads new file
        $newKtp = UploadedFile::fake()->create('ktp_baru.pdf', 400, 'application/pdf');

        $updateResponse = $this->actingAs($this->asesi)->put(
            route('asesi.applications.update', $application),
            [
                'documents' => [
                    $this->requirement->id => $newKtp,
                ],
            ]
        );

        $application->refresh();
        $this->assertEquals(ApplicationStatus::Diajukan, $application->status);
    }

    public function test_admin_can_approve_application_and_plot_schedule(): void
    {
        $application = AssessmentApplication::create([
            'nomor_pendaftaran' => 'REG-202610-0055',
            'user_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'status' => ApplicationStatus::Diajukan,
            'tujuan_asesmen' => 'Sertifikasi Baru',
        ]);

        // 1. Approve
        $response = $this->actingAs($this->admin)->post(
            route('admin.verifications.update-status', $application),
            [
                'status' => 'diterima',
                'rekomendasi_admin' => 'Diterima sebagai peserta sertifikasi',
            ]
        );

        $application->refresh();
        $this->assertEquals(ApplicationStatus::Diterima, $application->status);

        // 2. Plot schedule
        $plotResponse = $this->actingAs($this->admin)->post(
            route('admin.verifications.plot-schedule', $application),
            [
                'schedule_id' => $this->schedule->id,
            ]
        );

        $application->refresh();
        $this->assertEquals($this->schedule->id, $application->schedule_id);
    }
}
