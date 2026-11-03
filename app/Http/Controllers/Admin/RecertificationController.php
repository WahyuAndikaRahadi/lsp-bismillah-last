<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecertificationController extends Controller
{
    /**
     * Tampilkan daftar permohonan sertifikasi ulang (resertifikasi) yang tertaut ke sertifikat lama.
     */
    public function index(Request $request): View
    {
        $recertifications = Assessment::with([
            'asesi.profile',
            'scheme',
            'schedule',
            'priorCertificate.uploader',
            'assessor',
        ])
        ->whereNotNull('prior_certificate_id')
        ->orderByDesc('id')
        ->paginate(15);

        return view('admin.recertification.index', compact('recertifications'));
    }
}
