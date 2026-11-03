<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssessmentReportRequest;
use App\Models\AssessmentReport;
use App\Models\AuditEvent;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentReportController extends Controller
{
    private function authorizeAssessorSchedule(Schedule $schedule): void
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        if ($user->isAdmin()) {
            return;
        }

        $isAssigned = $schedule->assessors()->where('users.id', $user->id)->exists();
        abort_unless($isAssigned, 403, 'Anda tidak ditugaskan sebagai asesor pada jadwal ini.');
    }

    /**
     * Tampilkan formulir FR.AK.05 (Laporan Asesmen) khusus lingkup asesi asesor login.
     */
    public function showAk05(Schedule $schedule): View
    {
        $this->authorizeAssessorSchedule($schedule);

        $assessorId = auth()->id();

        // Roster khusus asesi yang ditugaskan kepada asesor ini
        $assignedAssessments = $schedule->assessments()
            ->where(function ($q) use ($assessorId) {
                $q->where('assessor_id', $assessorId)
                  ->orWhereNull('assessor_id');
            })
            ->with(['asesi.profile', 'ak02Items.competencyUnit'])
            ->get();

        $report = AssessmentReport::where('schedule_id', $schedule->id)
            ->where('assessor_id', $assessorId)
            ->where('report_type', 'AK05')
            ->latest('revision')
            ->first();

        return view('asesor.reports.ak05', compact('schedule', 'assignedAssessments', 'report'));
    }

    /**
     * Simpan formulir FR.AK.05 (Laporan Asesmen) per asesor dengan snapshot roster.
     */
    public function storeAk05(Schedule $schedule, AssessmentReportRequest $request): RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        $assessorId = auth()->id();

        $existing = AssessmentReport::where('schedule_id', $schedule->id)
            ->where('assessor_id', $assessorId)
            ->where('report_type', 'AK05')
            ->latest('revision')
            ->first();

        // Stale save / Version conflict check
        if ($request->filled('lock_version') && $existing) {
            if ((int) $request->input('lock_version') !== (int) ($existing->lock_version ?? 1)) {
                abort(409, 'Konflik versi: laporan telah diperbarui pada sesi lain. Muat ulang halaman.');
            }
        }

        $assignedAssessments = $schedule->assessments()
            ->where(function ($q) use ($assessorId) {
                $q->where('assessor_id', $assessorId)
                  ->orWhereNull('assessor_id');
            })
            ->with(['asesi.profile', 'ak02Items.competencyUnit'])
            ->get();

        $rosterSnapshot = $assignedAssessments->map(function ($a) {
            $bkUnits = $a->ak02Items->where('is_competent', false)->map(fn ($item) => [
                'code' => $item->competencyUnit?->code,
                'title' => $item->competencyUnit?->title,
            ])->values()->toArray();

            return [
                'assessment_id' => $a->id,
                'asesi_id' => $a->asesi_id,
                'asesi_name' => $a->asesi?->name,
                'final_result' => $a->final_result,
                'rekomendasi_asesor' => $a->rekomendasi_asesor,
                'bk_units' => $bkUnits,
            ];
        })->toArray();

        $content = [
            'aspects_positive' => $request->input('aspects_positive'),
            'aspects_negative' => $request->input('aspects_negative'),
            'rejections_record' => $request->input('rejections_record'),
            'improvements' => $request->input('improvements'),
            'notes' => $request->input('notes'),
        ];

        $signature = $request->input('signature_data') ?? auth()->user()->profile?->signature_data;

        DB::transaction(function () use ($schedule, $assessorId, $existing, $rosterSnapshot, $content, $signature) {
            $revision = $existing ? ($existing->revision + 1) : 1;
            $lockVersion = $existing ? ($existing->lock_version + 1) : 1;

            $report = AssessmentReport::create([
                'schedule_id' => $schedule->id,
                'assessor_id' => $assessorId,
                'report_type' => 'AK05',
                'revision' => $revision,
                'roster_json' => $rosterSnapshot,
                'content_json' => $content,
                'status' => 'SUBMITTED',
                'assessor_signature' => $signature,
                'assessor_signed_at' => now(),
                'submitted_at' => now(),
                'lock_version' => $lockVersion,
            ]);

            AuditEvent::record(
                action: 'report.ak05_submitted',
                subject: $report,
                reason: "Asesor menyerahkan Laporan Asesmen FR.AK.05 revisi {$revision}",
                details: [
                    'schedule_id' => $schedule->id,
                    'assessor_id' => $assessorId,
                    'revision' => $revision,
                    'candidates_count' => count($rosterSnapshot),
                ],
                userId: $assessorId
            );
        });

        return redirect()->route('asesor.schedule.reports', $schedule->id)
            ->with('success', 'FR.AK.05 Laporan Asesmen berhasil disimpan dan diserahkan.');
    }

    /**
     * Tampilkan formulir FR.AK.06 (Kaji Ulang Asesmen).
     */
    public function showAk06(Schedule $schedule): View
    {
        $this->authorizeAssessorSchedule($schedule);

        $assessorId = auth()->id();

        $report = AssessmentReport::where('schedule_id', $schedule->id)
            ->where('assessor_id', $assessorId)
            ->where('report_type', 'AK06')
            ->latest('revision')
            ->first();

        $defaultMatrix = [
            ['name' => '1. Perencanaan Asesmen (Tahap Persiapan & Konsultasi)', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
            ['name' => '2. Pelaksanaan Asesmen (Observasi Praktik & Klarifikasi)', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
            ['name' => '3. Keputusan Asesmen (Pemberian Rekomendasi K/BK)', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
            ['name' => '4. Umpan Balik Asesmen (Komunikasi Hasil & Hak Banding)', 'valid' => 'YA', 'reliabel' => 'YA', 'fleksibel' => 'YA', 'adil' => 'YA', 'notes' => ''],
        ];

        return view('asesor.reports.ak06', compact('schedule', 'report', 'defaultMatrix'));
    }

    /**
     * Simpan formulir FR.AK.06 (Kaji Ulang Asesmen).
     */
    public function storeAk06(Schedule $schedule, AssessmentReportRequest $request): RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        $assessorId = auth()->id();

        $existing = AssessmentReport::where('schedule_id', $schedule->id)
            ->where('assessor_id', $assessorId)
            ->where('report_type', 'AK06')
            ->latest('revision')
            ->first();

        // Stale save / Version conflict check
        if ($request->filled('lock_version') && $existing) {
            if ((int) $request->input('lock_version') !== (int) ($existing->lock_version ?? 1)) {
                abort(409, 'Konflik versi: tinjauan asesmen telah diperbarui pada sesi lain. Muat ulang halaman.');
            }
        }

        $content = [
            'procedure_matrix' => $request->input('procedure_matrix'),
            'dimensions_review' => $request->input('dimensions_review'),
            'evidence_references' => $request->input('evidence_references'),
            'comments' => $request->input('comments'),
        ];

        $signature = $request->input('signature_data') ?? auth()->user()->profile?->signature_data;

        DB::transaction(function () use ($schedule, $assessorId, $existing, $content, $signature) {
            $revision = $existing ? ($existing->revision + 1) : 1;
            $lockVersion = $existing ? ($existing->lock_version + 1) : 1;

            $report = AssessmentReport::create([
                'schedule_id' => $schedule->id,
                'assessor_id' => $assessorId,
                'report_type' => 'AK06',
                'revision' => $revision,
                'content_json' => $content,
                'status' => 'SUBMITTED',
                'assessor_signature' => $signature,
                'assessor_signed_at' => now(),
                'submitted_at' => now(),
                'lock_version' => $lockVersion,
            ]);

            AuditEvent::record(
                action: 'report.ak06_submitted',
                subject: $report,
                reason: "Asesor menyerahkan Kaji Ulang Asesmen FR.AK.06 revisi {$revision}",
                details: [
                    'schedule_id' => $schedule->id,
                    'assessor_id' => $assessorId,
                    'revision' => $revision,
                ],
                userId: $assessorId
            );
        });

        return redirect()->route('asesor.schedule.reports', $schedule->id)
            ->with('success', 'FR.AK.06 Kaji Ulang Asesmen berhasil disimpan dan diserahkan.');
    }
}
