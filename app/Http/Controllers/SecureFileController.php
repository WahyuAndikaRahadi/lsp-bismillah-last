<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentIaResponse;
use App\Models\AssessmentProof;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureFileController extends Controller
{
    public function showProof(Request $request, AssessmentProof $proof): Response|StreamedResponse
    {
        $proof->loadMissing('assessment.schedule.assessors');
        $assessment = $proof->assessment;

        $this->authorizeAccess($request->user(), $assessment);

        return $this->serveFile($proof->file_path, $proof->original_filename);
    }

    public function showEvidence(Request $request, AssessmentIaResponse $response): Response|StreamedResponse
    {
        $response->loadMissing('assessment.schedule.assessors');
        $assessment = $response->assessment;

        $this->authorizeAccess($request->user(), $assessment);

        $data = is_array($response->data_json)
            ? $response->data_json
            : (json_decode($response->data_json ?? '[]', true) ?: []);

        $path = $data['evidence_image_path'] ?? null;
        if (! $path) {
            return $this->serveMissingFileFallback('bukti_observasi.jpg', 'Berkas bukti observasi/praktik belum diunggah.');
        }

        $filename = $data['evidence_original_name'] ?? basename($path);

        return $this->serveFile($path, $filename);
    }

    protected function authorizeAccess(User $user, ?Assessment $assessment): void
    {
        if (! $assessment) {
            abort(404, 'Data asesmen tidak ditemukan.');
        }

        // Admins & Komite Teknis have full audit / review access
        if ($user->isRole('admin') || $user->isRole('super_admin') || $user->isRole('komite_teknis')) {
            return;
        }

        // Asesi can only view their own files
        if ($assessment->asesi_id === $user->id) {
            return;
        }

        // Assigned Assessor (direct or via schedule)
        if ($assessment->assessor_id === $user->id) {
            return;
        }

        if ($assessment->schedule && $assessment->schedule->assessors->contains('id', $user->id)) {
            return;
        }

        // TUK Admin for their own TUK
        $userRole = is_object($user->role) ? $user->role->value : (string) $user->role;
        if (($userRole === 'TUK_ADMIN' || $userRole === 'tuk') && $user->tuk_id && $assessment->schedule && $assessment->schedule->tuk_id === $user->tuk_id) {
            return;
        }

        abort(403, 'Anda tidak memiliki hak akses untuk melihat berkas ini.');
    }

    protected function serveFile(string $path, ?string $filename = null): Response|StreamedResponse
    {
        $disk = 'local';
        if (! Storage::disk('local')->exists($path)) {
            if (Storage::disk('public')->exists($path)) {
                $disk = 'public';
            } else {
                return $this->serveMissingFileFallback($filename ?: basename($path));
            }
        }

        $filename = $filename ?: basename($path);

        return Storage::disk($disk)->response($path, $filename, [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
        ]);
    }

    protected function serveMissingFileFallback(string $filename, ?string $message = null): Response
    {
        $safeName = htmlspecialchars($filename, ENT_QUOTES, 'UTF-8');
        $safeMsg = htmlspecialchars($message ?? 'File fisik sementara tidak ditemukan pada direktori penyimpanan server.', ENT_QUOTES, 'UTF-8');
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="480" viewBox="0 0 800 480">
    <rect width="100%" height="100%" fill="#f8fafc"/>
    <rect x="24" y="24" width="752" height="432" rx="16" fill="#ffffff" stroke="#cbd5e1" stroke-width="2" stroke-dasharray="8 8"/>
    <g transform="translate(400, 180)" text-anchor="middle">
        <circle cx="0" cy="0" r="42" fill="#fef2f2" stroke="#fca5a5" stroke-width="2"/>
        <path d="M-12 -12 L12 12 M12 -12 L-12 12" stroke="#ef4444" stroke-width="4" stroke-linecap="round"/>
        <text y="75" font-family="system-ui, -apple-system, sans-serif" font-size="20" font-weight="bold" fill="#0f172a">Berkas Lampiran Tidak Ditemukan</text>
        <text y="105" font-family="system-ui, -apple-system, sans-serif" font-size="14" font-weight="600" fill="#475569">Nama File: {$safeName}</text>
        <text y="130" font-family="system-ui, -apple-system, sans-serif" font-size="12" fill="#94a3b8">{$safeMsg}</text>
    </g>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-cache, private',
            'Content-Disposition' => 'inline; filename="dokumen-tidak-tersedia.svg"',
        ]);
    }
}
