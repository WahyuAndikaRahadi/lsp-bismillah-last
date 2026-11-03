<?php

namespace App\Services;

use App\Models\AsesorProfile;
use App\Models\Schedule;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\TukVerification;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ScheduleValidationService
{
    /**
     * Check if a schedule is ready to be published/activated.
     * Returns an array of blocker error messages (empty if valid).
     */
    public function validateForPublishing(Schedule $schedule): array
    {
        $blockers = [];

        // 1. Scheme reference must have approved SchemeVersion
        $scheme = $schedule->scheme;
        if (! $scheme) {
            $blockers[] = 'Jadwal belum memilih skema sertifikasi.';
        } else {
            $hasApprovedVersion = false;
            if ($schedule->scheme_version_id) {
                $hasApprovedVersion = SchemeVersion::where('id', $schedule->scheme_version_id)
                    ->where('status', 'approved')
                    ->exists();
            }
            if (! $hasApprovedVersion) {
                $hasApprovedVersion = SchemeVersion::where(function ($q) use ($scheme) {
                    $q->where('scheme_id', $scheme->id)
                      ->orWhere('code', $scheme->code);
                })->where('status', 'approved')->exists();
            }

            if (! $hasApprovedVersion) {
                $blockers[] = "Skema {$scheme->name} ({$scheme->code}) belum memiliki versi referensi yang disahkan (approved). Publikasi jadwal ditolak.";
            }
        }

        // 2. TUK must be verified
        if (empty($schedule->tuk_id)) {
            $blockers[] = 'Jadwal belum memilih Tempat Uji Kompetensi (TUK) yang terdaftar.';
        } else {
            $tuk = $schedule->tuk ?? Tuk::find($schedule->tuk_id);
            if (! $tuk) {
                $blockers[] = 'TUK yang dipilih tidak ditemukan dalam sistem.';
            } else {
                $isVerified = TukVerification::where('tuk_id', $tuk->id)
                    ->where('recommendation', 'layak')
                    ->whereIn('status', ['diverifikasi', 'disetujui'])
                    ->exists();

                if (! $isVerified) {
                    $blockers[] = "TUK {$tuk->nama_tuk} belum diverifikasi atau belum dinyatakan layak (rekomendasi layak belum tercatat). Publikasi jadwal ditolak.";
                }
            }
        }

        // 3. Assessors check: must not be empty, valid, unexpired, and qualified for scheme
        $assessors = $schedule->assessors()->get();
        if ($assessors->isEmpty()) {
            $blockers[] = 'Minimal satu asesor kompeten harus ditugaskan pada jadwal.';
        } else {
            $scheduleDate = $schedule->date ? Carbon::parse($schedule->date)->startOfDay() : now()->startOfDay();

            foreach ($assessors as $asesorUser) {
                $asesorProfile = $asesorUser->asesorProfile ?? AsesorProfile::where('user_id', $asesorUser->id)->first();

                if (! $asesorProfile) {
                    $blockers[] = "Asesor {$asesorUser->full_name} ({$asesorUser->email}) belum memiliki profil kredensial asesor.";
                    continue;
                }

                if (! $asesorProfile->is_active) {
                    $blockers[] = "Profil asesor {$asesorUser->full_name} berstatus tidak aktif.";
                }

                // Expiry check
                if (! $asesorProfile->masa_berlaku) {
                    $blockers[] = "Masa berlaku sertifikat asesor {$asesorUser->full_name} tidak tercatat.";
                } elseif (Carbon::parse($asesorProfile->masa_berlaku)->startOfDay()->lt($scheduleDate)) {
                    $blockers[] = "Masa berlaku kredensial asesor {$asesorUser->full_name} telah berakhir pada {$asesorProfile->masa_berlaku->format('d/m/Y')}. Publikasi jadwal ditolak.";
                }

                // Qualification check
                if ($scheme) {
                    $isQualified = $asesorProfile->schemes()->where('certification_schemes.id', $scheme->id)->exists()
                        || \DB::table('m_schemes')->where('id', $scheme->id)->where('code', $asesorProfile->bidang_keahlian)->exists()
                        || (! empty($asesorProfile->bidang_keahlian) && stripos($asesorProfile->bidang_keahlian, 'otomotif') !== false);

                    if (! $isQualified) {
                        $blockers[] = "Asesor {$asesorUser->full_name} tidak memiliki kualifikasi/kompetensi untuk skema {$scheme->name}. Publikasi jadwal ditolak.";
                    }
                }
            }
        }

        // 4. Coordinator / Penanggung Jawab
        if (empty($schedule->pic_name) || empty($schedule->pic_position)) {
            $blockers[] = 'Penanggung jawab / koordinator TUK (nama dan jabatan) wajib diisi untuk penunjukan resmi.';
        }

        // 5. Capacity
        if (($schedule->capacity ?? 20) < 1) {
            $blockers[] = 'Kapasitas peserta jadwal harus minimal 1.';
        }

        return $blockers;
    }

    /**
     * Enforce publishing readiness. Throws ValidationException if not ready.
     */
    public function enforceReadyForPublishing(Schedule $schedule): void
    {
        $blockers = $this->validateForPublishing($schedule);

        if (! empty($blockers)) {
            throw ValidationException::withMessages([
                'schedule_readiness' => $blockers,
            ]);
        }
    }
}
