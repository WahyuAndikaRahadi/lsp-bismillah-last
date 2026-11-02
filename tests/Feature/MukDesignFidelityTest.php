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

class MukDesignFidelityTest extends TestCase
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
            'email' => 'peserta.fidelity@test.com',
            'full_name' => 'Budi Santoso',
        ]);

        UserProfile::create([
            'user_id' => $this->asesi->id,
            'nik' => '3201012345678901',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 45, Bandung',
            'institution' => 'SMK Negeri 1 Otomotif',
        ]);

        $this->scheme = Scheme::where('code', '05/LSP PrO Indo/I/2026')->first()
            ?? Scheme::first();

        $this->schedule = Schedule::first() ?? Schedule::create([
            'scheme_id' => $this->scheme->id,
            'name' => 'Uji Sertifikasi Otomotif BNSP 2026',
            'code' => 'SCH-FIDELITY-01',
            'tuk_name' => 'TUK Otomotif Sejahtera',
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'selected_instruments' => ['IA01', 'IA01B', 'IA02', 'IA03', 'IA05'],
            'status' => 'ACTIVE',
            'created_by' => $this->admin->id,
        ]);
        $this->schedule->assessors()->syncWithoutDetaching([$this->asesor->id]);

        $this->assessment = Assessment::first() ?? Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'scheme_id' => $this->scheme->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'APPROVED',
            'final_result' => 'KOMPETEN',
            'registered_at' => now(),
        ]);
    }

    /**
     * Test that all mandatory MUK document views render without template leaks,
     * containing exact scheme titles, codes, and static labels.
     */
    public function test_muk_print_templates_render_with_exact_labels_and_zero_leaks(): void
    {
        $documents = [
            // Group 1
            [
                'route' => route('admin.dokumen-muk.export-ba', $this->schedule->id),
                'expected' => ['BERITA ACARA', 'LSP PrO Indo', $this->scheme->name],
            ],
            [
                'route' => route('admin.dokumen-muk.export-hadir-asesi', $this->schedule->id),
                'expected' => ['DAFTAR HADIR', 'TUK', 'Tanda Tangan'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-hadir-asesor', $this->schedule->id),
                'expected' => ['DAFTAR HADIR ASESOR', 'No.MET', 'Tanda Tangan'],
            ],
            // Group 2
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ak02']),
                'expected' => ['FR.AK.02', 'REKAMAN ASESMEN KOMPETENSI', 'Rekomendasi'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ak03']),
                'expected' => ['FR.AK.03', 'UMPAN BALIK DAN CATATAN ASESMEN'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ak04']),
                'expected' => ['FR.AK.04', 'BANDING ASESMEN', 'Banding'],
            ],
            [
                'route' => route('admin.dokumen-muk.export', [$this->schedule->id, 'ak05']),
                'expected' => ['FR.AK.05', 'LAPORAN ASESMEN', 'Aspek Negatif dan Positif'],
            ],
            [
                'route' => route('admin.dokumen-muk.export', [$this->schedule->id, 'ak06']),
                'expected' => ['FR.AK.06', 'MENINJAU PROSES ASESMEN'],
            ],
            // Group 3
            [
                'route' => route('admin.dokumen-muk.export', [$this->schedule->id, 'cover']),
                'expected' => ['DOKUMEN', 'MUK 1', 'LSP-PrO Indo'],
            ],
            [
                'route' => route('admin.dokumen-muk.export', [$this->schedule->id, 'daftar-periksa']),
                'expected' => ['DAFTAR PERIKSA', 'KELENGKAPAN DOKUMEN'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'pernyataan']),
                'expected' => ['SURAT PERNYATAAN PEMEGANG SERTIFIKAT'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ak01']),
                'expected' => ['FR.AK.01', 'PERSETUJUAN ASESMEN DAN KERAHASIAAN'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ak07']),
                'expected' => ['FR.AK.07', 'PENYESUAIAN YANG WAJAR'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'apl01']),
                'expected' => ['FR.APL.01', 'PERMOHONAN SERTIFIKASI KOMPETENSI'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'apl02']),
                'expected' => ['FR.APL.02', 'ASESMEN MANDIRI'],
            ],
            [
                'route' => route('admin.dokumen-muk.export', [$this->schedule->id, 'mapa']),
                'expected' => ['FR.MAPA.01', 'MERENCANAKAN AKTIVITAS DAN PROSES ASESMEN'],
            ],
            [
                'route' => route('admin.dokumen-muk.export', [$this->schedule->id, 'mapa02']),
                'expected' => ['FR.MAPA.02', 'PETA INSTRUMEN'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ia01']),
                'expected' => ['FR.IA.01', 'CL - CEKLIS OBSERVASI AKTIVITAS DI TEMPAT KERJA'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ia01b']),
                'expected' => ['FR.IA.01B', 'Report Sheet', 'MEKANIK JUNIOR'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ia02']),
                'expected' => ['FR.IA.02', 'TPD - TUGAS PRAKTIK DEMONSTRASI'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ia03']),
                'expected' => ['FR.IA.03', 'PMO - PERTANYAAN MENDUKUNG OBSERVASI'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ia05a']),
                'expected' => ['FR.IA.05A', 'DPT - PERTANYAAN TERTULIS PILIHAN GANDA'],
            ],
            [
                'route' => route('admin.dokumen-muk.export', [$this->schedule->id, 'ia05b']),
                'expected' => ['FR.IA.05.B', 'KUNCI JAWABAN'],
            ],
            [
                'route' => route('admin.dokumen-muk.export-asesi', [$this->assessment->id, 'ia05c']),
                'expected' => ['FR.IA.05.C', 'LEMBAR JAWABAN'],
            ],
        ];

        $forbiddenPlaceholders = [
            'Lorem ipsum',
            'TODO',
            'undefined',
            'NaN',
            '{{ $',
            '{!! $',
        ];

        foreach ($documents as $doc) {
            $response = $this->actingAs($this->admin)->get($doc['route']);
            $response->assertOk();
            $content = $response->getContent();

            // Assert required static labels are present
            foreach ($doc['expected'] as $label) {
                $this->assertStringContainsString(
                    $label,
                    $content,
                    "Document [{$doc['route']}] must contain static label '{$label}'"
                );
            }

            // Assert no template leak or forbidden placeholder exists
            foreach ($forbiddenPlaceholders as $forbidden) {
                $this->assertStringNotContainsString(
                    $forbidden,
                    $content,
                    "Document [{$doc['route']}] leaks forbidden placeholder '{$forbidden}'"
                );
            }
        }
    }
}
