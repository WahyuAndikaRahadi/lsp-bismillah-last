<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\CertificateReceiptEvent;
use App\Models\CertificationDecision;
use App\Services\PrivateFileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function __construct(
        protected PrivateFileService $privateFileService
    ) {}

    /**
     * Tampilkan daftar sertifikat dan asesi yang berhak menerima sertifikat.
     */
    public function index(Request $request): View
    {
        $certificates = Certificate::with(['assessment.asesi.profile', 'assessment.scheme', 'uploader', 'receiptEvents'])
            ->orderByDesc('id')
            ->paginate(20);

        // Cari assessment dengan keputusan KOMPETEN yang belum diunggah sertifikat aktifnya
        $eligibleAssessments = Assessment::with(['asesi.profile', 'scheme'])
            ->where('pleno_decision', 'KOMPETEN')
            ->whereDoesntHave('certificates', function ($query) {
                $query->where('status', 'ACTIVE');
            })
            ->latest('id')
            ->take(10)
            ->get();

        return view('admin.certificates.index', compact('certificates', 'eligibleAssessments'));
    }

    /**
     * Form unggah sertifikat untuk asesi yang sah.
     */
    public function create(Assessment $assessment): View|RedirectResponse
    {
        $latestDecision = CertificationDecision::where('assessment_id', $assessment->id)
            ->where('is_superseded', false)
            ->latest('id')
            ->first();

        if (! $latestDecision || $latestDecision->decision !== 'KOMPETEN') {
            return redirect()->route('admin.certificates.index')
                ->with('error', 'Sertifikat hanya dapat diunggah untuk asesi dengan keputusan sertifikasi KOMPETEN yang sah.');
        }

        $activeCert = $assessment->activeCertificate();

        return view('admin.certificates.create', compact('assessment', 'latestDecision', 'activeCert'));
    }

    /**
     * Simpan berkas fisik dan metadata sertifikat ke private storage.
     */
    public function store(Request $request, Assessment $assessment): RedirectResponse
    {
        $latestDecision = CertificationDecision::where('assessment_id', $assessment->id)
            ->where('is_superseded', false)
            ->latest('id')
            ->first();

        if (! $latestDecision || $latestDecision->decision !== 'KOMPETEN') {
            return redirect()->back()
                ->with('error', 'Sertifikat hanya dapat diunggah untuk asesi dengan keputusan sertifikasi KOMPETEN yang sah.');
        }

        $validated = $request->validate([
            'certificate_number' => ['required', 'string', 'max:100', 'unique:t_certificates,certificate_number'],
            'blanko_number' => ['nullable', 'string', 'max:100'],
            'issue_date' => ['required', 'date'],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:valid_from'],
            'certificate_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'certificate_number.unique' => 'Nomor sertifikat sudah terdaftar pada sistem LSP.',
            'valid_until.after_or_equal' => 'Masa berlaku akhir sertifikat harus sama atau setelah tanggal mulai berlaku.',
            'certificate_file.required' => 'Berkas sertifikat fisik wajib diunggah.',
        ]);

        $actor = Auth::user();

        // 1. Simpan berkas melalui PrivateFileService (validasi MIME, ekstensi, anti-spoofing)
        $privateFile = $this->privateFileService->storeUploadedFile(
            file: $request->file('certificate_file'),
            category: 'certificates',
            ownerType: Assessment::class,
            ownerId: $assessment->id,
            uploadedBy: $actor->id
        );

        DB::beginTransaction();
        try {
            // 2. Tandai sertifikat sebelumnya sebagai SUPERSEDED jika penggantian
            $activeCert = $assessment->activeCertificate();
            $nextRevision = 1;
            if ($activeCert) {
                $nextRevision = $activeCert->revision + 1;
                $activeCert->update(['status' => 'SUPERSEDED']);
            }

            // 3. Rekam sertifikat baru
            $certificate = Certificate::create([
                'assessment_id' => $assessment->id,
                'decision_id' => $latestDecision->id,
                'certificate_number' => $validated['certificate_number'],
                'blanko_number' => $validated['blanko_number'] ?? null,
                'issue_date' => $validated['issue_date'],
                'valid_from' => $validated['valid_from'],
                'valid_until' => $validated['valid_until'],
                'private_file_path' => $privateFile->path,
                'file_hash' => $privateFile->file_hash,
                'uploader_id' => $actor->id,
                'status' => 'ACTIVE',
                'revision' => $nextRevision,
            ]);

            // 4. Rekam event upload
            CertificateReceiptEvent::create([
                'certificate_id' => $certificate->id,
                'assessment_id' => $assessment->id,
                'recipient_id' => $actor->id,
                'event_type' => 'upload',
                'occurred_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.certificates.index')
                ->with('success', "Sertifikat nomor {$certificate->certificate_number} (Revisi {$certificate->revision}) berhasil diunggah.");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal memproses unggah sertifikat: ' . $e->getMessage());
        }
    }

    /**
     * Unduh berkas fisik sertifikat oleh Admin dengan pencatatan audit.
     */
    public function download(Certificate $certificate): StreamedResponse
    {
        Gate::authorize('download', $certificate);

        $disk = config('filesystems.disks.private') ? 'private' : 'local';
        if (! Storage::disk($disk)->exists($certificate->private_file_path)) {
            abort(404, 'Berkas fisik sertifikat tidak ditemukan di penyimpanan.');
        }

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
}
