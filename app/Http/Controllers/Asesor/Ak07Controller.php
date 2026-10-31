<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Models\AssessmentAk07Item;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class Ak07Controller extends Controller
{
    use AuthorizesAssessor;

    public function index(Schedule $schedule, Request $request): View|RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        $schedule->load('scheme', 'assessments.asesi', 'assessments.ak07Items');

        $unapprovedCount = $schedule->assessments()->where('apl_status', '!=', 'APPROVED')->count();
        if ($unapprovedCount > 0) {
            return redirect()->route('asesor.jadwal.asesi', $schedule->id)
                ->with('error', "Formulir FR.AK07 belum dapat dibuka. Masih ada {$unapprovedCount} asesi yang belum disetujui APL-nya.");
        }

        $jadwal = $schedule;
        $assessments = $schedule->assessments;
        $selectedAssessmentId = $request->query('assessment');

        return view('asesor.ak07', compact('schedule', 'jadwal', 'assessments', 'selectedAssessmentId'));
    }

    public function store(Schedule $schedule, Request $request): RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        if ($schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }

        $unapprovedCount = $schedule->assessments()->where('apl_status', '!=', 'APPROVED')->count();
        if ($unapprovedCount > 0) {
            return redirect()->route('asesor.jadwal.asesi', $schedule->id)
                ->with('error', 'Penyesuaian tidak dapat disimpan karena belum seluruh asesi disetujui APL-nya.');
        }

        $ids = $request->input('assessment_ids', $request->input('asesi_ids', []));

        // If asesor submits without selecting any asesi or adjustments (unfilled/optional)
        if (empty($ids)) {
            foreach ($schedule->assessments as $assessment) {
                AssessmentAk07Item::firstOrCreate([
                    'assessment_id' => $assessment->id,
                ], [
                    'adjustment_category' => 'Standar',
                    'adjustment_detail' => 'Tidak ada penyesuaian khusus yang diperlukan (asesmen standar).',
                    'assessor_id' => auth()->id(),
                ]);
            }

            return redirect()->route('asesor.schedule.ak01', $schedule->id)
                ->with('info', 'FR.AK07 ditetapkan tanpa penyesuaian khusus. Melanjutkan ke FR.AK01 (Persetujuan Asesmen & Kerahasiaan).');
        }

        $items = $request->input('items', []);
        $agreementStatus = $request->input('agreement_status');
        $agreementNotes = $request->input('agreement_notes');
        $legacyCategory = $request->input('category');
        $legacyNotes = $request->input('notes');

        $savedAny = false;

        DB::transaction(function () use (
            $ids,
            $items,
            $agreementStatus,
            $agreementNotes,
            $legacyCategory,
            $legacyNotes,
            &$savedAny
        ) {
            // Idempotency: clear previous AK07 items for selected assessments to avoid duplicates
            AssessmentAk07Item::whereIn('assessment_id', $ids)->delete();

            // Process 8-item table format if present
            if (! empty($items) && is_array($items)) {
                foreach ($items as $itemIndex => $itemData) {
                    $needed = strtoupper($itemData['needed'] ?? '');
                    if ($needed === 'YA') {
                        $selectedOptions = $itemData['options'] ?? [];
                        $customNote = trim($itemData['custom_note'] ?? '');
                        $descParts = [];
                        if (! empty($selectedOptions)) {
                            $descParts[] = implode('; ', $selectedOptions);
                        }
                        if ($customNote !== '') {
                            $descParts[] = "Catatan: {$customNote}";
                        }
                        $detailText = ! empty($descParts) ? implode(' | ', $descParts) : 'Penyesuaian wajar disetujui';

                        $categoryName = $itemData['title'] ?? "Karakteristik #{$itemIndex}";

                        foreach ($ids as $assessmentId) {
                            AssessmentAk07Item::updateOrCreate(
                                [
                                    'assessment_id' => $assessmentId,
                                    'adjustment_category' => substr($categoryName, 0, 100),
                                ],
                                [
                                    'adjustment_detail' => $detailText,
                                    'assessor_id' => auth()->id(),
                                ]
                            );
                        }
                        $savedAny = true;
                    }
                }
            }

            // Process agreement notes or legacy format
            if ($legacyCategory && $legacyNotes) {
                foreach ($ids as $assessmentId) {
                    AssessmentAk07Item::updateOrCreate(
                        [
                            'assessment_id' => $assessmentId,
                            'adjustment_category' => substr($legacyCategory, 0, 100),
                        ],
                        [
                            'adjustment_detail' => $legacyNotes,
                            'assessor_id' => auth()->id(),
                        ]
                    );
                }
                $savedAny = true;
            } elseif ($agreementStatus === 'YA' && ! empty($agreementNotes)) {
                foreach ($ids as $assessmentId) {
                    AssessmentAk07Item::updateOrCreate(
                        [
                            'assessment_id' => $assessmentId,
                            'adjustment_category' => 'Kesepakatan Asesor-Asesi',
                        ],
                        [
                            'adjustment_detail' => $agreementNotes,
                            'assessor_id' => auth()->id(),
                        ]
                    );
                }
                $savedAny = true;
            }

            if (! $savedAny) {
                foreach ($ids as $assessmentId) {
                    AssessmentAk07Item::firstOrCreate([
                        'assessment_id' => $assessmentId,
                    ], [
                        'adjustment_category' => 'Standar',
                        'adjustment_detail' => 'Tidak ada penyesuaian khusus yang diperlukan (asesmen standar).',
                        'assessor_id' => auth()->id(),
                    ]);
                }
            }
        });

        $msg = $savedAny
            ? 'Penyesuaian yang wajar (FR.AK07) berhasil disimpan. Melanjutkan ke FR.AK01 (Persetujuan Asesmen & Kerahasiaan).'
            : 'FR.AK07 disimpan tanpa penyesuaian khusus. Melanjutkan ke FR.AK01 (Persetujuan Asesmen & Kerahasiaan).';

        return redirect()->route('asesor.schedule.ak01', $schedule->id)->with('success', $msg);
    }
}
