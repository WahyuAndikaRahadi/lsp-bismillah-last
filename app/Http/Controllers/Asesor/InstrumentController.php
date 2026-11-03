<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Concerns\AuthorizesAssessor;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentIaResponse;
use App\Models\Schedule;
use App\Models\Setting;
use App\Rules\ValidSignature;
use App\Services\SchemeInstrumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InstrumentController extends Controller
{
    use AuthorizesAssessor;

    private const INSTRUMENT_MAP = [
        'ia01' => ['view' => 'asesor.ruang-uji.ia01', 'resolver' => 'resolveIa01'],
        'ia01b' => ['view' => 'asesor.ruang-uji.ia01b', 'resolver' => 'resolveIa01b'],
        'ia02' => ['view' => 'asesor.ruang-uji.ia02', 'resolver' => 'resolveIa02'],
        'ia03' => ['view' => 'asesor.ruang-uji.ia03', 'resolver' => 'resolveIa03'],
        'ia05' => ['view' => 'asesor.ruang-uji.ia05', 'resolver' => 'resolveIa05'],
    ];

    protected array $instrumentsMeta = [
        'AK01' => [
            'code' => 'FR.AK.01',
            'title' => 'Persetujuan Asesmen dan Kerahasiaan',
            'desc' => 'Persetujuan pelaksanaan asesmen, kerahasiaan materi uji, dan hak serta kewajiban asesi.',
            'badge' => 'Persetujuan',
            'badge_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'route_code' => 'ak01',
        ],
        'IA01' => [
            'code' => 'FR.IA.01',
            'title' => 'Ceklis Observasi Aktivitas di Tempat Kerja',
            'desc' => 'Daftar periksa observasi langsung terhadap KUK dan elemen kompetensi pada unit unjuk kerja.',
            'badge' => 'Observasi Praktik',
            'badge_color' => 'bg-blue-50 text-blue-700 border-blue-200',
            'route_code' => 'ia01',
        ],
        'IA01B' => [
            'code' => 'FR.IA.01B',
            'title' => 'Lembar Pemeriksaan Teknis (Report Sheet)',
            'desc' => 'Pencatatan dan verifikasi hasil pengukuran parameter fisik komponen serta lembar kerja asesi.',
            'badge' => 'Report Sheet',
            'badge_color' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            'route_code' => 'ia01b',
        ],
        'IA02' => [
            'code' => 'FR.IA.02',
            'title' => 'Tugas Praktik Demonstrasi (TPD)',
            'desc' => 'Penilaian tugas praktik unjuk kerja berdasarkan skenario dan instruksi demonstrasi standar di TUK.',
            'badge' => 'Demonstrasi',
            'badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'route_code' => 'ia02',
        ],
        'IA03' => [
            'code' => 'FR.IA.03',
            'title' => 'Pertanyaan Mendukung Observasi (PMO / Lisan)',
            'desc' => 'Daftar pertanyaan lisan terstruktur untuk mengonfirmasi pemahaman teknis dan dasar teori asesi.',
            'badge' => 'Tanya Jawab',
            'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
            'route_code' => 'ia03',
        ],
        'IA05' => [
            'code' => 'FR.IA.05',
            'title' => 'Pertanyaan Tertulis Pilihan Ganda (CBT)',
            'desc' => 'Hasil ujian tertulis berbasis komputer (CBT) dengan auto-grading dan verifikasi nilai akhir.',
            'badge' => 'Ujian CBT',
            'badge_color' => 'bg-purple-50 text-purple-700 border-purple-200',
            'route_code' => 'ia05',
        ],
    ];

    public function __construct(
        protected SchemeInstrumentService $instrumentService
    ) {}

    public function scheduleRuangUji(Schedule $schedule): View|RedirectResponse
    {
        $this->authorizeAssessorSchedule($schedule);

        $unapprovedCount = $schedule->assessments()->where('apl_status', '!=', 'APPROVED')->count();
        if ($unapprovedCount > 0) {
            return redirect()->route('asesor.jadwal.asesi', $schedule->id)
                ->with('error', "Ruang Uji belum dapat dibuka. Masih ada {$unapprovedCount} asesi yang belum disetujui APL-nya.");
        }

        $schedule->load([
            'scheme',
            'assessments.asesi.profile',
            'assessments.iaResponses',
            'assessments.assessor',
            'assessors',
        ]);

        $selectedCodes = ! empty($schedule->selected_instruments)
            ? (array) $schedule->selected_instruments
            : ['AK01', 'IA01', 'IA01B', 'IA02', 'IA03', 'IA05'];

        $totalAsesi = $schedule->assessments->count();
        $instruments = [];

        foreach ($selectedCodes as $code) {
            $c = strtoupper($code);
            $meta = $this->instrumentsMeta[$c] ?? [
                'code' => 'FR.'.$c,
                'title' => 'Instrumen '.$c,
                'desc' => 'Instrumen asesmen kompetensi terstandar.',
                'badge' => $c,
                'badge_color' => 'bg-slate-50 text-slate-700 border-slate-200',
                'route_code' => strtolower($c),
            ];

            $completedCount = 0;
            foreach ($schedule->assessments as $ass) {
                if ($c === 'AK01') {
                    if (! empty($ass->assessor_signature) || ! empty($ass->approved_at)) {
                        $completedCount++;
                    }
                } else {
                    if ($ass->iaResponses->firstWhere('instrument_code', $c)) {
                        $completedCount++;
                    }
                }
            }

            $meta['raw_code'] = $c;
            $meta['completed_count'] = $completedCount;
            $meta['total_asesi'] = $totalAsesi;
            $meta['percentage'] = $totalAsesi > 0 ? round(($completedCount / $totalAsesi) * 100) : 0;

            $instruments[] = $meta;
        }

        return view('asesor.ruang-uji.schedule-hub', compact('schedule', 'instruments', 'totalAsesi'));
    }

    public function scheduleInstrument(Schedule $schedule, string $code): View
    {
        $this->authorizeAssessorSchedule($schedule);

        $codeUpper = strtoupper($code);
        $codeLower = strtolower($code);

        $schedule->load([
            'scheme',
            'assessments.asesi.profile',
            'assessments.iaResponses',
            'assessments.assessor',
            'assessors',
        ]);

        $meta = $this->instrumentsMeta[$codeUpper] ?? [
            'code' => 'FR.'.$codeUpper,
            'title' => 'Instrumen '.$codeUpper,
            'desc' => 'Instrumen asesmen kompetensi.',
            'badge' => $codeUpper,
            'badge_color' => 'bg-slate-50 text-slate-700 border-slate-200',
            'route_code' => $codeLower,
        ];

        $asesiList = $schedule->assessments->map(function ($assessment) use ($codeUpper, $codeLower, $schedule) {
            $response = $assessment->iaResponses->firstWhere('instrument_code', $codeUpper);
            $isCompleted = false;
            $scoreLabel = null;
            $statusLabel = 'Belum Dinilai';
            $statusClass = 'bg-slate-100 text-slate-700 border-slate-300';

            if ($codeUpper === 'AK01') {
                $isCompleted = ! empty($assessment->assessor_signature) || ! empty($assessment->approved_at);
                $statusLabel = $isCompleted ? 'Sudah Ditandatangani' : 'Belum Ditandatangani';
                $statusClass = $isCompleted ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-amber-50 text-amber-700 border-amber-300';
                $inspectUrl = route('asesor.schedule.ak01', ['schedule' => $assessment->schedule_id, 'assessment' => $assessment->id]);
            } else {
                if ($response) {
                    $isCompleted = true;
                    if ($codeUpper === 'IA05') {
                        $score = $response->score ?? ($assessment->ia05_score ?? 0);
                        $scoreLabel = "Nilai CBT: {$score}%";
                        $passingGrade = (float) ($assessment->schedule?->scheme?->passing_grade_theory ?? Setting::get('cbt_passing_grade', 75.0));
                        $statusLabel = $score >= $passingGrade ? 'Kompeten (K)' : 'Belum Kompeten (BK)';
                        $statusClass = $score >= $passingGrade ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-rose-50 text-rose-700 border-rose-300';
                    } else {
                        $statusLabel = 'Sudah Dinilai';
                        $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-300';
                    }
                }
                $inspectUrl = route('asesor.assessment.instrument', ['assessment' => $assessment->id, 'code' => $codeLower]);
            }

            $hasEvidence = ! empty($response?->data_json['evidence_image_path']);
            $user = auth()->user();
            $isSingleAssessor = $schedule->assessors->count() === 1 && $schedule->assessors->first()?->id === $user?->id;
            $isMyCandidate = (int) $assessment->assessor_id === (int) $user?->id || (empty($assessment->assessor_id) && $isSingleAssessor);

            return (object) [
                'assessment' => $assessment,
                'asesi' => $assessment->asesi,
                'is_completed' => $isCompleted,
                'status_label' => $statusLabel,
                'status_class' => $statusClass,
                'score_label' => $scoreLabel,
                'inspect_url' => $inspectUrl,
                'has_evidence' => $hasEvidence,
                'is_my_candidate' => $isMyCandidate,
            ];
        });

        return view('asesor.ruang-uji.instrument-asesi-list', compact('schedule', 'codeUpper', 'codeLower', 'meta', 'asesiList'));
    }

    public function ak01Schedule(Schedule $schedule, Request $request)
    {
        $this->authorizeAssessorSchedule($schedule);

        $assessmentId = $request->query('assessment');
        if ($assessmentId) {
            $assessment = Assessment::with('schedule.scheme', 'asesi.profile', 'assessor')->findOrFail($assessmentId);
            $this->authorizeAssessorAssessment($assessment);

            return view('asesor.ak01', compact('schedule', 'assessment'));
        }

        return redirect()->route('asesor.schedule.instrument', ['schedule' => $schedule->id, 'code' => 'ak01']);
    }

    public function ak01Sign(Assessment $assessment, Request $request): RedirectResponse
    {
        $this->authorizeAssessorAssessment($assessment);

        $request->validate([
            'assessor_signature' => ['nullable', 'string', new ValidSignature],
            'signature_data' => ['nullable', 'string', new ValidSignature],
        ]);

        $signature = $request->input('assessor_signature') ?? $request->input('signature_data');
        $assessment->update([
            'assessor_signature' => $signature,
            'assessor_signed_at' => now(),
            'assessor_id' => auth()->id(),
        ]);

        return redirect()->route('asesor.schedule.instrument', [
            'schedule' => $assessment->schedule_id,
            'code' => 'ak01',
        ])->with('success', "FR.AK01 untuk {$assessment->asesi->full_name} berhasil ditandatangani.");
    }

    public function hub(Assessment $assessment): View
    {
        $this->authorizeAssessorAssessment($assessment);

        $assessment->load('schedule.scheme', 'asesi.profile', 'iaResponses');

        return view('asesor.ruang-uji.hub', compact('assessment'));
    }

    public function instrumentShow(Assessment $assessment, string $code): View
    {
        $this->authorizeAssessorAssessment($assessment);

        $code = strtolower($code);
        if ($code === 'ak01') {
            $assessment->load('schedule.scheme', 'asesi.profile', 'assessor');
            $schedule = $assessment->schedule;

            return view('asesor.ak01', compact('assessment', 'schedule'));
        }

        abort_unless(isset(self::INSTRUMENT_MAP[$code]), 404, 'Instrumen tidak dikenali.');

        $mapping = self::INSTRUMENT_MAP[$code];
        $relations = ['schedule.scheme', 'asesi.profile', 'iaResponses'];
        if ($code === 'ia01') {
            $relations[] = 'schedule.scheme.competencyUnits.elements.kuks';
        }
        $assessment->load($relations);

        $codeUpper = strtoupper($code);
        $response = $assessment->iaResponses->firstWhere('instrument_code', $codeUpper);
        $scheme = $assessment->schedule->scheme;

        $savedData = is_array($response?->data_json) ? $response->data_json : (json_decode($response?->data_json ?? '[]', true) ?: []);
        $savedAnswers = $savedData['answers'] ?? $savedData['kuks'] ?? $savedData['tasks'] ?? $savedData['questions'] ?? $savedData['items'] ?? $savedData;
        $savedNotes = $response?->assessor_notes;
        $savedGeneralNotes = $response?->assessor_notes;
        $savedSignature = $response?->assessor_signature ?? auth()->user()->profile?->signature_data;
        $isCompleted = (bool) $response;
        $nextAssessment = $this->getNextAssessment($assessment, $code);
        $nextCandidate = $nextAssessment?->asesi;

        $resolvedData = $this->{$mapping['resolver']}($scheme, $assessment, $response, $savedData);

        return view($mapping['view'], array_merge([
            'assessment' => $assessment,
            'code' => $code,
            'response' => $response,
            'scheme' => $scheme,
            'savedAnswers' => $savedAnswers,
            'savedNotes' => $savedNotes,
            'savedGeneralNotes' => $savedGeneralNotes,
            'savedSignature' => $savedSignature,
            'isCompleted' => $isCompleted,
            'nextCandidate' => $nextCandidate,
        ], $resolvedData));
    }

    public function instrumentStore(Assessment $assessment, string $code, Request $request): RedirectResponse
    {
        $this->authorizeAssessorAssessment($assessment);

        $code = strtoupper($code);
        if ($assessment->schedule?->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah ditutup dan dikunci.');
        }

        if ($assessment->apl_status !== 'APPROVED') {
            return back()->with('error', 'Penilaian instrumen hanya dapat direkam untuk asesi yang permohonan APL-nya telah disetujui.');
        }

        if (! $assessment->esign_consent_at) {
            return back()->with('error', 'Penilaian instrumen belum dapat disimpan karena asesi belum memberikan persetujuan klausul tanda tangan elektronik (e-sign consent). Asesi harus menyetujui klausul tersebut terlebih dahulu.');
        }

        $request->validate([
            'signature_data' => ['nullable', 'string', new ValidSignature],
            'assessor_signature' => ['nullable', 'string', new ValidSignature],
        ]);

        $existing = AssessmentIaResponse::where('assessment_id', $assessment->id)
            ->where('instrument_code', $code)
            ->first();

        // Stale save protection (optimistic locking)
        if ($request->filled('lock_version') && $existing) {
            if ((int) $request->input('lock_version') !== (int) ($existing->attempt_version ?? 1)) {
                abort(409, 'Konflik versi: dokumen telah diperbarui pada sesi lain. Muat ulang halaman.');
            }
        }

        // Instrument specific validation gates
        if ($code === 'IA01') {
            $scheme = $assessment->schedule?->scheme ?? $assessment->scheme;
            if ($scheme) {
                $submittedKukIds = array_keys($request->input('kuks', []));
                $validKukIds = \App\Models\Kuk::whereHas('unitElement.competencyUnit', function ($q) use ($scheme) {
                    $q->where('scheme_id', $scheme->id);
                })->pluck('id')->map(fn ($id) => (string) $id)->toArray();

                foreach ($submittedKukIds as $kId) {
                    if (! empty($validKukIds) && ! in_array((string) $kId, $validKukIds, true)) {
                        return back()->withErrors(["kuks.{$kId}" => "KUK ID #{$kId} tidak dikenal. Foreign KUK ditolak."])->withInput();
                    }
                }
            }
        } elseif ($code === 'IA01B') {
            $user = auth()->user();
            if ($user && $user->id === $assessment->asesi_id && ! $user->isAdmin()) {
                foreach ($request->input('items', []) as $idx => $item) {
                    if (isset($item['result']) && trim((string) $item['result']) !== '') {
                        return back()->withErrors(["items.{$idx}.result" => 'Kolom penilaian kesesuaian merupakan kewenangan asesor. Unauthorized role column ditolak.'])->withInput();
                    }
                }
            }
        }

        $data = $request->input('data') ?? [
            'kuks' => $request->input('kuks'),
            'tasks' => $request->input('tasks'),
            'questions' => $request->input('questions'),
            'items' => $request->input('items'),
        ];

        $existingData = is_array($existing?->data_json)
            ? $existing->data_json
            : (json_decode($existing?->data_json ?? '[]', true) ?: []);

        if (isset($existingData['evidence_image_path'])) {
            $data['evidence_image_path'] = $existingData['evidence_image_path'];
            $data['evidence_uploaded_at'] = $existingData['evidence_uploaded_at'] ?? null;
            $data['evidence_original_name'] = $existingData['evidence_original_name'] ?? null;
        }

        if ($request->hasFile('evidence_image')) {
            $request->validate(['evidence_image' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:10240']);
            $path = $request->file('evidence_image')->store('ia02_evidence', 'local');
            $data['evidence_image_path'] = $path;
            $data['evidence_uploaded_at'] = now()->toIso8601String();
            $data['evidence_original_name'] = $request->file('evidence_image')->getClientOriginalName();
        }

        $data['audit_trail'] = [
            'evaluated_by_id' => auth()->id(),
            'evaluated_by_name' => auth()->user()?->name,
            'evaluated_at' => now()->toIso8601String(),
            'ip_address' => $request->ip(),
        ];

        Log::info('Asesor ID '.auth()->id()." ({$data['audit_trail']['evaluated_by_name']}) menyimpan penilaian {$code} untuk asesmen #{$assessment->id} (asesi #{$assessment->asesi_id})");

        $asesiSig = null;
        $asesiSignedAt = null;
        if ($assessment->esign_consent_at) {
            $asesiSig = $existing?->asesi_signature ?? $assessment->asesi_signature ?? $assessment->asesi?->profile?->signature_data;
            $asesiSignedAt = $existing?->asesi_signed_at ?? ($asesiSig ? now() : null);
        }

        DB::transaction(function () use ($assessment, $code, $data, $request, $asesiSig, $asesiSignedAt) {
            $resp = AssessmentIaResponse::updateOrCreate(
                ['assessment_id' => $assessment->id, 'instrument_code' => $code],
                [
                    'data_json' => $data,
                    'assessor_notes' => $request->input('general_notes') ?? $request->input('notes') ?? $request->input('assessor_notes'),
                    'assessor_signature' => $request->input('signature_data') ?? $request->input('assessor_signature'),
                    'assessor_signed_at' => now(),
                    'asesi_signature' => $asesiSig,
                    'asesi_signed_at' => $asesiSignedAt,
                ]
            );

            $resp->increment('attempt_version');

            if ($assessment->schedule) {
                app(\App\Services\AttendanceService::class)->recordAssessorActive(
                    $assessment->schedule,
                    auth()->user(),
                    "instrument:save:{$code}"
                );
            }

            \App\Models\AuditEvent::record(
                action: 'instrument.saved',
                subject: $assessment,
                reason: "Penilaian instrumen {$code} disimpan oleh " . auth()->user()->name,
                details: ['code' => $code, 'assessment_id' => $assessment->id]
            );
        });

        // Flow: Simpan & Lanjut ke Asesi Berikutnya
        if ($request->input('save_action') === 'next') {
            $nextAssessment = $this->getNextAssessment($assessment, $code);

            if ($nextAssessment) {
                return redirect()->route('asesor.assessment.instrument', [
                    'assessment' => $nextAssessment->id,
                    'code' => strtolower($code),
                ])->with('success', "Penilaian {$code} untuk {$assessment->asesi->full_name} tersimpan. Melanjutkan ke: {$nextAssessment->asesi->full_name}.");
            }

            return redirect()->route('asesor.schedule.instrument', [
                'schedule' => $assessment->schedule_id,
                'code' => strtolower($code),
            ])->with('success', "Penilaian {$code} untuk {$assessment->asesi->full_name} tersimpan. Seluruh asesi telah selesai dinilai.");
        }

        return redirect()->route('asesor.schedule.instrument', [
            'schedule' => $assessment->schedule_id,
            'code' => strtolower($code),
        ])->with('success', "Penilaian {$code} untuk {$assessment->asesi->full_name} berhasil disimpan.");
    }

    protected function getNextAssessment(Assessment $assessment, string $code): ?Assessment
    {
        $codeUpper = strtoupper($code);

        return Assessment::where('schedule_id', $assessment->schedule_id)
            ->where('id', '!=', $assessment->id)
            ->where('apl_status', 'APPROVED')
            ->whereDoesntHave('iaResponses', function ($query) use ($codeUpper) {
                $query->where('instrument_code', $codeUpper)
                    ->whereNotNull('assessor_signed_at');
            })
            ->with('asesi.profile')
            ->orderBy('id')
            ->first();
    }

    private function resolveIa01($scheme, $assessment, $response, array $savedData): array
    {
        return ['units' => $scheme->competencyUnits];
    }

    private function resolveIa01b($scheme, $assessment, $response, array $savedData): array
    {
        return ['reportSheetItems' => $this->instrumentService->getReportSheetItems($scheme)];
    }

    private function resolveIa02($scheme, $assessment, $response, array $savedData): array
    {
        $tpdData = $this->instrumentService->getTpdData($scheme);
        $tasks = collect($tpdData['tasks'])->map(fn ($t) => (object) [
            'id' => $t['id'],
            'instruction' => $t['instruction'],
            'title' => $t['instruction'],
        ]);

        return ['tpdData' => $tpdData, 'tasks' => $tasks];
    }

    private function resolveIa03($scheme, $assessment, $response, array $savedData): array
    {
        $pmoQuestions = $this->instrumentService->getPmoQuestions($scheme);
        $questions = collect($pmoQuestions)->values()->map(function ($q, $idx) {
            $num = $idx + 1;

            return (object) [
                'id' => $num,
                'number' => $num,
                'question' => $q['question'] ?? "Pertanyaan PMO #{$num}",
                'expected_answer' => $q['expected_answer'] ?? '',
            ];
        });

        return ['questions' => $questions, 'pmoQuestions' => $pmoQuestions];
    }

    private function resolveIa05($scheme, $assessment, $response, array $savedData): array
    {
        return [
            'breakdown' => $savedData['breakdown'] ?? $savedData['items'] ?? [],
            'score' => $response?->score ?? $savedData['score'] ?? ($assessment->ia05_score ?? 0),
        ];
    }
}
