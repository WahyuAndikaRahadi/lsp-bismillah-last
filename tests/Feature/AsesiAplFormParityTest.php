<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\CompetencyUnit;
use App\Models\Kuk;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\UnitElement;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AsesiAplFormParityTest extends TestCase
{
    use RefreshDatabase;

    protected User $asesi;
    protected User $asesor;
    protected Scheme $scheme;
    protected Schedule $schedule;
    protected Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Admin LSP',
            'email' => 'admin_apl@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ADMIN_LSP',
            'is_active' => true,
        ]);

        $this->asesor = User::create([
            'name' => 'Gunadi, S.T.',
            'email' => 'asesor_gunadi@proindo.test',
            'password' => bcrypt('secret123'),
            'role' => 'ASESOR',
            'is_active' => true,
        ]);

        $this->asesi = User::create([
            'name' => 'Bambang Pratama',
            'email' => 'bambang@proindo.test',
            'phone' => '081234567890',
            'password' => bcrypt('secret123'),
            'role' => 'ASESI',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201010101019999',
            'tempat_lahir' => 'Karawang',
            'tanggal_lahir' => '2000-01-15',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'alamat_rumah' => 'Jl. Otomotif No. 45 Karawang',
            'kode_pos' => '41371',
            'pendidikan_terakhir' => 'SMK Otomotif',
            'nama_institusi' => 'Bengkel Auto Maju',
            'jabatan' => 'Teknisi Junior',
            'alamat_kantor' => 'Kawasan Industri KIIC',
            'signature_data' => 'data:image/png;base64,validsignature123',
        ]);

        $this->scheme = Scheme::create([
            'code' => '01/LSP PrO Indo/I/2026',
            'name' => 'Engine Junior Technician',
            'scheme_type' => 'OKUPASI',
        ]);

        $unit1 = CompetencyUnit::create([
            'scheme_id' => $this->scheme->id,
            'code' => 'G.45OTO01.001.2',
            'title' => 'Melaksanakan Keselamatan dan Kesehatan Kerja',
        ]);

        $element1 = UnitElement::create([
            'competency_unit_id' => $unit1->id,
            'title' => 'Mengidentifikasi potensi bahaya di tempat kerja',
        ]);

        Kuk::create([
            'unit_element_id' => $element1->id,
            'code_number' => '1.1',
            'statement' => 'Potensi bahaya diidentifikasi sesuai SOP K3',
        ]);

        $this->schedule = Schedule::create([
            'code' => 'SCH-ENG-01',
            'name' => 'Sertifikasi Engine SMK Karawang',
            'scheme_id' => $this->scheme->id,
            'tuk_name' => 'Bengkel SMKN 1 Karawang',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['AK01', 'IA01', 'IA02', 'IA05'],
            'created_by' => $admin->id,
            'status' => 'ACTIVE',
        ]);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'DRAFT',
            'status' => 'REGISTERED',
        ]);
    }

    public function test_apl_form_view_renders_official_bnsp_structure(): void
    {
        $response = $this->actingAs($this->asesi)
            ->get(route('asesi.assessment.apl', $this->assessment));

        $response->assertStatus(200);

        // Header & Branding MUK
        $response->assertSee('FR.APL.01. PERMOHONAN SERTIFIKASI KOMPETENSI');
        $response->assertSee('Bagian 1 : Rincian Data Pemohon Sertifikasi');
        $response->assertSee('a. Data Pribadi');
        $response->assertSee('b. Data Pekerjaan Sekarang');
        $response->assertSee('Bagian 2 : Data Sertifikasi');
        $response->assertSee('Bagian 3 : Bukti Kelengkapan Pemohon');

        // Personal data values
        $response->assertSee('Bambang Pratama');
        $response->assertSee('3201010101019999');
        $response->assertSee('Karawang');
        $response->assertSee('Bengkel Auto Maju');
        $response->assertSee('Teknisi Junior');

        // Scheme units
        $response->assertSee('Engine Junior Technician');
        $response->assertSee('G.45OTO01.001.2');
        $response->assertSee('Melaksanakan Keselamatan dan Kesehatan Kerja');

        // APL-02 Asesmen Mandiri
        $response->assertSee('FR.APL.02. ASESMEN MANDIRI');
        $response->assertSee('Potensi bahaya diidentifikasi sesuai SOP K3');
        $response->assertSee('Pilih Semua Kompeten');

        // Official single action button
        $response->assertSee('Simpan &amp; Kirim Permohonan Asesmen', false);
    }

    public function test_apl_submission_saves_mandiri_items_and_updates_status(): void
    {
        Storage::fake('local');

        $dummyKtp = UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf');
        $dummyFoto = UploadedFile::fake()->image('pasfoto.jpg');
        $dummyIjazah = UploadedFile::fake()->create('ijazah.pdf', 100, 'application/pdf');

        $kukId = Kuk::first()->id;

        $postData = [
            'assessor_id' => $this->asesor->id,
            'tujuan_asesmen' => 'Sertifikasi',
            'statement_integrity' => '1',
            'ktp' => $dummyKtp,
            'pas_foto' => $dummyFoto,
            'ijazah_smk' => $dummyIjazah,
            'kuk' => [
                $kukId => 'K',
            ],
        ];

        $response = $this->actingAs($this->asesi)
            ->post(route('asesi.assessment.apl.store', $this->assessment), $postData);

        $response->assertRedirect(route('asesi.dashboard'));
        $response->assertSessionHas('success');

        $this->assessment->refresh();
        $this->assertEquals('SUBMITTED', $this->assessment->apl_status);
        $this->assertDatabaseHas('t_assessment_apl02_items', [
            'assessment_id' => $this->assessment->id,
            'kuk_id' => $kukId,
            'assessment_value' => 'K',
        ]);
    }

    public function test_ak01_form_view_renders_official_bnsp_structure(): void
    {
        $this->assessment->update([
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => null,
        ]);

        $response = $this->actingAs($this->asesi)
            ->get(route('asesi.assessment.ak01', $this->assessment));

        $response->assertStatus(200);
        $response->assertSee('FR.AK.01. PERSETUJUAN ASESMEN DAN KERAHASIAAN');
        $response->assertSee('Engine Junior Technician');
        $response->assertSee('Bengkel SMKN 1 Karawang');
        $response->assertSee('Bambang Pratama');
        $response->assertSee('Bukti yang akan dikumpulkan');
        $response->assertSee('Pelaksanaan asesmen disepakati pada');
        $response->assertSee('Pernyataan Asesi');
        $response->assertSee('Pernyataan Asesor');
    }
}
