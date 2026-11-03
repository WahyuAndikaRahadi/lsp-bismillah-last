<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAk07Item;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JadwalController extends Controller
{
    use AuthorizesAssessor;

    public function index(): View
    {
        $user = auth()->user();
        $assignedScheduleIds = $user->assignedSchedules()->pluck('t_schedules.id');

        $schedules = $user->assignedSchedules()
            ->with(['scheme', 'assessments'])
            ->latest('date')
            ->get();

        $jadwals = $schedules;

        // Pending APL reviews across all assigned schedules
        $pendingAplAssessments = Assessment::with(['asesi.profile', 'schedule.scheme'])
            ->whereIn('schedule_id', $assignedScheduleIds)
            ->where('apl_status', 'SUBMITTED')
            ->latest('registered_at')
            ->get();

        // Candidates needing assessment/scoring (APL approved but not completed/scored)
        $pendingScoringAssessments = Assessment::with(['asesi.profile', 'schedule.scheme'])
            ->whereIn('schedule_id', $assignedScheduleIds)
            ->where('apl_status', 'APPROVED')
            ->whereNull('final_result')
            ->where('status', '!=', 'COMPLETED')
            ->latest('registered_at')
            ->take(6)
            ->get();

        return view('asesor.jadwal.index', compact(
            'schedules',
            'jadwals',
            'pendingAplAssessments',
            'pendingScoringAssessments'
        ));
    }

    public function asesiList(Schedule $schedule): View
    {
        $this->authorizeAssessorSchedule($schedule);

        $schedule->load(['scheme', 'assessors']);
        $jadwal = $schedule;
        $assessments = $schedule->assessments()
            ->with(['asesi.profile', 'assessor', 'latestAppeal'])
            ->get();

        return view('asesor.jadwal.asesi', compact('schedule', 'jadwal', 'assessments'));
    }

    public function bulkAction(Schedule $schedule, Request $request): RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        $validated = $request->validate([
            'assessment_ids' => 'required|array|min:1',
            'assessment_ids.*' => 'required|exists:t_assessments,id',
            'bulk_action' => 'required|string|in:approve_and_unlock,set_instruments',
            'instruments' => 'nullable|array',
            'instruments.*' => 'string|in:IA01,IA01B,IA02,IA03,IA05,ia01,ia01b,ia02,ia03,ia05',
        ]);

        $assessor = auth()->user();
        $assessorSig = $assessor->profile?->signature_data;
        $now = now();
        $action = $validated['bulk_action'];

        $assessments = Assessment::where('schedule_id', $schedule->id)
            ->whereIn('id', $validated['assessment_ids'])
            ->with(['asesi.profile', 'schedule.assessors'])
            ->get();

        $processedCount = 0;

        DB::transaction(function () use ($assessments, $action, $assessor, $assessorSig, $now, $validated, &$processedCount) {
            foreach ($assessments as $assessment) {
                $isAssigned = (int) $assessment->assessor_id === (int) $assessor->id;
                $isSingleAssessor = $assessment->schedule->assessors->count() === 1 && $assessment->schedule->assessors->first()?->id === $assessor->id;
                if (! $isAssigned && ! empty($assessment->assessor_id) && ! $isSingleAssessor) {
                    continue; // Skip candidates explicitly plotted to a different assessor
                }

                if ($action === 'approve_and_unlock') {
                    $asesiSig = $assessment->asesi_signature ?? $assessment->asesi?->profile?->signature_data;

                    $assessment->update([
                        'apl_status' => 'APPROVED',
                        'approved_at' => $assessment->approved_at ?? $now,
                        'admin_status' => 'APPROVED',
                        'assessor_id' => $assessor->id,
                        'assessor_signature' => $assessment->assessor_signature ?? $assessorSig,
                        'assessor_signed_at' => $assessment->assessor_signed_at ?? $now,
                        'ak01_agreed_at' => $assessment->ak01_agreed_at ?? $now,
                        'asesi_signature' => $asesiSig,
                        'asesi_signed_at' => $assessment->asesi_signed_at ?? $now,
                        'esign_consent_at' => $assessment->esign_consent_at ?? $now,
                        'last_active_step' => 'ruang-uji',
                    ]);

                    AssessmentAk07Item::firstOrCreate([
                        'assessment_id' => $assessment->id,
                    ], [
                        'adjustment_category' => 'Standar',
                        'adjustment_detail' => 'Tidak ada penyesuaian khusus yang diperlukan (asesmen standar).',
                        'assessor_id' => $assessor->id,
                    ]);

                    if (! empty($validated['instruments'])) {
                        $assessment->update([
                            'unlocked_instruments' => array_values(array_map('strtoupper', $validated['instruments'])),
                        ]);
                    }

                    $processedCount++;
                } elseif ($action === 'set_instruments') {
                    $instruments = ! empty($validated['instruments'])
                        ? array_values(array_map('strtoupper', $validated['instruments']))
                        : null;

                    $assessment->update([
                        'unlocked_instruments' => $instruments,
                    ]);

                    $processedCount++;
                }
            }
        });

        $msg = $action === 'approve_and_unlock'
            ? "Berhasil menyetujui APL, mengesahkan FR.AK.01, dan membuka Ruang Uji untuk {$processedCount} asesi sekaligus!"
            : "Instrumen asesmen berhasil diperbarui untuk {$processedCount} asesi.";

        return redirect()->route('asesor.jadwal.asesi', $schedule->id)->with('success', $msg);
    }
}
