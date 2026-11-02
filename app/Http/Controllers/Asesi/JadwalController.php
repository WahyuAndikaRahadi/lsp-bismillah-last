<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function dashboard(): View
    {
        $userId = auth()->id();
        $activeAssessmentsCount = Assessment::where('asesi_id', $userId)
            ->where('status', 'IN_PROGRESS')
            ->count();

        $assessments = Assessment::with(['schedule.scheme', 'assessor'])
            ->where('asesi_id', $userId)
            ->latest('registered_at')
            ->get();

        $latestAssessment = $assessments->first();

        $nextStep = null;
        if (! $latestAssessment) {
            $nextStep = [
                'type' => 'no_schedule',
                'badge' => 'Pendaftaran Asesmen',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                'title' => 'Belum Terdaftar di Jadwal Asesmen',
                'description' => 'Masukkan kode jadwal asesmen 4-digit atau pilih jadwal yang tersedia untuk memulai proses sertifikasi kompetensi.',
                'action_label' => 'Gabung Jadwal Sekarang',
                'action_url' => route('asesi.jadwal.index'),
                'is_code_form' => true,
            ];
        } elseif ($latestAssessment->status === 'COMPLETED' || in_array($latestAssessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true)) {
            $isKompeten = $latestAssessment->isCompetent();
            $nextStep = [
                'type' => 'completed',
                'badge' => $isKompeten ? 'Rekomendasi KOMPETEN' : 'Rekomendasi BELUM KOMPETEN',
                'badge_color' => $isKompeten ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-rose-100 text-rose-800 border-rose-300',
                'title' => $isKompeten ? 'Selamat! Anda Dinyatakan KOMPETEN' : 'Hasil Asesmen: Belum Kompeten',
                'description' => $isKompeten
                    ? ($latestAssessment->no_sertifikat
                        ? "Sertifikat kompetensi resmi BNSP Anda telah diterbitkan dengan Nomor: {$latestAssessment->no_sertifikat}."
                        : 'Hasil asesmen Anda telah direkomendasikan KOMPETEN oleh asesor dan menunggu penetapan sidang pleno sertifikat BNSP.')
                    : 'Anda belum mencapai standar kompetensi pada seluruh unit skema ini. Anda dapat mengajukan banding asesmen bila terdapat ketidaksesuaian.',
                'action_label' => ($isKompeten && $latestAssessment->no_sertifikat) ? 'Unduh Sertifikat BNSP' : 'Lihat Rekap Hasil Asesmen',
                'action_url' => ($isKompeten && $latestAssessment->no_sertifikat) ? route('documents.sertifikat', $latestAssessment->id) : route('asesi.assessment.ruang-uji', $latestAssessment->id),
                'secondary_label' => $isKompeten ? 'Beri Umpan Balik (FR.AK.03)' : 'Ajukan Banding (FR.AK.04)',
                'secondary_url' => $isKompeten ? route('asesi.assessment.ak03', $latestAssessment->id) : route('asesi.assessment.appeal', $latestAssessment->id),
            ];
        } elseif ($latestAssessment->apl_status === 'APPROVED') {
            if (empty($latestAssessment->ak01_agreed_at)) {
                $nextStep = [
                    'type' => 'need_ak01',
                    'badge' => 'Persetujuan Asesmen',
                    'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'title' => 'Persetujuan Asesmen & Kerahasiaan (FR.AK.01)',
                    'description' => 'Berkas APL Anda telah disetujui. Langkah berikutnya adalah menandatangani persetujuan asesmen dan komitmen kerahasiaan sebelum memasuki Ruang Uji.',
                    'action_label' => 'Tandatangani Persetujuan (FR.AK.01)',
                    'action_url' => route('asesi.assessment.ak01', $latestAssessment->id),
                ];
            } else {
                $nextStep = [
                    'type' => 'ready_assessment',
                    'badge' => 'Pelaksanaan Asesmen',
                    'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'title' => 'Ruang Uji Kompetensi Aktif',
                    'description' => 'Persetujuan asesmen telah disepakati. Silakan masuk ke Ruang Uji untuk mengikuti ujian tertulis CBT (FR.IA.05) dan menyelesaikan instrumen praktik kerja sesuai instruksi asesor.',
                    'action_label' => 'Masuk Ruang Uji Kompetensi',
                    'action_url' => route('asesi.assessment.ruang-uji', $latestAssessment->id),
                ];
            }
        } elseif ($latestAssessment->apl_status === 'SUBMITTED') {
            $nextStep = [
                'type' => 'awaiting_verification',
                'badge' => 'Menunggu Verifikasi Asesor',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'title' => 'Berkas Pendaftaran Sedang Diverifikasi',
                'description' => 'Formulir FR.APL-01 dan asesmen mandiri FR.APL-02 Anda telah diserahkan dan sedang ditinjau oleh asesor penguji.',
                'action_label' => 'Lihat Rincian Formulir APL',
                'action_url' => route('asesi.assessment.apl', $latestAssessment->id),
            ];
        } elseif ($latestAssessment->apl_status === 'REVISION_REQUESTED') {
            $nextStep = [
                'type' => 'revision_requested',
                'badge' => 'Perlu Perbaikan Berkas',
                'badge_color' => 'bg-rose-100 text-rose-800 border-rose-300',
                'title' => 'Perbaikan Dokumen Persyaratan (FR.APL)',
                'description' => 'Asesor meminta perbaikan atau kelengkapan berkas: ' . ($latestAssessment->apl_revision_notes ?? $latestAssessment->admin_notes ?? 'Silakan periksa dokumen persyaratan.'),
                'action_label' => 'Perbaiki Berkas APL Sekarang',
                'action_url' => route('asesi.assessment.apl', $latestAssessment->id),
            ];
        } else {
            $nextStep = [
                'type' => 'draft_apl',
                'badge' => 'Pengisian Formulir Mandiri',
                'badge_color' => 'bg-slate-100 text-slate-800 border-slate-300',
                'title' => 'Lengkapi Formulir APL-01 & APL-02',
                'description' => 'Anda telah terdaftar pada jadwal asesmen. Segera lengkapi data permohonan sertifikasi dan asesmen mandiri agar dapat diverifikasi asesor.',
                'action_label' => 'Isi Formulir APL-01 & APL-02',
                'action_url' => route('asesi.assessment.apl', $latestAssessment->id),
            ];
        }

        return view('asesi.dashboard', compact('activeAssessmentsCount', 'assessments', 'nextStep'));
    }

    public function index(): View
    {
        $assessments = Assessment::with(['schedule.scheme', 'assessor'])
            ->where('asesi_id', auth()->id())
            ->latest('registered_at')
            ->get();

        return view('asesi.jadwal.index', compact('assessments'));
    }

    public function join(Request $request): RedirectResponse
    {
        if (! auth()->user()->isProfileComplete()) {
            return redirect()->route('asesi.profile.edit')
                ->with('error', 'Silakan lengkapi biodata dan tanda tangan profil Anda terlebih dahulu sebelum bergabung dengan jadwal asesmen.');
        }

        $request->validate([
            'code' => 'required_without:schedule_code|nullable|string',
            'schedule_code' => 'nullable|string',
        ]);

        $code = $request->input('code') ?: $request->input('schedule_code');

        $schedule = Schedule::where('code', $code)->first();

        if (! $schedule || $schedule->status !== 'ACTIVE' || $schedule->is_closed) {
            return back()->with('error', 'Kode jadwal tidak ditemukan atau jadwal sudah tidak aktif/ditutup.');
        }

        $userId = auth()->id();

        $assessment = DB::transaction(function () use ($schedule, $userId) {
            return Assessment::firstOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'asesi_id' => $userId,
                ],
                [
                    'apl_status' => 'DRAFT',
                    'admin_status' => 'APPROVED',
                    'registered_at' => now(),
                    'last_active_step' => 'apl',
                ]
            );
        });

        if (! $assessment->wasRecentlyCreated) {
            if ($assessment->last_active_step === 'ruang-uji' && $assessment->apl_status === 'APPROVED') {
                return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                    ->with('info', 'Anda sudah terdaftar pada jadwal ini dan telah disetujui untuk Ruang Uji.');
            }

            return redirect()->route('asesi.assessment.apl', $assessment)
                ->with('info', 'Anda sudah terdaftar pada jadwal ini.');
        }

        return redirect()->route('asesi.assessment.apl', $assessment)
            ->with('success', 'Berhasil bergabung dengan jadwal asesmen.');
    }
}
