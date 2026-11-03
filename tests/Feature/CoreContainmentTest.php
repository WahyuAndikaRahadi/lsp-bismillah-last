<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\AssessmentAk02Item;
use App\Models\CompetencyUnit;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreContainmentTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $asesorUser;
    private User $asesiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => UserRole::Admin,
            'name' => 'Admin LSP',
            'email' => 'admin@example.com',
        ]);

        $this->asesorUser = User::factory()->create([
            'role' => UserRole::Asesor,
            'name' => 'Asesor Kompetensi',
            'email' => 'asesor@example.com',
        ]);

        $this->asesiUser = User::factory()->create([
            'role' => UserRole::Asesi,
            'name' => 'Peserta Uji',
            'email' => 'asesi@example.com',
        ]);
    }

    public function test_out_of_scope_routes_are_disabled_and_inaccessible(): void
    {
        $outOfScopeUrls = [
            '/proxy',
            '/admin/proxy',
            '/admin/complaints',
            '/admin/finance/transactions',
            '/admin/letters',
            '/admin/quality/documents',
            '/admin/messages',
            '/sertifikat/1',
            '/admin/exports/bnsp',
            '/staff',
        ];

        foreach ($outOfScopeUrls as $url) {
            // Guest access must be 404 (not routed)
            $response = $this->get($url);
            $this->assertEquals(404, $response->status(), "URL {$url} should return 404 for guest");

            // Admin access must be 404 (disabled completely)
            $adminResponse = $this->actingAs($this->adminUser)->get($url);
            $this->assertEquals(404, $adminResponse->status(), "URL {$url} should return 404 for admin");
        }

        // Post switch-back proxy must be 404
        $proxyPostResponse = $this->actingAs($this->adminUser)->post('/proxy/switch-back');
        $this->assertEquals(404, $proxyPostResponse->status(), "POST /proxy/switch-back should return 404");
    }

    public function test_get_or_head_request_to_workspace_does_not_mutate_domain_or_auto_assign_competence(): void
    {
        $scheme = Scheme::create([
            'code' => 'SKM-01',
            'name' => 'Skema Uji Otomotif',
            'scheme_type' => 'KLASTER',
        ]);

        $unit = CompetencyUnit::create([
            'scheme_id' => $scheme->id,
            'code' => 'OTO.KR01.001.01',
            'title' => 'Melakukan Perawatan Mesin',
        ]);

        $schedule = Schedule::create([
            'code' => 'SCH-TEST-01',
            'name' => 'Jadwal Uji Test',
            'scheme_id' => $scheme->id,
            'tuk_name' => 'TUK Mandiri',
            'date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'duration_minutes' => 480,
            'selected_instruments' => ['AK01', 'IA01', 'IA05'],
            'created_by' => $this->adminUser->id,
            'status' => 'ACTIVE',
        ]);

        $schedule->assessors()->attach($this->asesorUser->id);

        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'assessor_id' => $this->asesorUser->id,
            'asesi_id' => $this->asesiUser->id,
            'apl_status' => 'APPROVED',
            'status' => 'REGISTERED',
            'ak01_agreed_at' => now(),
            'final_result' => 'BELUM_SELESAI',
        ]);

        $this->assertEquals(0, AssessmentAk02Item::where('assessment_id', $assessment->id)->count());

        // Perform GET request twice
        $response1 = $this->actingAs($this->asesorUser)->get(route('asesor.assessment.workspace', $assessment->id));
        $response1->assertOk();

        // Perform HEAD request
        $responseHead = $this->actingAs($this->asesorUser)->head(route('asesor.assessment.workspace', $assessment->id));
        $responseHead->assertOk();

        $response2 = $this->actingAs($this->asesorUser)->get(route('asesor.assessment.workspace', $assessment->id));
        $response2->assertOk();

        // Verify zero mutation occurred on GET/HEAD
        $this->assertEquals(0, AssessmentAk02Item::where('assessment_id', $assessment->id)->count());

        $assessment->refresh();
        $this->assertEquals('BELUM_SELESAI', $assessment->final_result);
    }

    public function test_layout_menus_do_not_contain_links_to_out_of_scope_modules(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertOk();

        $forbiddenKeywords = [
            'admin/proxy',
            'admin/complaints',
            'admin/finance',
            'admin/letters',
            'admin/quality',
            'admin/exports',
            'Switch Role (Proxy)',
        ];

        foreach ($forbiddenKeywords as $keyword) {
            $response->assertDontSee($keyword);
        }
    }
}
