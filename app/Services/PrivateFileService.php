<?php

namespace App\Services;

use App\Models\PrivateFile;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateFileService
{
    public const MAX_FILE_SIZE_BYTES = 10485760; // 10 MB

    public const ALLOWED_MIMES = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'application/pdf' => ['pdf'],
    ];

    /**
     * Safely validate and store an uploaded file on the private disk.
     */
    public function storeUploadedFile(
        UploadedFile $file,
        string $category = 'general',
        ?string $ownerType = null,
        ?int $ownerId = null,
        ?int $uploadedBy = null
    ): PrivateFile {
        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'file' => ['Berkas yang diunggah tidak valid atau rusak.'],
            ]);
        }

        // 1. Validate file size
        if ($file->getSize() > self::MAX_FILE_SIZE_BYTES) {
            throw ValidationException::withMessages([
                'file' => ['Ukuran berkas melebihi batas maksimum 10MB.'],
            ]);
        }

        // 2. Validate MIME type
        $detectedMime = $file->getMimeType() ?: $file->getClientMimeType();
        if (! array_key_exists($detectedMime, self::ALLOWED_MIMES)) {
            throw ValidationException::withMessages([
                'file' => ['Tipe konten berkas tidak didukung (hanya PDF, JPG, PNG).'],
            ]);
        }

        // 3. Validate file extension matches detected MIME
        $clientExt = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = self::ALLOWED_MIMES[$detectedMime];
        if (! in_array($clientExt, $allowedExtensions, true)) {
            throw ValidationException::withMessages([
                'file' => ['Ekstensi berkas tidak sesuai dengan tipe MIME yang terdeteksi.'],
            ]);
        }

        // Disallow dangerous extensions
        $dangerous = ['php', 'phtml', 'phar', 'exe', 'sh', 'js', 'html', 'htm', 'bat', 'cmd'];
        if (in_array($clientExt, $dangerous, true)) {
            throw ValidationException::withMessages([
                'file' => ['Ekstensi berkas berbahaya dan ditolak.'],
            ]);
        }

        // 4. Image decoding verification to prevent MIME spoofing
        if (str_starts_with($detectedMime, 'image/')) {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                throw ValidationException::withMessages([
                    'file' => ['Berkas gambar korup atau bukan gambar yang valid.'],
                ]);
            }
        }

        // 5. Server-generated storage path (no user-supplied path)
        $cleanCategory = preg_replace('/[^a-zA-Z0-9_\-]/', '', $category) ?: 'general';
        $serverFileName = Str::uuid()->toString().'.'.$clientExt;
        $targetPath = "secure/{$cleanCategory}/{$serverFileName}";

        // 6. Calculate SHA-256 hash
        $fileHash = hash_file('sha256', $file->getRealPath());

        // 7. Store onto private disk
        $diskName = config('filesystems.disks.private') ? 'private' : 'local';
        $stored = Storage::disk($diskName)->put($targetPath, file_get_contents($file->getRealPath()));

        if (! $stored) {
            throw new \RuntimeException('Gagal menyimpan berkas ke media penyimpanan privat.');
        }

        // 8. Sanitize original filename (prevent path traversal / PII leak)
        $sanitizedOriginalName = basename(preg_replace('/[\x00-\x1F\x7F\/\\\\]/', '', $file->getClientOriginalName()));

        return PrivateFile::create([
            'disk' => $diskName,
            'path' => $targetPath,
            'original_name' => $sanitizedOriginalName,
            'mime_type' => $detectedMime,
            'file_size' => $file->getSize(),
            'file_hash' => $fileHash,
            'owner_type' => $ownerType ?? User::class,
            'owner_id' => $ownerId ?? ($uploadedBy ?? auth()->id() ?? 0),
            'uploaded_by' => $uploadedBy ?? auth()->id(),
            'status' => 'available',
        ]);
    }

    /**
     * Authorize and serve a private file response.
     */
    public function serve(PrivateFile $privateFile, User $user): Response|StreamedResponse
    {
        // Object policy authorization
        if (Gate::forUser($user)->denies('view', $privateFile)) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat berkas ini.');
        }

        // Revocation check
        if ($privateFile->status === 'revoked') {
            abort(403, 'Berkas telah dicabut.');
        }

        // Verify existence on disk
        $disk = $privateFile->disk ?: (config('filesystems.disks.private') ? 'private' : 'local');
        if (! Storage::disk($disk)->exists($privateFile->path)) {
            abort(404, 'Berkas privat tidak ditemukan.');
        }

        $safeName = basename($privateFile->original_name ?: 'document');

        return Storage::disk($disk)->response($privateFile->path, $safeName, [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="'.addslashes($safeName).'"',
        ]);
    }
}
