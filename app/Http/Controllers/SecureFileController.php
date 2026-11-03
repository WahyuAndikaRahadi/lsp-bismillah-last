<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentIaResponse;
use App\Models\AssessmentProof;
use App\Models\PrivateFile;
use App\Models\User;
use App\Services\PrivateFileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureFileController extends Controller
{
    /**
     * Serve a canonical PrivateFile record with object-level authorization.
     */
    public function show(Request $request, PrivateFile $file, PrivateFileService $fileService): Response|StreamedResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        return $fileService->serve($file, $user);
    }

    /**
     * Serve legacy AssessmentProof with strict object-level authorization.
     */
    public function showProof(Request $request, AssessmentProof $proof): Response|StreamedResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $proof->loadMissing('assessment.schedule.assessors');
        $assessment = $proof->assessment;

        $this->authorizeAccess($user, $assessment);

        return $this->serveFile($proof->file_path, $proof->original_filename);
    }

    /**
     * Serve legacy AssessmentIaResponse evidence with strict object-level authorization.
     */
    public function showEvidence(Request $request, AssessmentIaResponse $response): Response|StreamedResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $response->loadMissing('assessment.schedule.assessors');
        $assessment = $response->assessment;

        $this->authorizeAccess($user, $assessment);

        $data = is_array($response->data_json)
            ? $response->data_json
            : (json_decode($response->data_json ?? '[]', true) ?: []);

        $path = $data['evidence_image_path'] ?? null;
        if (! $path) {
            abort(404, 'Berkas bukti observasi/praktik belum diunggah.');
        }

        $filename = $data['evidence_original_name'] ?? basename($path);

        return $this->serveFile($path, $filename);
    }

    protected function authorizeAccess(User $user, ?Assessment $assessment): void
    {
        if (! $assessment) {
            abort(404, 'Data asesmen tidak ditemukan.');
        }

        // Admin has full audit / review access
        if ($user->isAdmin()) {
            return;
        }

        // Asesi can only view their own files
        if ($user->isAsesi() && $assessment->asesi_id === $user->id) {
            return;
        }

        // Assigned Assessor (direct or via schedule)
        if ($user->isAsesor()) {
            if ($assessment->assessor_id === $user->id) {
                return;
            }

            if ($assessment->schedule && $assessment->schedule->assessors->contains('id', $user->id)) {
                return;
            }
        }

        abort(403, 'Anda tidak memiliki hak akses untuk melihat berkas ini.');
    }

    protected function serveFile(string $path, ?string $filename = null): Response|StreamedResponse
    {
        // Strictly private/local disk only; NEVER fallback to public disk
        $disk = config('filesystems.disks.private') ? 'private' : 'local';

        if (! Storage::disk($disk)->exists($path)) {
            // Also check local root storage
            if (Storage::disk('local')->exists($path)) {
                $disk = 'local';
            } else {
                abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
            }
        }

        $filename = basename($filename ?: basename($path));

        return Storage::disk($disk)->response($path, $filename, [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
        ]);
    }
}
