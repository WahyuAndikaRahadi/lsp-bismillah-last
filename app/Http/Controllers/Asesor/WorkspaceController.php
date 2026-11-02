<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Setting;
use App\Services\AkWorkflowService;
use App\Services\SchemeInstrumentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    use AuthorizesAssessor;

    public function __construct(
        protected AkWorkflowService $workflowService,
        protected SchemeInstrumentService $instrumentService
    ) {}

    public function show(Assessment $assessment, Request $request): View
    {
        $this->authorizeAssessorAssessment($assessment);

        $assessment->load([
            'asesi.profile',
            'schedule.scheme.competencyUnits.elements.kuks',
            'proofs',
            'apl02Items.kuk',
            'ak02Items.competencyUnit',
            'iaResponses',
            'assessor.profile',
            'ak07Items',
        ]);

        // Auto sync AK.02 if not yet initialized
        if ($assessment->ak02Items->isEmpty()) {
            $this->workflowService->syncAk02($assessment);
            $assessment->refresh();
        }

        $scheme = $assessment->schedule->scheme;
        $profile = $assessment->asesi->profile;
        $proofs = $assessment->proofs;
        $apl02Items = $assessment->apl02Items->keyBy('kuk_id');
        $units = $scheme->competencyUnits;
        $ak02Items = $assessment->ak02Items->keyBy('competency_unit_id');

        // CBT IA05 data
        $ia05Response = $assessment->iaResponses->firstWhere('instrument_code', 'IA05');
        $ia05Data = is_array($ia05Response?->response_data)
            ? $ia05Response->response_data
            : (json_decode($ia05Response?->response_data ?? '[]', true) ?: []);
        $cbtBreakdown = $ia05Data['breakdown'] ?? $ia05Data['items'] ?? [];
        $cbtScore = $ia05Response?->score ?? $assessment->ia05_score ?? 0;
        $passingGrade = (float) ($scheme->passing_grade_theory ?? Setting::get('cbt_passing_grade', 70.0));
        $isCbtPassed = (float) $cbtScore >= $passingGrade;

        // Praktik & PMO status
        $ia01Done = $assessment->iaResponses->firstWhere('instrument_code', 'IA01') !== null;
        $ia01bDone = $assessment->iaResponses->firstWhere('instrument_code', 'IA01B') !== null;
        $ia02Done = $assessment->iaResponses->firstWhere('instrument_code', 'IA02') !== null;
        $ia03Done = $assessment->iaResponses->firstWhere('instrument_code', 'IA03') !== null;
        $ak07Item = $assessment->ak07Items->first();

        // Selected tab
        $tabParam = $request->query('tab');
        $validTabs = ['apl', 'ak01', 'ia05', 'praktik', 'ak02'];

        if ($tabParam && in_array($tabParam, $validTabs, true)) {
            $activeTab = $tabParam;
        } else {
            // Contextual smart default tab
            if ($assessment->apl_status !== 'APPROVED') {
                $activeTab = 'apl';
            } elseif (empty($assessment->assessor_signature) && empty($assessment->approved_at)) {
                $activeTab = 'ak01';
            } elseif (! empty($assessment->final_result)) {
                $activeTab = 'ak02';
            } else {
                $activeTab = 'ia05';
            }
        }

        return view('asesor.workspace.show', compact(
            'assessment',
            'scheme',
            'profile',
            'proofs',
            'apl02Items',
            'units',
            'ak02Items',
            'cbtBreakdown',
            'cbtScore',
            'passingGrade',
            'isCbtPassed',
            'ia01Done',
            'ia01bDone',
            'ia02Done',
            'ia03Done',
            'ak07Item',
            'activeTab'
        ));
    }
}
