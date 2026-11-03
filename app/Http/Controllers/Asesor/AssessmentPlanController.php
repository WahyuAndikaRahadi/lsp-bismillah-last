<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mapa01Request;
use App\Http\Requests\Mapa02Request;
use App\Models\AssessmentPlan;
use App\Models\Schedule;
use App\Services\SchemeInstrumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentPlanController extends Controller
{
    public function __construct(
        protected SchemeInstrumentService $instrumentService
    ) {}

    public function edit(Schedule $schedule, Request $request): View
    {
        $user = auth()->user();

        // Check assessor assignment or admin
        if (! $user->isAdmin() && ! $schedule->assessors()->where('users.id', $user->id)->exists()) {
            abort(403, 'Anda tidak ditugaskan pada jadwal asesmen ini.');
        }

        $assessmentId = $request->query('assessment_id');

        $plan = AssessmentPlan::where('schedule_id', $schedule->id)
            ->where('assessor_id', $user->id)
            ->when($assessmentId, fn ($q) => $q->where('assessment_id', $assessmentId))
            ->latest('revision')
            ->first();

        if (! $plan) {
            $defaultGroups = $this->instrumentService->getMapa01Groups($schedule->scheme);

            $plan = new AssessmentPlan([
                'schedule_id' => $schedule->id,
                'assessor_id' => $user->id,
                'scheme_id' => $schedule->scheme_id,
                'scheme_version_id' => $schedule->scheme_version_id,
                'assessment_id' => $assessmentId,
                'revision' => 1,
                'status' => 'draft',
                'lock_version' => 1,
                'approach_data' => [
                    'asesi_type' => 'hasil_pelatihan_telusur',
                    'purpose' => 'sertifikasi',
                    'context_environment' => 'tempat_kerja_simulasi',
                    'context_opportunities' => 'tersedia',
                    'context_relationship' => 'langsung',
                    'context_conducted_by' => 'lembaga_sertifikasi',
                    'benchmarks' => ['standar_kompetensi'],
                ],
                'work_groups' => $defaultGroups,
                'modifications' => [
                    'has_special_characteristics' => null,
                    'special_characteristics_notes' => null,
                    'has_workplace_context' => null,
                    'workplace_context_notes' => null,
                    'has_training_package_suggestions' => null,
                    'training_package_suggestions_notes' => null,
                    'has_instrument_adjustments' => null,
                    'instrument_adjustments_notes' => null,
                    'has_integrated_assessment_opportunities' => null,
                    'integrated_assessment_notes' => null,
                ],
                'confirmation_data' => [
                    'compilers' => [
                        [
                            'name' => $user->full_name,
                            'met' => $user->assessor_registration_number ?? $user->asesorProfile?->no_met ?? 'MET.LSP.2026',
                            'date' => now()->toDateString(),
                        ],
                    ],
                ],
            ]);
        }

        return view('asesor.plans.mapa01', [
            'schedule' => $schedule,
            'plan' => $plan,
            'scheme' => $schedule->scheme,
        ]);
    }

    public function store(Mapa01Request $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $schedule = Schedule::findOrFail($validated['schedule_id']);

        if (! $user->isAdmin() && ! $schedule->assessors()->where('users.id', $user->id)->exists()) {
            abort(403, 'Anda tidak ditugaskan pada jadwal asesmen ini.');
        }

        $plan = AssessmentPlan::create([
            'schedule_id' => $schedule->id,
            'assessor_id' => $user->id,
            'scheme_id' => $schedule->scheme_id,
            'scheme_version_id' => $schedule->scheme_version_id,
            'assessment_id' => $validated['assessment_id'] ?? null,
            'revision' => 1,
            'status' => $validated['status'] ?? 'draft',
            'lock_version' => 1,
            'approach_data' => $validated['approach_data'] ?? null,
            'work_groups' => $validated['work_groups'],
            'modifications' => $validated['modifications'] ?? null,
            'confirmation_data' => $validated['confirmation_data'] ?? null,
        ]);

        $plan->invalidateAcceptedAgreements('Rencana asesmen (MAPA-01) baru disusun.');

        return redirect()->route('asesor.mapa01.edit', ['schedule' => $schedule->id])
            ->with('success', 'Rencana asesmen (FR.MAPA.01) berhasil disimpan.');
    }

    public function update(Mapa01Request $request, AssessmentPlan $plan): RedirectResponse
    {
        $user = auth()->user();

        if (! $plan->isEditableBy($user)) {
            abort(403, 'Rencana asesmen terkunci atau Anda tidak memiliki akses untuk mengedit.');
        }

        $validated = $request->validated();

        // Stale revision check (optimistic locking)
        if ($request->filled('lock_version') && (int) $request->input('lock_version') !== (int) $plan->lock_version) {
            abort(409, 'Konflik perubahan: dokumen telah diperbarui pada sesi lain. Muat ulang halaman.');
        }

        $plan->update([
            'approach_data' => $validated['approach_data'] ?? $plan->approach_data,
            'work_groups' => $validated['work_groups'] ?? $plan->work_groups,
            'modifications' => $validated['modifications'] ?? $plan->modifications,
            'confirmation_data' => $validated['confirmation_data'] ?? $plan->confirmation_data,
            'status' => $validated['status'] ?? $plan->status,
            'lock_version' => $plan->lock_version + 1,
        ]);

        $invalidatedCount = $plan->invalidateAcceptedAgreements('Rencana asesmen (MAPA-01) dimutakhirkan.');

        $msg = 'Rencana asesmen (FR.MAPA.01) berhasil diperbarui.';
        if ($invalidatedCount > 0) {
            $msg .= " ({$invalidatedCount} persetujuan AK-01 asesi di-reset untuk meminta persetujuan ulang terhadap revisi baru).";
        }

        return redirect()->route('asesor.mapa01.edit', ['schedule' => $plan->schedule_id])
            ->with('success', $msg);
    }

    public function lock(Request $request, AssessmentPlan $plan): RedirectResponse
    {
        $user = auth()->user();

        if (! $plan->isEditableBy($user)) {
            abort(403, 'Akses terbatas untuk mengunci rencana asesmen.');
        }

        $plan->update([
            'is_locked' => true,
            'status' => 'locked',
            'locked_at' => now(),
            'lock_version' => $plan->lock_version + 1,
        ]);

        return redirect()->route('asesor.mapa01.edit', ['schedule' => $plan->schedule_id])
            ->with('success', 'Rencana asesmen berhasil dikunci (locked). Perubahan selanjutnya memerlukan revisi baru.');
    }

    public function editMapa02(Schedule $schedule, Request $request): View
    {
        $user = auth()->user();

        if (! $user->isAdmin() && ! $schedule->assessors()->where('users.id', $user->id)->exists()) {
            abort(403, 'Anda tidak ditugaskan pada jadwal asesmen ini.');
        }

        $plan = AssessmentPlan::firstOrCreate(
            [
                'schedule_id' => $schedule->id,
                'assessor_id' => $user->id,
            ],
            [
                'scheme_id' => $schedule->scheme_id,
                'scheme_version_id' => $schedule->scheme_version_id,
                'revision' => 1,
                'status' => 'draft',
                'lock_version' => 1,
                'mapa02_data' => $this->instrumentService->getMapa02Template($schedule->scheme),
            ]
        );

        if (empty($plan->mapa02_data)) {
            $plan->update(['mapa02_data' => $this->instrumentService->getMapa02Template($schedule->scheme)]);
        }

        return view('asesor.plans.mapa02', [
            'schedule' => $schedule,
            'plan' => $plan,
            'scheme' => $schedule->scheme,
        ]);
    }

    public function updateMapa02(Mapa02Request $request, AssessmentPlan $plan): RedirectResponse
    {
        $user = auth()->user();

        if (! $plan->isEditableBy($user)) {
            abort(403, 'Rencana asesmen terkunci atau Anda tidak memiliki akses untuk mengedit.');
        }

        $validated = $request->validated();

        if ($request->filled('lock_version') && (int) $request->input('lock_version') !== (int) $plan->lock_version) {
            abort(409, 'Konflik perubahan: dokumen telah diperbarui pada sesi lain. Muat ulang halaman.');
        }

        $plan->update([
            'mapa02_data' => $validated['mapa02_data'],
            'lock_version' => $plan->lock_version + 1,
        ]);

        return redirect()->route('asesor.mapa02.edit', ['schedule' => $plan->schedule_id])
            ->with('success', 'Peta instrumen asesmen (FR.MAPA.02) berhasil disimpan.');
    }
}
