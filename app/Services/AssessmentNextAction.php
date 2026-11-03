<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Assessment;
use App\Models\User;

class AssessmentNextAction
{
    /**
     * Determine next action for any role.
     * Pure and read-only: does not mutate database or state.
     */
    public function determine(User $user, ?Assessment $assessment = null): array
    {
        $role = $user->canonicalRole();

        return match ($role) {
            UserRole::Asesi => $this->forAsesi($user, $assessment),
            UserRole::Asesor => $this->forAsesor($user, $assessment),
            UserRole::Admin => $this->forAdmin($user, $assessment),
            default => [
                'type' => 'unauthorized',
                'badge' => 'Tidak Terotorisasi',
                'badge_color' => 'bg-slate-100 text-slate-800 border-slate-200',
                'title' => 'Akses Terbatas',
                'description' => 'Peran pengguna tidak dikenali.',
                'action_label' => 'Kembali ke Beranda',
                'action_url' => route('login'),
                'is_blocked' => true,
                'blocker_reason' => 'Peran tidak dikenali',
                'secondary_label' => null,
                'secondary_url' => null,
            ],
        };
    }

    /**
     * Determine next action for Asesi.
     */
    public function forAsesi(User $user, ?Assessment $assessment = null): array
    {
        // 1. Profile incomplete check
        if (! $user->isProfileComplete()) {
            return [
                'type' => 'profile_incomplete',
                'badge' => 'Biodata Belum Lengkap',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'title' => 'Lengkapi Data Diri & Tanda Tangan',
                'description' => 'Silakan lengkapi biodata APL.01 dan tanda tangan digital Anda sebelum melanjutkan pendaftaran asesmen.',
                'action_label' => 'Lengkapi Biodata Sekarang',
                'action_url' => route('asesi.profile.edit'),
                'is_blocked' => true,
                'blocker_reason' => 'Biodata dan tanda tangan profil wajib dilengkapi.',
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        // 2. No assessment registered
        if (! $assessment) {
            return [
                'type' => 'no_schedule',
                'badge' => 'Pendaftaran Asesmen',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                'title' => 'Belum Terdaftar di Jadwal Asesmen',
                'description' => 'Masukkan kode jadwal asesmen atau pilih jadwal yang tersedia untuk memulai proses sertifikasi kompetensi.',
                'action_label' => 'Gabung Jadwal Sekarang',
                'action_url' => route('asesi.jadwal.index'),
                'is_blocked' => false,
                'blocker_reason' => null,
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        // 3. Completed or finalized result
        $isFinal = $assessment->status === 'COMPLETED'
            || in_array($assessment->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true)
            || ! empty($assessment->completed_at);

        if ($isFinal) {
            $isKompeten = method_exists($assessment, 'isCompetent')
                ? $assessment->isCompetent()
                : in_array($assessment->final_result, ['KOMPETEN', 'K'], true);

            $hasCertificate = ! empty($assessment->no_sertifikat) || $assessment->certificates()->exists();

            return [
                'type' => 'completed',
                'badge' => $isKompeten ? 'Rekomendasi KOMPETEN' : 'Rekomendasi BELUM KOMPETEN',
                'badge_color' => $isKompeten ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-rose-100 text-rose-800 border-rose-300',
                'title' => $isKompeten ? 'Hasil Asesmen: Rekomendasi KOMPETEN' : 'Hasil Asesmen: Belum Kompeten',
                'description' => $isKompeten
                    ? ($hasCertificate
                        ? "Sertifikat kompetensi resmi BNSP Anda telah diterbitkan dengan Nomor: " . ($assessment->no_sertifikat ?? 'Tersedia') . "."
                        : 'Hasil asesmen Anda telah direkomendasikan KOMPETEN oleh asesor dan menunggu penetapan sidang pleno sertifikat BNSP.')
                    : 'Anda belum mencapai standar kompetensi pada seluruh unit skema ini. Anda dapat mengajukan banding asesmen bila terdapat ketidaksesuaian.',
                'action_label' => ($isKompeten && $hasCertificate) ? 'Unduh Sertifikat BNSP' : 'Lihat Rekap Hasil Asesmen',
                'action_url' => ($isKompeten && $hasCertificate && \Route::has('documents.sertifikat'))
                    ? route('documents.sertifikat', $assessment->id)
                    : route('asesi.assessment.ruang-uji', $assessment->id),
                'is_blocked' => false,
                'blocker_reason' => null,
                'secondary_label' => $isKompeten ? 'Beri Umpan Balik (FR.AK.03)' : 'Ajukan Banding (FR.AK.04)',
                'secondary_url' => $isKompeten
                    ? (\Route::has('asesi.assessment.ak03') ? route('asesi.assessment.ak03', $assessment->id) : null)
                    : (\Route::has('asesi.assessment.appeal') ? route('asesi.assessment.appeal', $assessment->id) : null),
            ];
        }

        // 4. APL Status: Revision Requested
        if ($assessment->apl_status === 'REVISION_REQUESTED') {
            return [
                'type' => 'revision_requested',
                'badge' => 'Perlu Perbaikan Berkas',
                'badge_color' => 'bg-rose-100 text-rose-800 border-rose-300',
                'title' => 'Perbaikan Dokumen Persyaratan (FR.APL)',
                'description' => 'Asesor meminta perbaikan atau kelengkapan berkas: ' . ($assessment->apl_revision_notes ?? $assessment->admin_notes ?? 'Silakan periksa dokumen persyaratan.'),
                'action_label' => 'Perbaiki Permohonan',
                'action_url' => route('asesi.assessment.apl', $assessment->id),
                'is_blocked' => false,
                'blocker_reason' => null,
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        // 5. APL Status: Submitted / Awaiting Verification
        if ($assessment->apl_status === 'SUBMITTED') {
            return [
                'type' => 'awaiting_verification',
                'badge' => 'Menunggu Verifikasi Asesor',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'title' => 'Berkas Pendaftaran Sedang Diverifikasi',
                'description' => 'Formulir FR.APL-01 dan asesmen mandiri FR.APL-02 Anda telah diserahkan dan sedang ditinjau oleh asesor penguji.',
                'action_label' => 'Lihat Rincian Formulir APL',
                'action_url' => route('asesi.assessment.apl', $assessment->id),
                'is_blocked' => false,
                'blocker_reason' => null,
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        // 6. APL Status: Approved
        if ($assessment->apl_status === 'APPROVED') {
            // Need AK-01 agreement
            if (empty($assessment->ak01_agreed_at)) {
                return [
                    'type' => 'need_ak01',
                    'badge' => 'Persetujuan Asesmen',
                    'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'title' => 'Persetujuan Asesmen & Kerahasiaan (FR.AK.01)',
                    'description' => 'Berkas APL Anda telah disetujui. Langkah berikutnya adalah menandatangani persetujuan asesmen dan komitmen kerahasiaan sebelum memasuki Ruang Uji.',
                    'action_label' => 'Setujui Rencana Asesmen',
                    'action_url' => route('asesi.assessment.ak01', $assessment->id),
                    'is_blocked' => false,
                    'blocker_reason' => null,
                    'secondary_label' => null,
                    'secondary_url' => null,
                ];
            }

            // Ready for examination / Ruang Uji
            return [
                'type' => 'ready_assessment',
                'badge' => 'Pelaksanaan Asesmen',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'title' => 'Ruang Uji Kompetensi Aktif',
                'description' => 'Persetujuan asesmen telah disepakati. Silakan masuk ke Ruang Uji untuk mengikuti ujian kompetensi sesuai petunjuk asesor.',
                'action_label' => 'Masuk Ruang Uji',
                'action_url' => route('asesi.assessment.ruang-uji', $assessment->id),
                'is_blocked' => false,
                'blocker_reason' => null,
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        // 7. Default: Draft APL
        return [
            'type' => 'draft_apl',
            'badge' => 'Pengisian Formulir Mandiri',
            'badge_color' => 'bg-slate-100 text-slate-800 border-slate-300',
            'title' => 'Lengkapi Formulir APL-01 & APL-02',
            'description' => 'Anda telah terdaftar pada jadwal asesmen. Segera lengkapi data permohonan sertifikasi dan asesmen mandiri.',
            'action_label' => 'Lanjut ke APL-02',
            'action_url' => route('asesi.assessment.apl', $assessment->id),
            'is_blocked' => false,
            'blocker_reason' => null,
            'secondary_label' => null,
            'secondary_url' => null,
        ];
    }

    /**
     * Determine next action for Asesor.
     */
    public function forAsesor(User $user, ?Assessment $assessment = null): array
    {
        if (! $assessment) {
            return [
                'type' => 'no_selection',
                'badge' => 'Daftar Asesi',
                'badge_color' => 'bg-slate-100 text-slate-800 border-slate-200',
                'title' => 'Pilih Peserta Asesmen',
                'description' => 'Pilih asesi yang ditugaskan kepada Anda untuk memeriksa berkas atau memulai penilaian.',
                'action_label' => 'Lihat Jadwal Asesmen',
                'action_url' => route('asesor.jadwal.index'),
                'is_blocked' => false,
                'blocker_reason' => null,
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        if ($assessment->apl_status === 'SUBMITTED') {
            return [
                'type' => 'review_apl',
                'badge' => 'Perlu Verifikasi APL',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'title' => 'Verifikasi Permohonan & Asesmen Mandiri',
                'description' => 'Asesi telah mengirimkan berkas APL-01 dan APL-02. Periksa bukti persyaratan dan berikan rekomendasi.',
                'action_label' => 'Review Permohonan APL',
                'action_url' => \Route::has('asesor.apl.show') ? route('asesor.apl.show', $assessment->id) : route('asesor.jadwal.index'),
                'is_blocked' => false,
                'blocker_reason' => null,
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        if ($assessment->apl_status === 'APPROVED' && empty($assessment->ak01_agreed_at)) {
            return [
                'type' => 'waiting_ak01',
                'badge' => 'Menunggu AK-01 Asesi',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                'title' => 'Menunggu Persetujuan Asesmen (AK-01)',
                'description' => 'Asesi belum menandatangani persetujuan rencana asesmen dan komitmen kerahasiaan.',
                'action_label' => 'Lihat Rencana Asesmen',
                'action_url' => route('asesor.jadwal.index'),
                'is_blocked' => true,
                'blocker_reason' => 'Menunggu persetujuan AK-01 dari asesi.',
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        if ($assessment->status === 'IN_PROGRESS' || ! empty($assessment->ak01_agreed_at)) {
            return [
                'type' => 'conduct_assessment',
                'badge' => 'Pelaksanaan Asesmen',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'title' => 'Pelaksanaan & Observasi Asesmen',
                'description' => 'Laksanakan pengujian menggunakan instrumen yang telah disiapkan dan catat hasil observasi.',
                'action_label' => 'Simpan Observasi',
                'action_url' => route('asesor.jadwal.index'),
                'is_blocked' => false,
                'blocker_reason' => null,
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        return [
            'type' => 'completed',
            'badge' => 'Selesai',
            'badge_color' => 'bg-slate-100 text-slate-800 border-slate-200',
            'title' => 'Penilaian Selesai',
            'description' => 'Rekomendasi hasil asesmen telah disimpan.',
            'action_label' => 'Lihat Rekap Penilaian',
            'action_url' => route('asesor.jadwal.index'),
            'is_blocked' => false,
            'blocker_reason' => null,
            'secondary_label' => null,
            'secondary_url' => null,
        ];
    }

    /**
     * Determine next action for Admin.
     */
    public function forAdmin(User $user, ?Assessment $assessment = null): array
    {
        return [
            'type' => 'admin_overview',
            'badge' => 'Administrasi LSP',
            'badge_color' => 'bg-slate-100 text-slate-800 border-slate-200',
            'title' => 'Pemeriksaan Administratif',
            'description' => 'Kelola jadwal, verifikasi TUK, dan penetapan hasil sertifikasi.',
            'action_label' => 'Kelola Jadwal',
            'action_url' => route('admin.jadwal.index'),
            'is_blocked' => false,
            'blocker_reason' => null,
            'secondary_label' => null,
            'secondary_url' => null,
        ];
    }
}
