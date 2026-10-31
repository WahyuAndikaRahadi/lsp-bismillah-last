<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentVerificationStatus;
use App\Enums\ScheduleStatus;
use App\Http\Controllers\Controller;
use App\Mail\ApplicationStatusUpdatedMail;
use App\Models\ApplicationDocument;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Tampilkan daftar antrean verifikasi berkas APL-01
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'diajukan'); // Default tab antrean diajukan
        $schemeId = $request->query('scheme_id');
        $search = $request->query('search');

        $query = AssessmentApplication::with(['user.asesiProfile', 'scheme', 'schedule.tuk', 'documents'])
            ->latest('tgl_pengajuan');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($schemeId) {
            $query->where('scheme_id', $schemeId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pendaftaran', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->paginate(12)->withQueryString();

        // Hitung statistik untuk pill filter
        $counts = [
            'all' => AssessmentApplication::count(),
            'diajukan' => AssessmentApplication::where('status', ApplicationStatus::Diajukan)->count(),
            'revisi' => AssessmentApplication::where('status', ApplicationStatus::Revisi)->count(),
            'diterima' => AssessmentApplication::where('status', ApplicationStatus::Diterima)->count(),
            'ditolak' => AssessmentApplication::where('status', ApplicationStatus::Ditolak)->count(),
        ];

        $schemes = CertificationScheme::where('is_active', true)->orderBy('nama_skema')->get();

        $activeSchedules = AssessmentSchedule::whereIn('status', [ScheduleStatus::Pendaftaran, ScheduleStatus::Direncanakan])
            ->with(['scheme', 'tuk'])
            ->orderBy('tgl_asesmen')
            ->get();

        return view('admin.verifications.index', [
            'applications' => $applications,
            'counts' => $counts,
            'currentStatus' => $status,
            'schemes' => $schemes,
            'currentSchemeId' => $schemeId,
            'search' => $search,
            'activeSchedules' => $activeSchedules,
        ]);
    }

    /**
     * Tampilkan detail dokumen APL-01 dan lembar verifikasi berkas
     */
    public function show(AssessmentApplication $application): View
    {
        $application->load([
            'user.asesiProfile',
            'scheme.competencyUnits.elements.criterias',
            'scheme.requirements',
            'schedule.tuk',
            'documents.requirement',
            'documents.verifier',
            'admin',
        ]);

        $availableSchedules = AssessmentSchedule::where('scheme_id', $application->scheme_id)
            ->whereIn('status', [ScheduleStatus::Pendaftaran, ScheduleStatus::Direncanakan])
            ->with('tuk')
            ->orderBy('tgl_asesmen')
            ->get();

        return view('admin.verifications.show', [
            'application' => $application,
            'profile' => $application->user->asesiProfile,
            'availableSchedules' => $availableSchedules,
        ]);
    }

    /**
     * AJAX/Quick Verifikasi satu per satu dokumen
     */
    public function verifyDocument(Request $request, ApplicationDocument $document): JsonResponse|RedirectResponse
    {
        $request->validate([
            'status_verifikasi' => ['required', 'in:memenuhi,tidak_memenuhi,pending'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $document->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan' => $request->catatan,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status verifikasi berkas diperbarui.',
                'document' => [
                    'id' => $document->id,
                    'status' => $document->status_verifikasi->value,
                    'status_label' => $document->status_verifikasi->label(),
                    'catatan' => $document->catatan,
                ],
            ]);
        }

        return back()->with('success', 'Status berkas ' . $document->nama_dokumen . ' berhasil diperbarui.');
    }

    /**
     * Simpan keputusan akhir verifikasi permohonan APL-01
     */
    public function updateStatus(Request $request, AssessmentApplication $application): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:diterima,revisi,ditolak'],
            'rekomendasi_admin' => ['nullable', 'string'],
            'catatan_admin' => ['nullable', 'string'],
            'catatan_revisi' => ['required_if:status,revisi', 'nullable', 'string'],
            'schedule_id' => ['nullable', 'exists:assessment_schedules,id'],
        ]);

        DB::beginTransaction();
        try {
            $newStatus = ApplicationStatus::from($request->status);
            $rekomendasi = $request->rekomendasi_admin;

            if ($newStatus === ApplicationStatus::Diterima && !$rekomendasi) {
                $rekomendasi = 'Diterima sebagai peserta sertifikasi';
            } elseif ($newStatus === ApplicationStatus::Ditolak && !$rekomendasi) {
                $rekomendasi = 'Tidak diterima';
            }

            $updateData = [
                'status' => $newStatus,
                'rekomendasi_admin' => $rekomendasi,
                'catatan_admin' => $request->catatan_admin,
                'admin_id' => Auth::id(),
                'admin_verified_at' => now(),
                'ttd_admin' => Auth::user()->signature_image,
            ];

            if ($newStatus === ApplicationStatus::Revisi) {
                $updateData['catatan_revisi'] = $request->catatan_revisi;
            }

            // Jika admin langsung memilih jadwal saat menyetujui
            if ($request->filled('schedule_id')) {
                $schedule = AssessmentSchedule::findOrFail($request->schedule_id);
                if ($schedule->sisa_kuota <= 0 && $application->schedule_id !== $schedule->id) {
                    return back()->with('error', 'Jadwal yang dipilih sudah penuh (kuota habis). Silakan pilih jadwal lain.');
                }
                $updateData['schedule_id'] = $schedule->id;
            }

            $application->update($updateData);

            DB::commit();

            // Kirim notifikasi status ke email asesi via Mailpit
            try {
                Mail::to($application->user->email)->send(new ApplicationStatusUpdatedMail($application));
            } catch (\Throwable $e) {
                // Log and continue
            }

            return redirect()->route('admin.verifications.show', $application)
                ->with('success', 'Keputusan verifikasi permohonan berhasil disimpan. Status: ' . $newStatus->label());

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses verifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Plotting / Penempatan asesi yang telah diterima ke jadwal tertentu
     */
    public function plotSchedule(Request $request, AssessmentApplication $application): RedirectResponse
    {
        $request->validate([
            'schedule_id' => ['required', 'exists:assessment_schedules,id'],
        ]);

        $schedule = AssessmentSchedule::findOrFail($request->schedule_id);

        // Validasi kesesuaian skema
        if ($schedule->scheme_id !== $application->scheme_id) {
            return back()->with('error', 'Jadwal yang dipilih tidak sesuai dengan skema yang didaftarkan.');
        }

        // Cek sisa kuota
        if ($schedule->sisa_kuota <= 0 && $application->schedule_id !== $schedule->id) {
            return back()->with('error', 'Kuota peserta untuk jadwal ini sudah penuh.');
        }

        $application->update([
            'schedule_id' => $schedule->id,
        ]);

        return back()->with('success', "Asesi {$application->user->name} berhasil di-plot ke jadwal {$schedule->nama_jadwal} ({$schedule->kode_jadwal}).");
    }
}
