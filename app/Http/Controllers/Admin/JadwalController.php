<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use App\Services\AkWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = Schedule::with('scheme', 'assessors', 'creator')
            ->latest('date')
            ->paginate(15);

        return view('admin.jadwal.index', compact('schedules'));
    }

    public function create(): View
    {
        $schemes = Scheme::active()->get();
        $assessors = User::where('role', 'ASESOR')->where('status', 'ACTIVE')->get();

        return view('admin.jadwal.create', compact('schemes', 'assessors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'scheme_id' => 'required|exists:m_schemes,id',
            'tuk_name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'selected_instruments' => 'nullable|array',
            'notes' => 'nullable|string',
            'assessor_ids' => 'nullable|array',
            'assessors' => 'nullable|array',
            'code' => 'nullable|string|max:50',
        ]);

        $code = $request->input('code') ?: strtoupper(Str::random(8));

        $schedule = Schedule::create([
            'name' => $validated['name'],
            'scheme_id' => $validated['scheme_id'],
            'tuk_name' => $validated['tuk_name'] ?? null,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'selected_instruments' => $validated['selected_instruments'] ?? [],
            'notes' => $validated['notes'] ?? null,
            'code' => $code,
            'status' => 'ACTIVE',
            'created_by' => auth()->id(),
        ]);

        $assessorList = $request->input('assessor_ids', $request->input('assessors', []));
        if (! empty($assessorList)) {
            $schedule->assessors()->sync($assessorList);
        }

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dibuat.');
    }

    public function show(Schedule $jadwal): View
    {
        $jadwal->load('scheme', 'assessors', 'assessments.asesi.profile');

        return view('admin.jadwal.show', compact('jadwal'));
    }

    public function edit(Schedule $jadwal): View
    {
        $schemes = Scheme::active()->get();
        $assessors = User::where('role', 'ASESOR')->where('status', 'ACTIVE')->get();
        $jadwal->load('assessors');

        return view('admin.jadwal.edit', compact('jadwal', 'schemes', 'assessors'));
    }

    public function update(Request $request, Schedule $jadwal): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'scheme_id' => 'required|exists:m_schemes,id',
            'tuk_name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'selected_instruments' => 'nullable|array',
            'instruments' => 'nullable|array',
            'notes' => 'nullable|string',
            'assessor_ids' => 'nullable|array',
        ]);

        $instruments = $request->input('selected_instruments', $request->input('instruments', $jadwal->selected_instruments));
        $validated['selected_instruments'] = $instruments;
        unset($validated['instruments']);

        $jadwal->update($validated);

        if ($request->has('assessor_ids')) {
            $jadwal->assessors()->sync($request->input('assessor_ids', []));
        }

        return redirect()->route('admin.jadwal.show', $jadwal->id)
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $jadwal): RedirectResponse
    {
        if ($jadwal->assessments()->exists()) {
            $jadwal->update(['status' => 'FINISHED']);

            return redirect()->route('admin.jadwal.index')
                ->with('warning', 'Jadwal tidak dapat dihapus permanen karena sudah memiliki peserta terdaftar. Status jadwal telah ditutup (FINISHED).');
        }

        $jadwal->assessors()->detach();
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    public function reviewRegistration(Assessment $assessment, Request $request): RedirectResponse
    {
        $status = strtoupper((string) ($request->input('admin_status') ?? $request->input('status') ?? 'APPROVED'));
        $notes = $request->input('admin_notes') ?? $request->input('notes');

        app(AkWorkflowService::class)->approveCandidateRegistration(
            $assessment,
            $status,
            $notes,
            auth()->id()
        );

        return back()->with('success', "Status pendaftaran asesi {$assessment->asesi->full_name} berhasil diperbarui menjadi {$status}.");
    }

    public function batchReviewRegistration(Schedule $jadwal, Request $request): RedirectResponse
    {
        $status = strtoupper((string) ($request->input('admin_status') ?? 'APPROVED'));
        $notes = $request->input('admin_notes', 'Persetujuan batch verifikasi berkas admin');
        $workflowService = app(AkWorkflowService::class);
        $approvedCount = 0;
        $skippedCandidates = [];

        $jadwal->load('assessments.proofs', 'assessments.asesi');

        foreach ($jadwal->assessments as $assessment) {
            // When approving, only allow candidates with complete proofs (KTP + Pas Foto + Bukti Dasar)
            if ($status === 'APPROVED') {
                if (! $workflowService->isCandidateDocumentComplete($assessment)) {
                    $skippedCandidates[] = $assessment->asesi->full_name ?? "Asesi #{$assessment->id}";

                    continue;
                }
            }

            if (($assessment->admin_status ?? 'PENDING') !== $status) {
                $workflowService->approveCandidateRegistration(
                    $assessment,
                    $status,
                    $notes,
                    auth()->id()
                );
                $approvedCount++;
            }
        }

        if (count($skippedCandidates) > 0) {
            $skippedNames = implode(', ', $skippedCandidates);

            return back()->with('success', "Berhasil menyetujui {$approvedCount} peserta dengan berkas lengkap.")
                ->with('warning', 'Sebanyak '.count($skippedCandidates)." peserta dilewati karena berkas belum lengkap (KTP, Pas Foto, atau Bukti Dasar): {$skippedNames}.");
        }

        return back()->with('success', "Berhasil memperbarui status {$approvedCount} pendaftar menjadi {$status}.");
    }

    public function markAbsent(Assessment $assessment, Request $request): RedirectResponse
    {
        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah ditutup dan dikunci.');
        }

        $notes = $request->input('notes') ?? $request->input('final_notes');
        app(AkWorkflowService::class)->markCandidateAbsent($assessment, $notes, auth()->id());

        return back()->with('success', "Asesi {$assessment->asesi->full_name} berhasil ditandai TIDAK HADIR.");
    }
}
