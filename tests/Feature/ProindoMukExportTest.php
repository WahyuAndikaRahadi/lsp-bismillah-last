<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\ProindoSchemesAndMukSeeder;
use Database\Seeders\ProindoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProindoMukExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $asesor;

    protected User $asesi;

    protected Scheme $scheme;

    protected Schedule $schedule;

    protected Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProindoSchemesAndMukSeeder::class);
        $this->seed(ProindoUserSeeder::class);

        $this->admin = User::where('role', 'ADMIN_LSP')->first()
            ?? User::factory()->create(['role' => 'ADMIN_LSP', 'email' => 'admin@test.com']);

        $this->asesor = User::where('role', 'ASESOR')->first()
            ?? User::factory()->create(['role' => 'ASESOR', 'email' => 'asesor@test.com']);

        $this->asesi = User::factory()->create([
            'role' => 'ASESI',
            'email' => 'peserta.muk@test.com',
            'full_name' => 'Ahmad Fadhil',
        ]);

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201889900112233',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1996-08-17',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'D3 Teknik Otomotif',
            'alamat_rumah' => 'Jl. Pahlawan No. 45',
            'jabatan' => 'Mekanik Kepala',
            'nama_institusi_perusahaan' => 'Bengkel Mobil Maju Jaya',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ]);

        $this->scheme = Scheme::first();

        $this->schedule = Schedule::create([
            'scheme_id' => $this->scheme->id,
            'name' => 'Uji Sertifikasi Otomotif BNSP 2026',
            'code' => 'SCH-MUK-01',
            'tuk_name' => 'TUK Otomotif Sejahtera',
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'selected_instruments' => ['IA01', 'IA01B', 'IA02', 'IA03', 'IA05'],
            'status' => 'ACTIVE',
            'created_by' => $this->admin->id,
        ]);
        $this->schedule->assessors()->attach($this->asesor->id);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
            'registered_at' => now(),
        ]);
    }

    public function test_dokumen_muk_index_displays_schedules(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dokumen-muk.index'));
        $response->assertOk();
        $response->assertSee('Dokumen MUK & Berita Acara');
        $response->assertSee($this->schedule->code);
    }

    public function test_berita_acara_and_daftar_hadir_html_exports(): void
    {
        // 1. Berita Acara (BA)
        $resBa = $this->actingAs($this->admin)->get(route('admin.dokumen-muk.export-ba', $this->schedule));
        $resBa->assertOk();
        $resBa->assertSee('BERITA ACARA PELAKSANAAN UJI KOMPETENSI');
        $resBa->assertSee($this->schedule->tuk_name);

        // 2. Daftar Hadir Asesor
        $resAsesor = $this->actingAs($this->admin)->get(route('admin.dokumen-muk.export-hadir-asesor', $this->schedule));
        $resAsesor->assertOk();
        $resAsesor->assertSee('DAFTAR HADIR ASESOR');

        // 3. Daftar Hadir Asesi
        $resAsesi = $this->actingAs($this->admin)->get(route('admin.dokumen-muk.export-hadir-asesi', $this->schedule));
        $resAsesi->assertOk();
        $resAsesi->assertSee('DAFTAR HADIR');
        $resAsesi->assertSee($this->asesi->full_name);
    }

    public function test_individual_muk_documents_render_correctly(): void
    {
        $types = [
            'apl01',
            'apl02',
            'ak01',
            'ak02',
            'ak03',
            'pernyataan',
        ];

        foreach ($types as $type) {
            $response = $this->actingAs($this->admin)->get(route('admin.dokumen-muk.export-asesi', [
                'assessment' => $this->assessment->id,
                'type' => $type,
            ]));
            $response->assertOk();
        }
    }

    public function test_native_excel_biodata_export(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dokumen-muk.export-excel-biodata', $this->schedule));
        $response->assertOk();
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
        $this->assertStringContainsString('Biodata_Asesi_', $response->headers->get('Content-Disposition'));
    }

    public function test_native_excel_daftar_hadir_exports(): void
    {
        // Asesi
        $resAsesi = $this->actingAs($this->admin)->get(route('admin.dokumen-muk.export-excel-hadir-asesi', $this->schedule));
        $resAsesi->assertOk();
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $resAsesi->headers->get('Content-Type')
        );

        // Asesor
        $resAsesor = $this->actingAs($this->admin)->get(route('admin.dokumen-muk.export-excel-hadir-asesor', $this->schedule));
        $resAsesor->assertOk();
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $resAsesor->headers->get('Content-Type')
        );
    }
}
