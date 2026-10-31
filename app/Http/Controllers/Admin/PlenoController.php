<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ScheduleStatus;
use App\Http\Controllers\Controller;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\PlenoMeeting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PlenoController extends Controller
{
    /**
     * Daftar jadwal asesmen untuk penetapan rapat pleno
     */
    public function index(Request $request): View
    {
        $schedules = AssessmentSchedule::with([
            'scheme',
            'tuk',
            'applications.ak02',
            'plenoMeeting',
            'scheduleAsesors.asesor',
        ])
        ->whereIn('status', [ScheduleStatus::Asesmen, ScheduleStatus::Pleno, ScheduleStatus::Selesai])
        ->latest('tgl_asesmen')
        ->paginate(10);

        return view('admin.pleno.index', compact('schedules'));
    }

    /**
     * Lembar kerja Rapat Pleno penetapan kelulusan
     */
    public function show(AssessmentSchedule $schedule): View
    {
        $schedule->load([
            'scheme.competencyUnits',
            'tuk',
            'applications.user.asesiProfile',
            'applications.ak02',
            'scheduleAsesors.asesor',
            'plenoMeeting',
        ]);

        $pleno = $schedule->plenoMeeting ?? new PlenoMeeting([
            'schedule_id' => $schedule->id,
            'nomor_ba_pleno' => PlenoMeeting::generateNomorBaPleno(),
            'tanggal_pleno' => now()->toDateString(),
            'tempat' => 'Ruang Rapat Pleno LSP / Daring',
            'ketua_komite' => Auth::user()->name,
            'anggota_komite' => '1. Ketua Komite Teknis\n2. Asesor Penguji\n3. Bagian Sertifikasi LSP',
        ]);

        return view('admin.pleno.show', compact('schedule', 'pleno'));
    }

    /**
     * Simpan penetapan Rapat Pleno & terbitkan nomor sertifikat BNSP
     */
    public function store(Request $request, AssessmentSchedule $schedule): RedirectResponse
    {
        $request->validate([
            'nomor_ba_pleno' => ['required', 'string', 'max:100'],
            'tanggal_pleno' => ['required', 'date'],
            'tempat' => ['required', 'string', 'max:255'],
            'ketua_komite' => ['required', 'string', 'max:100'],
            'anggota_komite' => ['nullable', 'string'],
            'catatan_pleno' => ['nullable', 'string'],
            'kelulusan' => ['required', 'array'],
            'kelulusan.*' => ['required', 'in:kompeten,belum_kompeten'],
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan Record Pleno Meeting
            $pleno = PlenoMeeting::updateOrCreate(
                ['schedule_id' => $schedule->id],
                [
                    'nomor_ba_pleno' => $request->nomor_ba_pleno,
                    'tanggal_pleno' => $request->tanggal_pleno,
                    'tempat' => $request->tempat,
                    'ketua_komite' => $request->ketua_komite,
                    'anggota_komite' => $request->anggota_komite,
                    'catatan_pleno' => $request->catatan_pleno,
                    'status' => 'disahkan',
                    'ttd_ketua' => Auth::user()->signature_image,
                ]
            );

            // 2. Update status kelulusan peserta & terbitkan nomor sertifikat
            $scheme = $schedule->scheme;
            $tglTerbit = now()->toDateString();
            $masaBerlaku = now()->addYears(3)->toDateString(); // Lisensi BNSP berlaku 3 tahun

            foreach ($request->kelulusan as $appId => $statusLulus) {
                $app = AssessmentApplication::where('id', $appId)
                    ->where('schedule_id', $schedule->id)
                    ->first();

                if ($app) {
                    $noSertifikat = $app->no_sertifikat;
                    if ($statusLulus === 'kompeten' && !$noSertifikat) {
                        $noSertifikat = PlenoMeeting::generateNomorSertifikat($scheme);
                    }

                    $app->update([
                        'status_kelulusan' => $statusLulus,
                        'no_sertifikat' => $statusLulus === 'kompeten' ? $noSertifikat : null,
                        'tgl_terbit_sertifikat' => $statusLulus === 'kompeten' ? $tglTerbit : null,
                        'masa_berlaku_sertifikat' => $statusLulus === 'kompeten' ? $masaBerlaku : null,
                    ]);
                }
            }

            // 3. Update status jadwal ke 'selesai'
            $schedule->update([
                'status' => ScheduleStatus::Selesai,
            ]);

            DB::commit();

            return redirect()->route('admin.pleno.show', $schedule)
                ->with('success', "Keputusan Rapat Pleno untuk jadwal {$schedule->nama_jadwal} berhasil disahkan! Sertifikat kompetensi resmi telah diterbitkan.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses penetapan pleno: ' . $e->getMessage());
        }
    }
}
