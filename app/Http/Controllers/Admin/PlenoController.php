<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\PlenoMeeting;
use App\Models\Schedule;
use App\Services\CertificationDecisionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PlenoController extends Controller
{
    public function __construct(
        protected CertificationDecisionService $decisionService
    ) {}

    /**
     * Daftar jadwal asesmen untuk penetapan rapat pleno
     */
    public function index(Request $request): View
    {
        $schedules = Schedule::with([
            'scheme',
            'tuk',
            'assessments.ak02Items',
            'plenoMeeting',
            'assessors',
        ])
            ->latest('date')
            ->paginate(10);

        return view('admin.pleno.index', compact('schedules'));
    }

    /**
     * Lembar kerja Rapat Pleno penetapan kelulusan
     */
    public function show(Schedule $schedule): View
    {
        $schedule->load([
            'scheme.competencyUnits',
            'tuk',
            'assessments.asesi.profile',
            'assessments.assessor',
            'assessments.ak02Items',
            'assessors',
            'plenoMeeting',
        ]);

        $pleno = $schedule->plenoMeeting ?? new PlenoMeeting([
            'schedule_id' => $schedule->id,
            'nomor_ba_pleno' => PlenoMeeting::generateNomorBaPleno(),
            'tanggal_pleno' => now()->toDateString(),
            'tempat' => 'Ruang Rapat Pleno LSP / Daring',
            'ketua_komite' => Auth::user()?->name ?? 'Ketua Komite Teknis',
            'anggota_komite' => "1. Ketua Komite Teknis\n2. Manajer Sertifikasi\n3. Perwakilan Pengurus LSP",
        ]);

        return view('admin.pleno.show', compact('schedule', 'pleno'));
    }

    /**
     * Simpan penetapan Rapat Pleno & keputusan sertifikasi independen.
     */
    public function store(Request $request, Schedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_ba_pleno' => ['required', 'string', 'max:100'],
            'tanggal_pleno' => ['required', 'date'],
            'tempat' => ['required', 'string', 'max:255'],
            'ketua_komite' => ['required', 'string', 'max:100'],
            'anggota_komite' => ['nullable', 'string'],
            'catatan_pleno' => ['nullable', 'string'],
            'decisions' => ['required', 'array'],
            'decisions.*.decision' => ['required', 'in:KOMPETEN,BELUM_KOMPETEN,CORRECTION_REQUESTED,kompeten,belum_kompeten,k,bk'],
            'decisions.*.notes' => ['nullable', 'string'],
            'decisions.*.corrective_action' => ['nullable', 'string'],
        ], [
            'decisions.required' => 'Keputusan sertifikasi untuk asesi pada jadwal ini wajib ditetapkan.',
        ]);

        $actor = Auth::user();

        DB::beginTransaction();
        try {
            // 1. Simpan Record Pleno Meeting
            $pleno = PlenoMeeting::updateOrCreate(
                ['schedule_id' => $schedule->id],
                [
                    'nomor_ba_pleno' => $validated['nomor_ba_pleno'],
                    'tanggal_pleno' => $validated['tanggal_pleno'],
                    'tempat' => $validated['tempat'],
                    'ketua_komite' => $validated['ketua_komite'],
                    'anggota_komite' => $validated['anggota_komite'] ?? null,
                    'catatan_pleno' => $validated['catatan_pleno'] ?? null,
                    'status' => 'disahkan',
                    'ttd_ketua' => $actor->profile?->signature_data ?? $actor->signature_image,
                ]
            );

            // 2. Catat keputusan sertifikasi per assessment via CertificationDecisionService
            foreach ($validated['decisions'] as $assessmentId => $item) {
                $assessment = Assessment::where('id', $assessmentId)
                    ->where('schedule_id', $schedule->id)
                    ->first();

                if (! $assessment) {
                    continue;
                }

                $this->decisionService->recordDecision(
                    assessment: $assessment,
                    decision: $item['decision'],
                    actor: $actor,
                    authorityRole: $validated['ketua_komite'],
                    decisionNotes: $item['notes'] ?? null,
                    correctiveAction: $item['corrective_action'] ?? null,
                    validationNotes: 'Diverifikasi dalam Rapat Pleno Komite Teknis',
                    plenoMeetingId: $pleno->id
                );
            }

            // 3. Update status jadwal ke FINISHED bila semua selesai
            $schedule->update([
                'status' => 'FINISHED',
            ]);

            DB::commit();

            return redirect()->route('admin.pleno.show', $schedule)
                ->with('success', "Keputusan Rapat Pleno untuk jadwal {$schedule->name} ({$schedule->code}) berhasil disahkan.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal memproses penetapan pleno: ' . $e->getMessage());
        }
    }
}
