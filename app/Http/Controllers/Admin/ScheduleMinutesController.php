<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleMinutes;
use App\Models\User;
use App\Rules\ValidSignature;
use App\Services\ScheduleMinutesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleMinutesController extends Controller
{
    public function __construct(
        protected ScheduleMinutesService $minutesService
    ) {}

    /**
     * Tampilkan formulir Berita Acara Pelaksanaan Uji Kompetensi (BA FR-SOP-04.4).
     */
    public function show(Schedule $schedule): View
    {
        $facts = $this->minutesService->aggregateScheduleFacts($schedule);

        $latestMinutes = ScheduleMinutes::where('schedule_id', $schedule->id)
            ->latest('revision')
            ->first();

        $coordinators = User::whereIn('role', ['admin', 'asesor'])->orderBy('name')->get();
        $assessors = $schedule->assessors;

        return view('admin.jadwal.minutes', compact('schedule', 'facts', 'latestMinutes', 'coordinators', 'assessors'));
    }

    /**
     * Simpan rekaman Berita Acara Pelaksanaan Uji Kompetensi.
     */
    public function store(Schedule $schedule, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_code' => 'nullable|string|max:50',
            'actual_date' => 'required|date',
            'actual_start_time' => 'required|string',
            'actual_end_time' => 'required|string',
            'location' => 'nullable|string|max:255',
            'coordinator_id' => 'required|exists:users,id',
            'coordinator_name' => 'nullable|string|max:255',
            'coordinator_signature' => ['nullable', 'string', new ValidSignature],
            'lead_assessor_id' => 'nullable|exists:users,id',
            'lead_assessor_signature' => ['nullable', 'string', new ValidSignature],
            'notes' => 'nullable|string|max:2000',
            'status' => 'required|in:DRAFT,FINAL',
            'lock_version' => 'nullable|integer',
        ], [
            'coordinator_id.required' => 'Koordinator pelaksanaan uji kompetensi wajib dipilih dari personel yang berwenang.',
            'actual_start_time.required' => 'Waktu mulai pelaksanaan aktual wajib diisi.',
            'actual_end_time.required' => 'Waktu selesai pelaksanaan aktual wajib diisi.',
        ]);

        $this->minutesService->recordMinutes($schedule, $validated, auth()->user());

        return redirect()->route('admin.schedules.minutes.show', $schedule->id)
            ->with('success', 'Berita Acara Pelaksanaan Uji Kompetensi berhasil disimpan.');
    }

    /**
     * Tampilkan rekapitulasi daftar hadir fakta (FR-SOP-04.3).
     */
    public function attendance(Schedule $schedule): View
    {
        $facts = $this->minutesService->aggregateScheduleFacts($schedule);

        $schedule->load(['assessors', 'tuk', 'scheme']);

        return view('admin.jadwal.attendance', compact('schedule', 'facts'));
    }
}
