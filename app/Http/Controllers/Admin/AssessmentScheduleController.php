<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ScheduleStatus;
use App\Http\Controllers\Controller;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Models\Tuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssessmentScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $query = AssessmentSchedule::query()->with(['event', 'scheme', 'tuk']);

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_jadwal', 'like', "%{$q}%")
                    ->orWhere('nama_jadwal', 'like', "%{$q}%")
                    ->orWhere('id_jadwal_bnsp', 'like', "%{$q}%")
                    ->orWhereHas('scheme', function ($s) use ($q) {
                        $s->where('nama_skema', 'like', "%{$q}%");
                    })
                    ->orWhereHas('tuk', function ($t) use ($q) {
                        $t->where('nama_tuk', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('scheme_id')) {
            $query->where('scheme_id', $request->scheme_id);
        }

        $schedules = $query->latest('tgl_asesmen')->paginate(10)->withQueryString();

        $events = Event::where('is_active', true)->get();
        $schemes = CertificationScheme::where('is_active', true)->get();
        $tuks = Tuk::where('is_active', true)->get();

        return view('admin.schedules.index', compact('schedules', 'events', 'schemes', 'tuks'));
    }

    public function create(): View
    {
        $events = Event::where('is_active', true)->get();
        $schemes = CertificationScheme::where('is_active', true)->get();
        $tuks = Tuk::where('is_active', true)->get();

        return view('admin.schedules.create', compact('events', 'schemes', 'tuks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'scheme_id' => ['required', 'exists:certification_schemes,id'],
            'tuk_id' => ['required', 'exists:tuks,id'],
            'kode_jadwal' => ['required', 'string', 'max:50', 'unique:assessment_schedules,kode_jadwal'],
            'nama_jadwal' => ['required', 'string', 'max:255'],
            'tgl_pra_asesmen' => ['nullable', 'date'],
            'tgl_asesmen' => ['required', 'date'],
            'tgl_selesai' => ['nullable', 'date', 'after_or_equal:tgl_asesmen'],
            'tgl_buka_daftar' => ['nullable', 'date'],
            'tgl_tutup_daftar' => ['nullable', 'date', 'after_or_equal:tgl_buka_daftar'],
            'kuota_peserta' => ['required', 'integer', 'min:1'],
            'id_jadwal_bnsp' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::enum(ScheduleStatus::class)],
            'keterangan' => ['nullable', 'string'],
        ]);

        $schedule = AssessmentSchedule::create($validated);

        return redirect()->route('admin.schedules.show', $schedule)
            ->with('success', 'Jadwal uji kompetensi berhasil dibuat.');
    }

    public function show(AssessmentSchedule $schedule): View
    {
        $schedule->load([
            'event',
            'scheme.units',
            'tuk',
        ]);

        return view('admin.schedules.show', compact('schedule'));
    }

    public function edit(AssessmentSchedule $schedule): View
    {
        $events = Event::where('is_active', true)->get();
        $schemes = CertificationScheme::where('is_active', true)->get();
        $tuks = Tuk::where('is_active', true)->get();

        return view('admin.schedules.edit', compact('schedule', 'events', 'schemes', 'tuks'));
    }

    public function update(Request $request, AssessmentSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'scheme_id' => ['required', 'exists:certification_schemes,id'],
            'tuk_id' => ['required', 'exists:tuks,id'],
            'kode_jadwal' => ['required', 'string', 'max:50', 'unique:assessment_schedules,kode_jadwal,' . $schedule->id],
            'nama_jadwal' => ['required', 'string', 'max:255'],
            'tgl_pra_asesmen' => ['nullable', 'date'],
            'tgl_asesmen' => ['required', 'date'],
            'tgl_selesai' => ['nullable', 'date', 'after_or_equal:tgl_asesmen'],
            'tgl_buka_daftar' => ['nullable', 'date'],
            'tgl_tutup_daftar' => ['nullable', 'date', 'after_or_equal:tgl_buka_daftar'],
            'kuota_peserta' => ['required', 'integer', 'min:1'],
            'id_jadwal_bnsp' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::enum(ScheduleStatus::class)],
            'keterangan' => ['nullable', 'string'],
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.show', $schedule)
            ->with('success', 'Data jadwal uji kompetensi berhasil diperbarui.');
    }

    public function updateStatus(Request $request, AssessmentSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(ScheduleStatus::class)],
        ]);

        $schedule->update($validated);

        return back()->with('success', "Status jadwal berhasil diubah menjadi: {$schedule->status->label()}.");
    }

    public function destroy(AssessmentSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal uji kompetensi berhasil dihapus.');
    }
}
