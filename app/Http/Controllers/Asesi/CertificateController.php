<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\CertificateReceiptEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    /**
     * Tampilkan semua sertifikat dan status sertifikasi milik asesi login.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Mengambil semua assessment milik asesi login beserta scheme dan sertifikatnya
        $assessments = Assessment::with([
            'scheme',
            'certificates.receiptEvents',
            'certificationDecisions' => function ($query) {
                $query->where('is_superseded', false);
            },
        ])
        ->where('asesi_id', $user->id)
        ->orderByDesc('id')
        ->get();

        return view('asesi.certificates.index', compact('assessments'));
    }

    /**
     * Tampilkan detail tanda terima sertifikat FR-10.2 dan opsi pengunduhan.
     */
    public function show(Certificate $certificate): View
    {
        Gate::authorize('view', $certificate);

        $certificate->load([
            'assessment.scheme',
            'assessment.asesi.profile',
            'receiptEvents',
            'uploader',
        ]);

        return view('asesi.certificates.show', compact('certificate'));
    }

    /**
     * Unduh berkas fisik sertifikat oleh asesi pemilik sertifikat sah.
     */
    public function download(Certificate $certificate): StreamedResponse
    {
        Gate::authorize('download', $certificate);

        $disk = config('filesystems.disks.private') ? 'private' : 'local';
        if (! Storage::disk($disk)->exists($certificate->private_file_path)) {
            abort(404, 'Berkas fisik sertifikat tidak ditemukan di penyimpanan privat.');
        }

        // Catat event unduh pada audit tanda terima
        CertificateReceiptEvent::create([
            'certificate_id' => $certificate->id,
            'assessment_id' => $certificate->assessment_id,
            'recipient_id' => Auth::id(),
            'event_type' => 'download',
            'occurred_at' => now(),
        ]);

        $safeNum = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $certificate->certificate_number);
        $filename = "Sertifikat_{$safeNum}.pdf";

        return Storage::disk($disk)->download($certificate->private_file_path, $filename, [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Konfirmasi penerimaan sertifikat fisik/digital (FR-10.2).
     */
    public function acknowledge(Request $request, Certificate $certificate): RedirectResponse
    {
        Gate::authorize('acknowledge', $certificate);

        $user = Auth::user();
        $signatureData = $request->input('signature_data') ?? $user->profile?->signature_data;

        CertificateReceiptEvent::create([
            'certificate_id' => $certificate->id,
            'assessment_id' => $certificate->assessment_id,
            'recipient_id' => $user->id,
            'event_type' => 'acknowledge',
            'occurred_at' => now(),
            'signature_data' => $signatureData,
        ]);

        return redirect()->back()->with('success', 'Tanda terima sertifikat (FR-10.2) berhasil dicatat dan disahkan.');
    }
}
