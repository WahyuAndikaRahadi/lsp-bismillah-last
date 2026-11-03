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
            'selected_instruments' => ['AK01', 'IA01', 'IA01B', 'IA02', 'IA03', 'IA05'],
            'status' => 'ACTIVE',
            'created_by' => $this->admin->id,
        ]);
        $this->schedule->assessors()->attach($this->asesor->id);

        $this->assessment = Assessment::create([
            'schedule_id' => $this->schedule->id,
            'asesi_id' => $this->asesi->id,
            'assessor_id' => $this->asesor->id,
            'apl_status' => 'APPROVED',
            'ak01_agreed_at' => now(),
            'final_result' => 'KOMPETEN',
            'registered_at' => now(),
        ]);
    }

    /**
     * Test that canonical MUK screens render without template leaks,
     * containing exact static labels and zero forbidden placeholders.
     */
    public function test_canonical_muk_screens_render_with_exact_labels_and_zero_leaks(): void
    {
        $canonicalScreens = [
            // Asesor Screens
            [
                'actor' => $this->asesor,
                'url' => route('asesor.mapa01.edit', $this->schedule->id),
                'expected' => ['FR.MAPA.01', 'Merencanakan Aktivitas dan Proses Asesmen'],
            ],
            [
                'actor' => $this->asesor,
                'url' => route('asesor.mapa02.edit', $this->schedule->id),
                'expected' => ['FR.MAPA.02', 'Peta Instrumen Asesmen'],
            ],
            [
                'actor' => $this->asesor,
                'url' => route('asesor.schedule.ak05', $this->schedule->id),
                'expected' => ['FR.AK.05', 'Laporan Asesmen'],
            ],
            [
                'actor' => $this->asesor,
                'url' => route('asesor.schedule.ak06', $this->schedule->id),
                'expected' => ['FR.AK.06', 'Kaji Ulang Asesmen'],
            ],
            [
                'actor' => $this->asesor,
                'url' => route('asesor.schedule.ak07', $this->schedule->id),
                'expected' => ['FR.AK.07', 'Penyesuaian yang Wajar'],
            ],
            [
                'actor' => $this->asesor,
                'url' => route('asesor.assessment.ak02', $this->assessment->id),
                'expected' => ['FR.AK.02', 'Rekaman Asesmen Kompetensi'],
            ],
            // Asesi Screens
            [
                'actor' => $this->asesi,
                'url' => route('asesi.assessment.apl', $this->assessment->id),
                'expected' => ['FR.APL.01', 'FR.APL.02'],
            ],
            [
                'actor' => $this->asesi,
                'url' => route('asesi.assessment.ak03', $this->assessment->id),
                'expected' => ['FR.AK.03', 'Umpan Balik'],
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

        foreach ($canonicalScreens as $screen) {
            $response = $this->actingAs($screen['actor'])->get($screen['url']);
            $response->assertOk();
            $content = $response->getContent();

            foreach ($screen['expected'] as $label) {
                $this->assertStringContainsString(
                    $label,
                    $content,
                    "Screen [{$screen['url']}] must contain static label '{$label}'"
                );
            }

            foreach ($forbiddenPlaceholders as $forbidden) {
                $this->assertStringNotContainsString(
                    $forbidden,
                    $content,
                    "Screen [{$screen['url']}] leaks forbidden placeholder '{$forbidden}'"
                );
            }
        }
    }

    public function test_retired_print_routes_are_unavailable(): void
    {
        $this->actingAs($this->admin)->get('/admin/dokumen-muk')->assertNotFound();
        $this->actingAs($this->admin)->get('/admin/dokumen-muk/export/1/ba')->assertNotFound();
    }
}
