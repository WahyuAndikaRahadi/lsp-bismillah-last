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
        $user = auth()->user();

        $activeAssessmentsCount = Assessment::where('asesi_id', $userId)
            ->where('status', 'IN_PROGRESS')
            ->count();

        $assessments = Assessment::with(['schedule.scheme', 'assessor'])
            ->where('asesi_id', $userId)
            ->latest('registered_at')
            ->get();

        $latestAssessment = $assessments->first();

        $nextStep = app(\App\Services\AssessmentNextAction::class)->forAsesi($user, $latestAssessment);

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
        $user = auth()->user();
        if (! $user->isProfileComplete()) {
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

        try {
            $result = DB::transaction(function () use ($schedule, $user) {
                $lockedSchedule = Schedule::where('id', $schedule->id)->lockForUpdate()->first();

                // Idempotent check
                $existing = Assessment::where('schedule_id', $lockedSchedule->id)
                    ->where('asesi_id', $user->id)
                    ->first();

                if ($existing) {
                    return ['assessment' => $existing, 'created' => false];
                }

                // Capacity check
                $currentCount = Assessment::where('schedule_id', $lockedSchedule->id)->count();
                $capacity = $lockedSchedule->capacity ?? 20;
                if ($currentCount >= $capacity) {
                    throw new \DomainException('Kapasitas peserta untuk jadwal ini sudah penuh.');
                }

                $profile = $user->profile;
                $snapshot = [
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'identity_type' => $profile?->identity_type ?? 'KTP',
                    'nik' => $profile?->nik,
                    'tempat_lahir' => $profile?->tempat_lahir,
                    'tanggal_lahir' => $profile?->tanggal_lahir instanceof \DateTimeInterface
                        ? $profile->tanggal_lahir->format('Y-m-d')
                        : (string) $profile?->tanggal_lahir,
                    'jenis_kelamin' => $profile?->jenis_kelamin,
                    'kebangsaan' => $profile?->kebangsaan,
                    'alamat_rumah' => $profile?->alamat_rumah,
                    'kode_pos' => $profile?->kode_pos,
                    'no_telepon_rumah' => $profile?->no_telepon_rumah,
                    'status_pekerjaan' => $profile?->status_pekerjaan,
                    'pendidikan_terakhir' => $profile?->pendidikan_terakhir,
                    'jabatan' => $profile?->jabatan,
                    'nama_institusi_perusahaan' => $profile?->nama_institusi_perusahaan,
                    'alamat_kantor' => $profile?->alamat_kantor,
                    'kode_pos_kantor' => $profile?->kode_pos_kantor,
                    'no_telepon_kantor' => $profile?->no_telepon_kantor,
                    'no_fax_kantor' => $profile?->no_fax_kantor,
                    'email_kantor' => $profile?->email_kantor,
                    'signature_data' => $profile?->signature_data,
                    'signature_version' => $profile?->signature_version ?? 1,
                    'snapshotted_at' => now()->toIso8601String(),
                ];

                $schemeVersionId = $lockedSchedule->scheme_version_id;
                if (! $schemeVersionId && $lockedSchedule->scheme) {
                    $schemeVersion = \App\Models\SchemeVersion::where(function ($q) use ($lockedSchedule) {
                        $q->where('scheme_id', $lockedSchedule->scheme->id)
                          ->orWhere('code', $lockedSchedule->scheme->code);
                    })
                        ->where('status', 'approved')
                        ->latest('version')
                        ->first();
                    $schemeVersionId = $schemeVersion?->id;
                }

                $newAssessment = Assessment::create([
                    'schedule_id' => $lockedSchedule->id,
                    'asesi_id' => $user->id,
                    'scheme_version_id' => $schemeVersionId,
                    'snapshot_profile' => $snapshot,
                    'apl_status' => 'DRAFT',
                    'admin_status' => 'APPROVED',
                    'registered_at' => now(),
                    'last_active_step' => 'apl',
                ]);

                // Record explicit join consent
                $consentText = "Persetujuan dan komitmen integritas asesmen saat bergabung jadwal {$lockedSchedule->code}";
                app(\App\Services\ScheduleConsentService::class)->recordConsent(
                    $lockedSchedule,
                    $user,
                    'join',
                    $consentText,
                    $profile?->signature_data,
                    request()
                );

                return ['assessment' => $newAssessment, 'created' => true];
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        $assessment = $result['assessment'];
        $wasCreated = $result['created'];

        if (! $wasCreated) {
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
