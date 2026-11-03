<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AsesorProfile;
use App\Models\Assessment;
use App\Models\CertificationScheme;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use App\Models\Tuk;
use App\Models\TukVerification;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AssessmentNextAction;
use App\Services\ScheduleValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreProfileScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function createSamplePngBase64(): string
    {
        $png1x1 = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        return 'data:image/png;base64,' . base64_encode($png1x1);
    }

    private function createAsesiUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => UserRole::Asesi->value,
            'full_name' => 'Ahmad Asesi',
            'phone' => '+6281234567890',
            'status' => 'ACTIVE',
        ], $attributes));
    }

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin->value,
            'full_name' => 'Admin LSP',
            'status' => 'ACTIVE',
        ]);
    }

    private function createAsesorUser(array $profileAttributes = []): User
    {
        $asesor = User::factory()->create([
            'role' => UserRole::Asesor->value,
            'full_name' => 'Budi Santoso Asesor',
            'phone' => '+628111222333',
            'status' => 'ACTIVE',
        ]);

        AsesorProfile::create(array_merge([
            'user_id' => $asesor->id,
            'no_met' => 'MET.' . rand(10000, 99999) . ' 2026',
            'masa_berlaku' => now()->addYear(),
            'bidang_keahlian' => 'Otomotif Kendaraan Ringan',
            'is_active' => true,
        ], $profileAttributes));

        return $asesor;
    }

    private function createVerifiedTuk(): Tuk
    {
        $tuk = Tuk::create([
            'kode_tuk' => 'TUK-' . rand(100, 999),
            'nama_tuk' => 'TUK Otomotif Mandiri',
            'jenis_tuk' => 'Mandiri',
            'alamat' => 'Jl. Industri No. 10 Bekasi',
            'is_active' => true,
        ]);

        $asesor = $this->createAsesorUser();
        $asesorProfile = $asesor->asesorProfile;

        TukVerification::create([
            'tuk_id' => $tuk->id,
            'asesor_id' => $asesorProfile->id,
            'spt_number' => 'ST/01/TUK/LSP/2026',
            'assigner_name' => 'Direktur LSP ProIndo',
            'assigner_position' => 'Direktur Eksekutif',
            'verifier_name' => $asesor->full_name,
            'verifier_position' => 'Asesor Verifikator',
            'verification_date' => now()->subDays(5)->toDateString(),
            'verification_type' => 'mandiri',
            'recommendation' => 'layak',
            'status' => 'diverifikasi',
        ]);

        return $tuk;
    }

    private function createApprovedScheme(string $code = 'SKM-01-2026'): Scheme
    {
        CertificationScheme::firstOrCreate(
            ['kode_skema' => $code],
            [
                'nama_skema' => 'Teknisi Junior Otomotif',
                'jenis_skema' => 'Klaster',
                'status' => 'active',
            ]
        );

        $scheme = Scheme::firstOrCreate(
            ['code' => $code],
            [
                'name' => 'Teknisi Junior Otomotif',
                'type' => 'KLASTER',
                'status' => 'ACTIVE',
            ]
        );

        SchemeVersion::firstOrCreate(
            ['scheme_id' => $scheme->id, 'version' => 1],
            [
                'code' => $code,
                'title' => 'Teknisi Junior Otomotif',
                'status' => 'approved',
                'source_hash' => hash('sha256', 'test-content-' . $code),
            ]
        );

        return $scheme;
    }

    private function createTestSchedule(array $attributes = []): Schedule
    {
        $admin = $this->createAdminUser();
        $scheme = $attributes['scheme'] ?? $this->createApprovedScheme('SKM-01-2026');
        $tuk = $attributes['tuk'] ?? $this->createVerifiedTuk();

        return Schedule::create(array_merge([
            'code' => 'SCH-' . uniqid(),
            'name' => 'Jadwal Uji Tes',
            'scheme_id' => $scheme->id,
            'tuk_id' => $tuk->id,
            'tuk_name' => $tuk->nama_tuk,
            'pic_name' => 'Drs. Koordinator TUK',
            'pic_position' => 'Ketua TUK',
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'capacity' => 20,
            'status' => 'ACTIVE',
            'created_by' => $admin->id,
        ], collect($attributes)->except(['scheme', 'tuk'])->toArray()));
    }

    public function test_asesi_profile_accepts_ktp_and_nik_16_digits(): void
    {
        $user = $this->createAsesiUser();
        $sig = $this->createSamplePngBase64();

        $response = $this->actingAs($user)->post(route('asesi.profile.update'), [
            'full_name' => 'Ahmad Asesi',
            'phone' => '081234567890',
            'identity_type' => 'KTP',
            'nik' => '3216012345670001',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'SMA/Sederajat',
            'alamat_rumah' => 'Jl. Merdeka No. 17',
            'kode_pos' => '17530',
            'jabatan' => 'Mekanik Junior',
            'signature_data' => $sig,
        ]);

        $response->assertRedirect(route('asesi.profile.edit'));
        $this->assertDatabaseHas('m_user_profiles', [
            'user_id' => $user->id,
            'identity_type' => 'KTP',
            'nik' => '3216012345670001',
        ]);
    }

    public function test_asesi_profile_accepts_passport_with_alphanumeric(): void
    {
        $user = $this->createAsesiUser();
        $sig = $this->createSamplePngBase64();

        $response = $this->actingAs($user)->post(route('asesi.profile.update'), [
            'full_name' => 'John Doe',
            'phone' => '+1-555-0199',
            'identity_type' => 'PASPOR',
            'nik' => 'A12345678B',
            'tempat_lahir' => 'Sydney',
            'tanggal_lahir' => '1992-08-20',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Australia',
            'pendidikan_terakhir' => 'S1',
            'alamat_rumah' => '42 Wallaby Way, Sydney',
            'kode_pos' => 'NSW-2000',
            'jabatan' => 'Technician',
            'signature_data' => $sig,
        ]);

        $response->assertRedirect(route('asesi.profile.edit'));
        $this->assertDatabaseHas('m_user_profiles', [
            'user_id' => $user->id,
            'identity_type' => 'PASPOR',
            'nik' => 'A12345678B',
        ]);
    }

    public function test_asesi_profile_accepts_foreign_address_and_international_phone(): void
    {
        $user = $this->createAsesiUser();
        $sig = $this->createSamplePngBase64();

        $response = $this->actingAs($user)->post(route('asesi.profile.update'), [
            'full_name' => 'Akira Tanaka',
            'phone' => '+81 90-1234-5678',
            'identity_type' => 'PASPOR',
            'nik' => 'TK9876543',
            'tempat_lahir' => 'Tokyo',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Jepang',
            'pendidikan_terakhir' => 'D3',
            'alamat_rumah' => 'Shibuya 1-2-3, Tokyo, Japan',
            'kode_pos' => '150-0002',
            'jabatan' => 'EV Specialist',
            'signature_data' => $sig,
        ]);

        $response->assertRedirect(route('asesi.profile.edit'));
        $this->assertDatabaseHas('m_user_profiles', [
            'user_id' => $user->id,
            'alamat_rumah' => 'Shibuya 1-2-3, Tokyo, Japan',
            'kode_pos' => '150-0002',
        ]);
        $this->assertEquals('+81 90-1234-5678', $user->fresh()->phone);
    }

    public function test_asesi_profile_alternative_employment_allows_null_company_and_office_phone(): void
    {
        $user = $this->createAsesiUser();
        $sig = $this->createSamplePngBase64();

        $response = $this->actingAs($user)->post(route('asesi.profile.update'), [
            'full_name' => 'Bambang Siswa',
            'phone' => '089988776655',
            'identity_type' => 'KTP',
            'nik' => '3216012345670002',
            'tempat_lahir' => 'Cikarang',
            'tanggal_lahir' => '2006-03-10',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'SMA/Sederajat',
            'alamat_rumah' => 'Jl. Sukatani No. 5',
            'kode_pos' => '17630',
            'status_pekerjaan' => 'Pelajar/Mahasiswa',
            'jabatan' => 'Pelajar / Siswa',
            'nama_institusi_perusahaan' => null,
            'alamat_kantor' => null,
            'no_telepon_kantor' => null,
            'signature_data' => $sig,
        ]);

        $response->assertRedirect(route('asesi.profile.edit'));
        $this->assertDatabaseHas('m_user_profiles', [
            'user_id' => $user->id,
            'jabatan' => 'Pelajar / Siswa',
            'nama_institusi_perusahaan' => null,
            'alamat_kantor' => null,
        ]);
    }

    public function test_asesi_profile_signature_is_reusable_and_increments_version_on_change(): void
    {
        $user = $this->createAsesiUser();
        $sig1 = $this->createSamplePngBase64();

        // Initial save
        $this->actingAs($user)->post(route('asesi.profile.update'), [
            'full_name' => 'Ahmad Asesi',
            'phone' => '081234567890',
            'nik' => '3216012345670003',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'SMA/Sederajat',
            'alamat_rumah' => 'Jl. Merdeka No. 17',
            'kode_pos' => '17530',
            'jabatan' => 'Mekanik Junior',
            'signature_data' => $sig1,
        ]);

        $profile = UserProfile::where('user_id', $user->id)->first();
        $this->assertEquals(1, $profile->signature_version);

        // Update profile without new signature (reusing existing)
        $this->actingAs($user)->post(route('asesi.profile.update'), [
            'full_name' => 'Ahmad Asesi Updated',
            'phone' => '081234567890',
            'nik' => '3216012345670003',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'D3',
            'alamat_rumah' => 'Jl. Merdeka No. 18',
            'kode_pos' => '17530',
            'jabatan' => 'Teknisi Senior',
            'signature_data' => null, // empty, must keep existing signature
        ]);

        $profile->refresh();
        $this->assertEquals(1, $profile->signature_version);
        $this->assertEquals($sig1, $profile->signature_data);
        $this->assertEquals('Jl. Merdeka No. 18', $profile->alamat_rumah);

        // Update with distinct new signature -> version increments to 2
        $png2 = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAEklEQVR42mNk+M/AwMDEwAAGgAUkAb/9yZ4AAAAASUVORK5CYII=');
        $sig2 = 'data:image/png;base64,' . base64_encode($png2);

        $this->actingAs($user)->post(route('asesi.profile.update'), [
            'full_name' => 'Ahmad Asesi Updated',
            'phone' => '081234567890',
            'nik' => '3216012345670003',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'D3',
            'alamat_rumah' => 'Jl. Merdeka No. 18',
            'kode_pos' => '17530',
            'jabatan' => 'Teknisi Senior',
            'signature_data' => $sig2,
        ]);

        $profile->refresh();
        $this->assertEquals(2, $profile->signature_version);
        $this->assertEquals($sig2, $profile->signature_data);
    }

    public function test_asesi_joins_schedule_snapshots_profile_and_subsequent_profile_edit_does_not_alter_snapshot(): void
    {
        $user = $this->createAsesiUser();
        $sig = $this->createSamplePngBase64();

        UserProfile::create([
            'user_id' => $user->id,
            'identity_type' => 'KTP',
            'nik' => '3216012345670004',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'SMA/Sederajat',
            'alamat_rumah' => 'Alamat Asli Snapshot 1',
            'kode_pos' => '17530',
            'jabatan' => 'Mekanik Pemula',
            'signature_data' => $sig,
            'signature_version' => 1,
        ]);

        $schedule = $this->createTestSchedule([
            'code' => 'JADWAL-01',
            'name' => 'Jadwal Uji Skema 1',
        ]);
        $asesor = $this->createAsesorUser();
        $schedule->assessors()->attach($asesor->id);

        // Asesi joins schedule
        $response = $this->actingAs($user)->post(route('asesi.jadwal.join'), [
            'code' => 'JADWAL-01',
        ]);

        $response->assertRedirect();
        $assessment = Assessment::where('schedule_id', $schedule->id)->where('asesi_id', $user->id)->first();
        $this->assertNotNull($assessment);
        $this->assertNotNull($assessment->snapshot_profile);

        $snapshot = is_array($assessment->snapshot_profile)
            ? $assessment->snapshot_profile
            : json_decode($assessment->snapshot_profile, true);

        $this->assertEquals('Alamat Asli Snapshot 1', $snapshot['alamat_rumah']);
        $this->assertEquals('Mekanik Pemula', $snapshot['jabatan']);

        // Now asesi edits profile
        $this->actingAs($user)->post(route('asesi.profile.update'), [
            'full_name' => 'Ahmad Asesi Baru',
            'phone' => '081234567890',
            'nik' => '3216012345670004',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'S1',
            'alamat_rumah' => 'Alamat Berubah Setelah Pindah Rumah',
            'kode_pos' => '17530',
            'jabatan' => 'Kepala Bengkel',
            'signature_data' => null,
        ]);

        // Assessment snapshot must remain UNCHANGED
        $assessment->refresh();
        $snapshotAfter = is_array($assessment->snapshot_profile)
            ? $assessment->snapshot_profile
            : json_decode($assessment->snapshot_profile, true);

        $this->assertEquals('Alamat Asli Snapshot 1', $snapshotAfter['alamat_rumah']);
        $this->assertEquals('Mekanik Pemula', $snapshotAfter['jabatan']);
    }

    public function test_asesi_can_join_two_schedules_without_reentering_profile(): void
    {
        $user = $this->createAsesiUser();
        $sig = $this->createSamplePngBase64();

        UserProfile::create([
            'user_id' => $user->id,
            'identity_type' => 'KTP',
            'nik' => '3216012345670005',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1998-02-14',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'D3',
            'alamat_rumah' => 'Jl. Dago No. 10',
            'kode_pos' => '40135',
            'jabatan' => 'Teknisi',
            'signature_data' => $sig,
            'signature_version' => 1,
        ]);

        $scheme1 = $this->createApprovedScheme('SKM-01-2026');
        $scheme2 = $this->createApprovedScheme('SKM-02-2026');
        $tuk = $this->createVerifiedTuk();
        $asesor = $this->createAsesorUser();

        $schedule1 = $this->createTestSchedule([
            'code' => 'JADWAL-S1',
            'name' => 'Jadwal Skema 1',
            'scheme' => $scheme1,
            'tuk' => $tuk,
        ]);
        $schedule1->assessors()->attach($asesor->id);

        $schedule2 = $this->createTestSchedule([
            'code' => 'JADWAL-S2',
            'name' => 'Jadwal Skema 2',
            'scheme' => $scheme2,
            'tuk' => $tuk,
        ]);
        $schedule2->assessors()->attach($asesor->id);

        // Join schedule 1
        $res1 = $this->actingAs($user)->post(route('asesi.jadwal.join'), ['code' => 'JADWAL-S1']);
        $res1->assertRedirect();
        $this->assertDatabaseHas('t_assessments', [
            'schedule_id' => $schedule1->id,
            'asesi_id' => $user->id,
        ]);

        // Join schedule 2 without re-entering profile
        $res2 = $this->actingAs($user)->post(route('asesi.jadwal.join'), ['code' => 'JADWAL-S2']);
        $res2->assertRedirect();
        $this->assertDatabaseHas('t_assessments', [
            'schedule_id' => $schedule2->id,
            'asesi_id' => $user->id,
        ]);

        $this->assertEquals(2, Assessment::where('asesi_id', $user->id)->count());
    }

    public function test_join_schedule_is_idempotent_and_does_not_duplicate_assessment(): void
    {
        $user = $this->createAsesiUser();
        $sig = $this->createSamplePngBase64();

        UserProfile::create([
            'user_id' => $user->id,
            'identity_type' => 'KTP',
            'nik' => '3216012345670006',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1996-07-22',
            'jenis_kelamin' => 'L',
            'kebangsaan' => 'Indonesia',
            'pendidikan_terakhir' => 'SMA/Sederajat',
            'alamat_rumah' => 'Jl. Sudirman No. 1',
            'kode_pos' => '10110',
            'jabatan' => 'Operator',
            'signature_data' => $sig,
            'signature_version' => 1,
        ]);

        $schedule = $this->createTestSchedule([
            'code' => 'JADWAL-IDEMPOTENT',
            'name' => 'Jadwal Idempotent Test',
        ]);
        $asesor = $this->createAsesorUser();
        $schedule->assessors()->attach($asesor->id);

        // First join
        $this->actingAs($user)->post(route('asesi.jadwal.join'), ['code' => 'JADWAL-IDEMPOTENT'])->assertRedirect();
        $this->assertEquals(1, Assessment::where('schedule_id', $schedule->id)->where('asesi_id', $user->id)->count());

        // Second join (retry)
        $this->actingAs($user)->post(route('asesi.jadwal.join'), ['code' => 'JADWAL-IDEMPOTENT'])->assertRedirect();
        $this->assertEquals(1, Assessment::where('schedule_id', $schedule->id)->where('asesi_id', $user->id)->count());
    }

    public function test_schedule_capacity_limit_enforced_on_join(): void
    {
        $sig = $this->createSamplePngBase64();

        $schedule = $this->createTestSchedule([
            'code' => 'JADWAL-CAPACITY',
            'name' => 'Jadwal Kuota Kecil',
            'capacity' => 2, // Maximum 2 participants
        ]);
        $asesor = $this->createAsesorUser();
        $schedule->assessors()->attach($asesor->id);

        // Candidate 1
        $user1 = $this->createAsesiUser(['email' => 'c1@test.com']);
        UserProfile::create([
            'user_id' => $user1->id,
            'nik' => '3216012345670011',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'alamat_rumah' => 'Jl. 1',
            'signature_data' => $sig,
        ]);
        $this->actingAs($user1)->post(route('asesi.jadwal.join'), ['code' => 'JADWAL-CAPACITY'])->assertRedirect();

        // Candidate 2
        $user2 = $this->createAsesiUser(['email' => 'c2@test.com']);
        UserProfile::create([
            'user_id' => $user2->id,
            'nik' => '3216012345670012',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'alamat_rumah' => 'Jl. 2',
            'signature_data' => $sig,
        ]);
        $this->actingAs($user2)->post(route('asesi.jadwal.join'), ['code' => 'JADWAL-CAPACITY'])->assertRedirect();

        $this->assertEquals(2, Assessment::where('schedule_id', $schedule->id)->count());

        // Candidate 3: Exceeds capacity
        $user3 = $this->createAsesiUser(['email' => 'c3@test.com']);
        UserProfile::create([
            'user_id' => $user3->id,
            'nik' => '3216012345670013',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'alamat_rumah' => 'Jl. 3',
            'signature_data' => $sig,
        ]);
        $res3 = $this->actingAs($user3)->post(route('asesi.jadwal.join'), ['code' => 'JADWAL-CAPACITY']);
        $res3->assertSessionHas('error');
        $this->assertEquals(2, Assessment::where('schedule_id', $schedule->id)->count());
    }

    public function test_schedule_publishing_blocked_by_draft_scheme_reference(): void
    {
        $scheme = Scheme::create([
            'code' => 'SKM-DRAFT-01',
            'name' => 'Skema Draft Only',
            'type' => 'KLASTER',
            'status' => 'ACTIVE',
        ]);
        // Only draft version, no approved version
        SchemeVersion::create([
            'scheme_id' => $scheme->id,
            'version' => 1,
            'code' => 'SKM-DRAFT-01',
            'title' => 'Skema Draft Only',
            'status' => 'draft',
            'source_hash' => 'hash-draft',
        ]);

        $tuk = $this->createVerifiedTuk();
        $asesor = $this->createAsesorUser();

        $schedule = $this->createTestSchedule([
            'code' => 'SCH-DRAFT-SCHEME',
            'name' => 'Jadwal Skema Draft',
            'scheme' => $scheme,
            'tuk' => $tuk,
            'status' => 'DRAFT',
        ]);
        $schedule->assessors()->attach($asesor->id);

        $validator = app(ScheduleValidationService::class);
        $blockers = $validator->validateForPublishing($schedule);

        $this->assertNotEmpty($blockers);
        $this->assertTrue(collect($blockers)->some(fn ($b) => str_contains($b, 'belum memiliki versi referensi yang disahkan')));
    }

    public function test_schedule_publishing_blocked_by_unverified_tuk(): void
    {
        $scheme = $this->createApprovedScheme('SKM-01-2026');
        $unverifiedTuk = Tuk::create([
            'kode_tuk' => 'TUK-UNVERIFIED',
            'nama_tuk' => 'TUK Belum Diverifikasi',
            'jenis_tuk' => 'Sewaktu',
            'alamat' => 'Jl. Belum Ada',
            'is_active' => true,
        ]);
        $asesor = $this->createAsesorUser();

        $schedule = $this->createTestSchedule([
            'code' => 'SCH-UNVERIFIED-TUK',
            'name' => 'Jadwal TUK Belum Verifikasi',
            'scheme' => $scheme,
            'tuk' => $unverifiedTuk,
            'status' => 'DRAFT',
        ]);
        $schedule->assessors()->attach($asesor->id);

        $validator = app(ScheduleValidationService::class);
        $blockers = $validator->validateForPublishing($schedule);

        $this->assertNotEmpty($blockers);
        $this->assertTrue(collect($blockers)->some(fn ($b) => str_contains($b, 'belum diverifikasi')));
    }

    public function test_schedule_publishing_blocked_by_expired_or_unqualified_asesor(): void
    {
        $scheme = $this->createApprovedScheme('SKM-01-2026');
        $tuk = $this->createVerifiedTuk();

        // Expired assessor
        $expiredAsesor = $this->createAsesorUser([
            'masa_berlaku' => now()->subDay()->toDateString(),
            'bidang_keahlian' => 'Otomotif Kendaraan Ringan',
        ]);

        $schedule = $this->createTestSchedule([
            'code' => 'SCH-EXPIRED-ASESOR',
            'name' => 'Jadwal Asesor Expired',
            'scheme' => $scheme,
            'tuk' => $tuk,
            'status' => 'DRAFT',
        ]);
        $schedule->assessors()->attach($expiredAsesor->id);

        $validator = app(ScheduleValidationService::class);
        $blockers = $validator->validateForPublishing($schedule);

        $this->assertNotEmpty($blockers);
        $this->assertTrue(collect($blockers)->some(fn ($b) => str_contains($b, 'telah berakhir')));
    }

    public function test_schedule_publishing_succeeds_when_all_prerequisites_met(): void
    {
        $scheme = $this->createApprovedScheme('SKM-01-2026');
        $tuk = $this->createVerifiedTuk();
        $asesor = $this->createAsesorUser([
            'masa_berlaku' => now()->addYears(2),
            'bidang_keahlian' => 'Otomotif',
        ]);

        $schedule = $this->createTestSchedule([
            'code' => 'SCH-READY-ALL',
            'name' => 'Jadwal Siap Publish',
            'scheme' => $scheme,
            'tuk' => $tuk,
            'status' => 'DRAFT',
        ]);
        $schedule->assessors()->attach($asesor->id);

        $validator = app(ScheduleValidationService::class);
        $blockers = $validator->validateForPublishing($schedule);

        $this->assertEmpty($blockers);
    }

    public function test_fr041_and_fr042_metadata_recorded_with_authorized_actors(): void
    {
        $admin = $this->createAdminUser();
        $tuk = $this->createVerifiedTuk();
        $asesor = $this->createAsesorUser();
        $scheme = $this->createApprovedScheme('SKM-01-2026');

        // FR-04.1 Surat Tugas Asesor metadata on schedule
        $schedule = Schedule::create([
            'code' => 'SCH-FR041',
            'name' => 'Pelaksanaan Asesmen Batch 1',
            'scheme_id' => $scheme->id,
            'tuk_id' => $tuk->id,
            'tuk_name' => $tuk->nama_tuk,
            'pic_name' => 'Bambang Irawan',
            'pic_position' => 'Koordinator Tempat Uji',
            'letter_number' => 'ST/01/ASESOR/LSP/2026',
            'assigned_by' => 'Direktur LSP P3 PrO Indo',
            'assigned_at' => now(),
            'date' => now()->addDays(3)->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'capacity' => 20,
            'status' => 'ACTIVE',
            'created_by' => $admin->id,
        ]);
        $schedule->assessors()->attach($asesor->id);

        $this->assertEquals('ST/01/ASESOR/LSP/2026', $schedule->letter_number);
        $this->assertEquals('Direktur LSP P3 PrO Indo', $schedule->assigned_by);
        $this->assertNotEquals($admin->full_name, $schedule->pic_name); // creator != coordinator

        // FR-04.2 SPT Verifikasi TUK metadata on TukVerification
        $verification = TukVerification::where('tuk_id', $tuk->id)->first();
        $this->assertNotNull($verification);
        $this->assertEquals('ST/01/TUK/LSP/2026', $verification->spt_number);
        $this->assertEquals('Direktur LSP ProIndo', $verification->assigner_name);
        $this->assertEquals('Direktur Eksekutif', $verification->assigner_position);
        $this->assertEquals($asesor->full_name, $verification->verifier_name);
    }

    public function test_assessment_next_action_is_pure_and_deterministic(): void
    {
        $service = app(AssessmentNextAction::class);

        // Case 1: Incomplete profile
        $userIncomplete = $this->createAsesiUser();
        $action1 = $service->forAsesi($userIncomplete, null);
        $this->assertEquals('profile_incomplete', $action1['type']);
        $this->assertTrue($action1['is_blocked']);

        // Case 2: Complete profile, no schedule
        $userComplete = $this->createAsesiUser();
        UserProfile::create([
            'user_id' => $userComplete->id,
            'nik' => '3216012345670099',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'alamat_rumah' => 'Jl. Sudirman',
            'signature_data' => $this->createSamplePngBase64(),
        ]);
        $action2 = $service->forAsesi($userComplete, null);
        $this->assertEquals('no_schedule', $action2['type']);
        $this->assertFalse($action2['is_blocked']);

        // Case 3: Assessment in DRAFT
        $schedule = $this->createTestSchedule([
            'code' => 'SCH-NEXT-01',
            'name' => 'Jadwal Next Action',
        ]);
        $assessment = Assessment::create([
            'schedule_id' => $schedule->id,
            'asesi_id' => $userComplete->id,
            'apl_status' => 'DRAFT',
            'admin_status' => 'APPROVED',
            'registered_at' => now(),
            'last_active_step' => 'apl',
        ]);

        $action3 = $service->forAsesi($userComplete, $assessment);
        $this->assertEquals('draft_apl', $action3['type']);
        $this->assertFalse($action3['is_blocked']);

        // Verify service did NOT modify DB records
        $this->assertEquals('DRAFT', $assessment->fresh()->apl_status);
    }
}
