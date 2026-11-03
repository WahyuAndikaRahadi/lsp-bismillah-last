<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RecertificationController extends Controller
{
    /**
     * Tampilkan formulir permohonan sertifikasi ulang (resertifikasi) dari sertifikat lama.
     */
    public function create(Certificate $certificate): View|RedirectResponse
    {
        abort_if($certificate->assessment?->asesi_id !== Auth::id(), 403, 'Akses ditolak. Anda bukan pemilik sah sertifikat ini.');

        $certificate->load(['assessment.schedule.scheme', 'assessment.asesi.profile']);
        $scheme = $certificate->assessment?->schedule?->scheme ?? $certificate->assessment?->scheme;

        if (! $scheme) {
            return redirect()->route('asesi.certificates.index')
                ->with('error', 'Skema sertifikasi terkait tidak ditemukan.');
        }

        // Cari jadwal aktif yang sesuai dengan skema sertifikat
        $availableSchedules = Schedule::where('scheme_id', $scheme->id)
            ->where('is_closed', false)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get();

        return view('asesi.recertification.create', compact('certificate', 'scheme', 'availableSchedules'));
    }

    /**
     * Simpan permohonan resertifikasi dan buat assessment baru yang tertaut ke sertifikat sebelumnya.
     */
    public function store(Request $request, Certificate $certificate): RedirectResponse
    {
        abort_if($certificate->assessment?->asesi_id !== Auth::id(), 403, 'Akses ditolak. Anda bukan pemilik sah sertifikat ini.');

        $validated = $request->validate([
            'schedule_id' => ['required', 'exists:t_schedules,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'schedule_id.required' => 'Jadwal pelaksanaan asesmen resertifikasi wajib dipilih.',
        ]);

        $schedule = Schedule::findOrFail($validated['schedule_id']);
        $schemeId = $certificate->assessment?->scheme_id ?? $certificate->assessment?->schedule?->scheme_id;

        if ((int) $schedule->scheme_id !== (int) $schemeId) {
            return back()->withInput()->with('error', 'Jadwal yang dipilih tidak sesuai dengan skema sertifikat yang akan diperpanjang.');
        }

        // Hindari permohonan ganda yang masih berjalan
        $existing = Assessment::where('prior_certificate_id', $certificate->id)
            ->whereIn('status', ['PENDING', 'IN_PROGRESS'])
            ->first();

        if ($existing) {
            return redirect()->route('asesi.jadwal.index')
                ->with('info', 'Permohonan resertifikasi untuk sertifikat ini telah tercatat dan sedang dalam proses.');
        }

        $user = Auth::user();
        $profile = $user->profile;

        DB::beginTransaction();
        try {
            // Buat assessment baru yang tertaut ke sertifikat lama (SOP 04 §5)
            $newAssessment = Assessment::create([
                'schedule_id' => $schedule->id,
                'scheme_version_id' => $schedule->scheme_version_id ?: $certificate->assessment?->scheme_version_id,
                'asesi_id' => $user->id,
                'prior_certificate_id' => $certificate->id,
                'snapshot_profile' => $profile ? $profile->toArray() : null,
                'apl_status' => 'SUBMITTED',
                'status' => 'PENDING',
                'registered_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('asesi.jadwal.index')
                ->with('success', "Permohonan resertifikasi untuk skema {$schedule->scheme?->name} berhasil didaftarkan. Profil Anda digunakan kembali dan tertaut pada sertifikat lama no. {$certificate->certificate_number}.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal memproses permohonan resertifikasi: ' . $e->getMessage());
        }
    }
}
