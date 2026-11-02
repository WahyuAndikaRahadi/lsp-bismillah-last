<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::query()->withCount('schedules');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_event', 'like', "%{$q}%")
                    ->orWhere('nama_event', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }

        if ($request->filled('sumber')) {
            $query->where('sumber_anggaran', $request->sumber);
        }

        $events = $query->latest('tgl_mulai')->paginate(10)->withQueryString();
        $years = Event::distinct()->pluck('tahun_anggaran')->sortDesc();

        return view('admin.events.index', compact('events', 'years'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_event' => ['required', 'string', 'max:50', 'unique:events,kode_event'],
            'nama_event' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'min:2020', 'max:2099'],
            'sumber_anggaran' => ['required', 'string', 'max:50'],
            'tgl_mulai' => ['required', 'date'],
            'tgl_selesai' => ['required', 'date', 'after_or_equal:tgl_mulai'],
            'keterangan' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event sertifikasi berhasil ditambahkan.');
    }

    public function show(Event $event): View
    {
        $event->load([
            'schedules.scheme',
            'schedules.tuk',
        ]);

        return view('admin.events.show', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'kode_event' => ['required', 'string', 'max:50', 'unique:events,kode_event,'.$event->id],
            'nama_event' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'min:2020', 'max:2099'],
            'sumber_anggaran' => ['required', 'string', 'max:50'],
            'tgl_mulai' => ['required', 'date'],
            'tgl_selesai' => ['required', 'date', 'after_or_equal:tgl_mulai'],
            'keterangan' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $event->update($validated);

        return back()->with('success', 'Data event sertifikasi berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->schedules()->exists()) {
            return back()->with('error', 'Event tidak dapat dihapus karena sudah memiliki jadwal asesmen yang terhubung.');
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event sertifikasi berhasil dihapus.');
    }
}
