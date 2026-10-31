<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsesiSubmoduleController extends Controller
{
    public function unscheduled(): View
    {
        $applications = AssessmentApplication::with(['asesi.user', 'scheme'])
            ->whereNull('schedule_id')
            ->where('status', '!=', 'ditolak')
            ->latest()
            ->paginate(15);

        return view('admin.asesi.submodule', [
            'title' => 'Asesi Belum Terjadwal',
            'subtitle' => 'Daftar asesi terverifikasi yang siap di-plotting ke jadwal uji kompetensi.',
            'applications' => $applications,
            'category' => 'unscheduled',
        ]);
    }

    public function scheduled(): View
    {
        $applications = AssessmentApplication::with(['asesi.user', 'scheme', 'schedule'])
            ->whereNotNull('schedule_id')
            ->latest()
            ->paginate(15);

        return view('admin.asesi.submodule', [
            'title' => 'Asesi Terjadwal',
            'subtitle' => 'Daftar asesi yang telah di-plot ke dalam jadwal uji kompetensi.',
            'applications' => $applications,
            'category' => 'scheduled',
        ]);
    }

    public function competent(): View
    {
        $applications = AssessmentApplication::with(['asesi.user', 'scheme'])
            ->where('status', 'diterima')
            ->whereNotNull('no_sertifikat')
            ->latest()
            ->paginate(15);

        return view('admin.asesi.submodule', [
            'title' => 'Asesi Kompeten & Bersertifikat',
            'subtitle' => 'Daftar asesi yang dinyatakan Kompeten (K) dan telah diterbitkan nomor sertifikat BNSP.',
            'applications' => $applications,
            'category' => 'competent',
        ]);
    }

    public function incompetent(): View
    {
        $applications = AssessmentApplication::with(['asesi.user', 'scheme'])
            ->where('status', 'ditolak')
            ->latest()
            ->paginate(15);

        return view('admin.asesi.submodule', [
            'title' => 'Asesi Belum Kompeten / Ditolak',
            'subtitle' => 'Daftar asesi yang direkomendasikan Belum Kompeten (BK) atau berkas ditolak.',
            'applications' => $applications,
            'category' => 'incompetent',
        ]);
    }
}
