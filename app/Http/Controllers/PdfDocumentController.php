<?php

namespace App\Http\Controllers;

use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\LspProfile;
use App\Models\ScheduleAsesor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfDocumentController extends Controller
{
    /**
     * Helper untuk mengambil profil LSP atau instansi default
     */
    private function getLspProfile(): LspProfile
    {
        return LspProfile::first() ?? new LspProfile([
            'nama_lsp' => 'Lembaga Sertifikasi Profesi',
            'no_lisensi_bnsp' => 'BNSP-LSP-000-ID',
            'alamat' => 'Jakarta, Indonesia',
            'telepon' => '021-12345678',
            'email' => 'info@lsp.id',
            'nama_ketua' => 'Direktur LSP',
        ]);
    }

    /**
     * 1. Surat Tugas Asesor Penguji
     */
    public function suratTugas(AssessmentSchedule $schedule, ScheduleAsesor $scheduleAsesor): Response
    {
        $lsp = $this->getLspProfile();
        $schedule->load(['scheme', 'tuk']);
        $scheduleAsesor->load(['asesor.asesorProfile']);

        // Peserta yang diplot untuk asesor ini
        $applications = AssessmentApplication::with(['user.asesiProfile'])
            ->where('schedule_id', $schedule->id)
            ->where('asesor_id', $scheduleAsesor->asesor_id)
            ->get();

        $pdf = Pdf::loadView('pdf.surat-tugas', compact('lsp', 'schedule', 'scheduleAsesor', 'applications'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("surattugas_{$schedule->kode_jadwal}_{$scheduleAsesor->id}.pdf");
    }

    /**
     * 2. Berita Acara Asesmen Kompetensi
     */
    public function beritaAcaraAsesmen(AssessmentSchedule $schedule): Response
    {
        $lsp = $this->getLspProfile();
        $schedule->load([
            'scheme',
            'tuk',
            'applications.user.asesiProfile',
            'applications.ak02',
            'scheduleAsesors.asesor.asesorProfile',
        ]);

        $pdf = Pdf::loadView('pdf.berita-acara-asesmen', compact('lsp', 'schedule'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("beritaacara_asesmen_{$schedule->kode_jadwal}.pdf");
    }

    /**
     * 3. Presensi / Daftar Hadir Asesmen Kompetensi
     */
    public function daftarHadirAsesmen(AssessmentSchedule $schedule): Response
    {
        $lsp = $this->getLspProfile();
        $schedule->load([
            'scheme',
            'tuk',
            'applications.user.asesiProfile',
            'scheduleAsesors.asesor.asesorProfile',
        ]);

        $pdf = Pdf::loadView('pdf.daftar-hadir-asesmen', compact('lsp', 'schedule'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("daftarhadir_asesmen_{$schedule->kode_jadwal}.pdf");
    }

    /**
     * 4. Berita Acara Rapat Pleno Komite Teknis
     */
    public function beritaAcaraPleno(AssessmentSchedule $schedule): Response
    {
        $lsp = $this->getLspProfile();
        $schedule->load([
            'scheme',
            'tuk',
            'applications.user.asesiProfile',
            'applications.ak02',
            'plenoMeeting',
        ]);

        $pleno = $schedule->plenoMeeting;

        $pdf = Pdf::loadView('pdf.berita-acara-pleno', compact('lsp', 'schedule', 'pleno'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("beritaacara_pleno_{$schedule->kode_jadwal}.pdf");
    }

    /**
     * 5. Presensi / Daftar Hadir Rapat Pleno
     */
    public function daftarHadirPleno(AssessmentSchedule $schedule): Response
    {
        $lsp = $this->getLspProfile();
        $schedule->load([
            'scheme',
            'scheduleAsesors.asesor.asesorProfile',
            'plenoMeeting',
        ]);

        $pleno = $schedule->plenoMeeting;

        $pdf = Pdf::loadView('pdf.daftar-hadir-pleno', compact('lsp', 'schedule', 'pleno'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("daftarhadir_pleno_{$schedule->kode_jadwal}.pdf");
    }

    /**
     * 6. FR-APL-01 Permohonan Sertifikasi Kompetensi
     */
    public function frApl01(AssessmentApplication $application): Response
    {
        $lsp = $this->getLspProfile();
        $application->load([
            'user.asesiProfile',
            'scheme.units',
            'documents.requirement',
            'verifier',
        ]);

        $profile = $application->user->asesiProfile;

        $pdf = Pdf::loadView('pdf.fr-apl-01', compact('lsp', 'application', 'profile'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("FR-APL-01_{$application->nomor_permohonan}.pdf");
    }

    /**
     * 7. FR-APL-02 Formulir Asesmen Mandiri
     */
    public function frApl02(AssessmentApplication $application): Response
    {
        $lsp = $this->getLspProfile();
        $application->load([
            'user',
            'scheme.units.elements.criterias',
            'schedule.tuk',
            'asesor.asesorProfile',
            'apl02.items',
            'apl02.asesor.asesorProfile',
        ]);

        $pdf = Pdf::loadView('pdf.fr-apl-02', compact('lsp', 'application'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("FR-APL-02_{$application->nomor_permohonan}.pdf");
    }

    /**
     * 8. FR-AK-01 Persetujuan Asesmen dan Kerahasiaan
     */
    public function frAk01(AssessmentApplication $application): Response
    {
        $lsp = $this->getLspProfile();
        $application->load([
            'user.asesiProfile',
            'scheme',
            'schedule.tuk',
            'asesor.asesorProfile',
        ]);

        $pdf = Pdf::loadView('pdf.fr-ak-01', compact('lsp', 'application'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("FR-AK-01_{$application->nomor_permohonan}.pdf");
    }

    /**
     * 9. FR-AK-02 Formulir Rekaman Asesmen Kompetensi
     */
    public function frAk02(AssessmentApplication $application): Response
    {
        $lsp = $this->getLspProfile();
        $application->load([
            'user.asesiProfile',
            'scheme.units',
            'schedule.tuk',
            'asesor.asesorProfile',
            'ak02.unitAssessments',
        ]);

        $pdf = Pdf::loadView('pdf.fr-ak-02', compact('lsp', 'application'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("FR-AK-02_{$application->nomor_permohonan}.pdf");
    }

    /**
     * 10. FR-AK-03 Formulir Umpan Balik Asesmen
     */
    public function frAk03(AssessmentApplication $application): Response
    {
        $lsp = $this->getLspProfile();
        $application->load([
            'user',
            'scheme',
            'schedule.tuk',
            'asesor',
            'ak03',
        ]);

        $pdf = Pdf::loadView('pdf.fr-ak-03', compact('lsp', 'application'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("FR-AK-03_{$application->nomor_permohonan}.pdf");
    }

    /**
     * 11. FR-AK-05 Laporan Asesmen
     */
    public function frAk05(AssessmentSchedule $schedule): Response
    {
        $lsp = $this->getLspProfile();
        $schedule->load([
            'scheme',
            'tuk',
            'applications.user.asesiProfile',
            'applications.asesor',
            'applications.ak02',
            'scheduleAsesors.asesor.asesorProfile',
            'ak05Report',
        ]);

        $pdf = Pdf::loadView('pdf.fr-ak-05', compact('lsp', 'schedule'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("FR-AK-05_{$schedule->kode_jadwal}.pdf");
    }

    /**
     * 12. Sertifikat Kompetensi Digital BNSP
     */
    public function sertifikat(AssessmentApplication $application): Response
    {
        if ($application->status_kelulusan !== 'kompeten') {
            abort(403, 'Sertifikat hanya dapat diterbitkan bagi asesi yang telah dinyatakan Kompeten (K) dalam Rapat Pleno.');
        }

        $lsp = $this->getLspProfile();
        $application->load([
            'user.asesiProfile',
            'scheme.units',
        ]);

        $pdf = Pdf::loadView('pdf.sertifikat', compact('lsp', 'application'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("Sertifikat_{$application->no_sertifikat}.pdf");
    }
}
