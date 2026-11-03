<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\PrivateFile;
use App\Models\User;
use App\Services\PrivateFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CorePrivateFileTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $asesi;
    private PrivateFileService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        Storage::fake('local');

        $this->service = app(PrivateFileService::class);

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->asesi = User::factory()->create([
            'role' => UserRole::Asesi,
            'is_active' => true,
        ]);
    }

    public function test_valid_file_upload_stores_to_private_disk_with_hash(): void
    {
        $file = UploadedFile::fake()->create('dokumen_legalitas.pdf', 500, 'application/pdf');

        $privateFile = $this->service->storeUploadedFile(
            file: $file,
            category: 'legalitas',
            ownerType: User::class,
            ownerId: $this->asesi->id,
            uploadedBy: $this->asesi->id
        );

        $this->assertInstanceOf(PrivateFile::class, $privateFile);
        $this->assertEquals('private', $privateFile->disk);
        $this->assertEquals('application/pdf', $privateFile->mime_type);
        $this->assertStringStartsWith('secure/legalitas/', $privateFile->path);
        $this->assertNotEmpty($privateFile->file_hash);

        // Verify physically exists on fake private disk
        $this->assertTrue(Storage::disk('private')->exists($privateFile->path));
    }

    public function test_mime_spoofing_is_rejected(): void
    {
        // Fake PHP script disguised as a jpg
        $file = UploadedFile::fake()->createWithContent('malicious.jpg', '<?php echo "evil"; ?>');

        $this->expectException(ValidationException::class);
        $this->service->storeUploadedFile($file, 'avatars');
    }

    public function test_oversize_file_exceeding_10mb_is_rejected(): void
    {
        // 11 MB file
        $file = UploadedFile::fake()->create('huge.pdf', 11 * 1024, 'application/pdf');

        $this->expectException(ValidationException::class);
        $this->service->storeUploadedFile($file, 'docs');
    }

    public function test_path_traversal_is_sanitized_in_original_filename(): void
    {
        $file = UploadedFile::fake()->create('../../../../etc/passwd.pdf', 100, 'application/pdf');

        $privateFile = $this->service->storeUploadedFile($file, 'traversal_test');

        $this->assertStringNotContainsString('..', $privateFile->original_name);
        $this->assertStringNotContainsString('/', $privateFile->original_name);
        $this->assertStringNotContainsString('\\', $privateFile->original_name);
        $this->assertEquals('passwd.pdf', $privateFile->original_name);
    }

    public function test_revoked_file_is_inaccessible(): void
    {
        $privateFile = PrivateFile::create([
            'disk' => 'private',
            'path' => 'secure/revoked/revoked_doc.pdf',
            'original_name' => 'revoked_doc.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'file_hash' => hash('sha256', 'content'),
            'owner_type' => User::class,
            'owner_id' => $this->asesi->id,
            'uploaded_by' => $this->asesi->id,
            'status' => 'revoked', // Explicitly revoked
        ]);
        Storage::disk('private')->put('secure/revoked/revoked_doc.pdf', 'content');

        $this->actingAs($this->asesi);
        $response = $this->get(route('secure-file.show', $privateFile->id));
        $response->assertForbidden();
    }

    public function test_missing_physical_file_returns_clean_404_without_leaking_server_paths(): void
    {
        $privateFile = PrivateFile::create([
            'disk' => 'private',
            'path' => 'secure/missing/non_existent.pdf',
            'original_name' => 'non_existent.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'file_hash' => hash('sha256', 'none'),
            'owner_type' => User::class,
            'owner_id' => $this->admin->id,
            'uploaded_by' => $this->admin->id,
            'status' => 'available',
        ]);
        // Do not put physical file in disk

        $this->actingAs($this->admin);
        $response = $this->get(route('secure-file.show', $privateFile->id));
        $response->assertNotFound();

        // Ensure internal storage path is NOT in response body
        $this->assertStringNotContainsString('secure/missing/non_existent.pdf', $response->getContent());
    }

    public function test_served_file_includes_strict_security_headers(): void
    {
        $privateFile = PrivateFile::create([
            'disk' => 'private',
            'path' => 'secure/headers/report.pdf',
            'original_name' => 'report.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'file_hash' => hash('sha256', 'report'),
            'owner_type' => User::class,
            'owner_id' => $this->admin->id,
            'uploaded_by' => $this->admin->id,
            'status' => 'available',
        ]);
        Storage::disk('private')->put('secure/headers/report.pdf', 'report');

        $this->actingAs($this->admin);
        $response = $this->get(route('secure-file.show', $privateFile->id));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
    }
}
