<?php

namespace App\Http\Controllers\Asesi;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentVerificationStatus;
use App\Enums\ScheduleStatus;
use App\Http\Controllers\Controller;
use App\Mail\ApplicationSubmittedMail;
use App\Models\ApplicationDocument;
use App\Models\AsesiProfile;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\SchemeRequirement;
use App\Services\WilayahService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(
        protected WilayahService $wilayahService
    ) {}

    /**
     * Tampilkan daftar permohonan sertifikasi milik asesi
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $applications = AssessmentApplication::with(['scheme', 'schedule.tuk', 'documents'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('asesi.applications.index', [
            'applications' => $applications,
            'profile' => $user->asesiProfile,
        ]);
    }

    /**
     * Tampilkan formulir permohonan sertifikasi baru (FR-APL-01)
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $profile = $user->asesiProfile ?? new AsesiProfile([
            'nama_lengkap' => $user->name,
            'nik' => $user->nik,
            'telepon' => $user->phone,
            'kebangsaan' => 'Indonesia',
        ]);

        $schemes = CertificationScheme::where('is_active', true)
            ->with(['requirements', 'competencyUnits'])
            ->orderBy('nama_skema')
            ->get();

        $selectedSchemeId = $request->query('scheme_id', $schemes->first()?->id);

        $schedules = AssessmentSchedule::whereIn('status', [ScheduleStatus::Pendaftaran, ScheduleStatus::Direncanakan])
            ->with(['scheme', 'tuk'])
            ->orderBy('tgl_asesmen')
            ->get();

        $selectedScheduleId = $request->query('schedule_id');

        $provinces = $this->wilayahService->getProvinces();

        return view('asesi.applications.create', [
            'user' => $user,
            'profile' => $profile,
            'schemes' => $schemes,
            'selectedSchemeId' => $selectedSchemeId,
            'schedules' => $schedules,
            'selectedScheduleId' => $selectedScheduleId,
            'provinces' => $provinces,
        ]);
    }

    /**
     * Simpan permohonan sertifikasi baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Bagian 1: Data Pribadi
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kebangsaan' => ['required', 'string', 'max:50'],
            'alamat' => ['required', 'string'],
            'rt_rw' => ['nullable', 'string', 'max:20'],
            'provinsi_kode' => ['nullable', 'string'],
            'provinsi_nama' => ['nullable', 'string'],
            'kota_kode' => ['nullable', 'string'],
            'kota_nama' => ['nullable', 'string'],
            'kecamatan_kode' => ['nullable', 'string'],
            'kecamatan_nama' => ['nullable', 'string'],
            'kelurahan_kode' => ['nullable', 'string'],
            'kelurahan_nama' => ['nullable', 'string'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'telepon' => ['required', 'string', 'max:25'],
            'pendidikan_terakhir' => ['required', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['nullable', 'string', 'max:150'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'alamat_kantor' => ['nullable', 'string'],
            'telp_kantor' => ['nullable', 'string', 'max:25'],
            'email_kantor' => ['nullable', 'email'],

            // Bagian 2: Data Skema
            'scheme_id' => ['required', 'exists:certification_schemes,id'],
            'schedule_id' => ['nullable', 'exists:assessment_schedules,id'],
            'tujuan_asesmen' => ['required', 'string', 'max:80'],

            // Tanda tangan & Persetujuan
            'ttd_asesi' => ['nullable', 'string'],
            'setuju_ketentuan' => ['accepted'],

            // File uploads
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // max 5MB
        ], [
            'nik.digits' => 'Nomor NIK harus 16 digit sesuai KTP.',
            'setuju_ketentuan.accepted' => 'Anda wajib menyetujui pernyataan kebenaran data formulir FR-APL-01.',
            'documents.*.max' => 'Ukuran berkas persyaratan maksimal 5 MB per dokumen.',
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            // 1. Update / Create Asesi Profile
            AsesiProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_lengkap' => trim($request->nama_lengkap),
                    'nik' => trim($request->nik),
                    'tempat_lahir' => trim($request->tempat_lahir),
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'kebangsaan' => $request->kebangsaan ?: 'Indonesia',
                    'alamat' => trim($request->alamat),
                    'rt_rw' => $request->rt_rw,
                    'provinsi_kode' => $request->provinsi_kode,
                    'provinsi_nama' => $request->provinsi_nama,
                    'kota_kode' => $request->kota_kode,
                    'kota_nama' => $request->kota_nama,
                    'kecamatan_kode' => $request->kecamatan_kode,
                    'kecamatan_nama' => $request->kecamatan_nama,
                    'kelurahan_kode' => $request->kelurahan_kode,
                    'kelurahan_nama' => $request->kelurahan_nama,
                    'kode_pos' => $request->kode_pos,
                    'telepon' => trim($request->telepon),
                    'pendidikan_terakhir' => $request->pendidikan_terakhir,
                    'pekerjaan' => $request->pekerjaan,
                    'nama_perusahaan' => $request->nama_perusahaan,
                    'jabatan' => $request->jabatan,
                    'alamat_kantor' => $request->alamat_kantor,
                    'telp_kantor' => $request->telp_kantor,
                    'email_kantor' => $request->email_kantor,
                ]
            );

            // Update user name and phone if needed
            $user->update([
                'name' => trim($request->nama_lengkap),
                'nik' => trim($request->nik),
                'phone' => trim($request->telepon),
            ]);

            // 2. Generate Nomor Pendaftaran
            $nomorPendaftaran = AssessmentApplication::generateRegistrationNumber();

            // 3. Simpan Permohonan APL-01
            $isDraft = $request->has('action_draft');
            $status = $isDraft ? ApplicationStatus::Draft : ApplicationStatus::Diajukan;

            $application = AssessmentApplication::create([
                'nomor_pendaftaran' => $nomorPendaftaran,
                'user_id' => $user->id,
                'scheme_id' => $request->scheme_id,
                'schedule_id' => $request->schedule_id,
                'tujuan_asesmen' => $request->tujuan_asesmen,
                'status' => $status,
                'tgl_pengajuan' => now(),
                'ttd_asesi' => $request->ttd_asesi ?: $user->signature_image,
                'tgl_ttd_asesi' => now(),
            ]);

            // 4. Upload berkas persyaratan skema
            $scheme = CertificationScheme::with('requirements')->findOrFail($request->scheme_id);
            $uploadedFiles = $request->file('documents', []);

            foreach ($scheme->requirements as $requirement) {
                if (isset($uploadedFiles[$requirement->id])) {
                    $file = $uploadedFiles[$requirement->id];
                    $storedPath = $file->store("documents/applications/{$nomorPendaftaran}", 'public');

                    ApplicationDocument::create([
                        'application_id' => $application->id,
                        'requirement_id' => $requirement->id,
                        'nama_dokumen' => $requirement->nama_dokumen,
                        'file_path' => $storedPath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'status_verifikasi' => DocumentVerificationStatus::Pending,
                    ]);
                }
            }

            // Jika ada dokumen tambahan custom
            if (isset($uploadedFiles['custom'])) {
                foreach ($uploadedFiles['custom'] as $idx => $customFile) {
                    $customName = $request->input("custom_names.{$idx}", 'Dokumen Tambahan #'.($idx + 1));
                    $storedPath = $customFile->store("documents/applications/{$nomorPendaftaran}", 'public');

                    ApplicationDocument::create([
                        'application_id' => $application->id,
                        'requirement_id' => null,
                        'nama_dokumen' => $customName,
                        'file_path' => $storedPath,
                        'file_name' => $customFile->getClientOriginalName(),
                        'file_size' => $customFile->getSize(),
                        'file_type' => $customFile->getClientOriginalExtension(),
                        'status_verifikasi' => DocumentVerificationStatus::Pending,
                    ]);
                }
            }

            DB::commit();

            // Kirim notifikasi email ke asesi
            if ($status === ApplicationStatus::Diajukan) {
                try {
                    Mail::to($user->email)->send(new ApplicationSubmittedMail($application));
                } catch (\Throwable $e) {
                    // Jangan gagalkan submit jika mail server offline
                }
            }

            $message = $isDraft
                ? 'Draft permohonan sertifikasi berhasil disimpan.'
                : 'Permohonan sertifikasi (FR-APL-01) berhasil diajukan! Tim admin LSP akan segera memverifikasi berkas Anda.';

            return redirect()->route('asesi.applications.show', $application)
                ->with('success', $message);

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal mengajukan permohonan: '.$e->getMessage());
        }
    }

    /**
     * Tampilkan detail resmi FR-APL-01
     */
    public function show(Request $request, AssessmentApplication $application): View
    {
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $application->load([
            'scheme.competencyUnits.elements.criterias',
            'scheme.requirements',
            'schedule.tuk',
            'documents.requirement',
            'user.asesiProfile',
        ]);

        return view('asesi.applications.show', [
            'application' => $application,
            'profile' => $application->user->asesiProfile,
        ]);
    }

    /**
     * Form edit / revisi permohonan
     */
    public function edit(Request $request, AssessmentApplication $application): View|RedirectResponse
    {
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (! $application->status->canEdit()) {
            return redirect()->route('asesi.applications.show', $application)
                ->with('error', 'Permohonan dalam status saat ini tidak dapat diubah.');
        }

        $application->load([
            'scheme.requirements',
            'documents',
            'user.asesiProfile',
        ]);

        $provinces = $this->wilayahService->getProvinces();

        return view('asesi.applications.edit', [
            'application' => $application,
            'profile' => $application->user->asesiProfile,
            'provinces' => $provinces,
        ]);
    }

    /**
     * Update / Submit ulang berkas revisi
     */
    public function update(Request $request, AssessmentApplication $application): RedirectResponse
    {
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (! $application->status->canEdit()) {
            return redirect()->route('asesi.applications.show', $application)
                ->with('error', 'Permohonan tidak dapat diubah.');
        }

        $request->validate([
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'catatan_pemohon' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        try {
            $uploadedFiles = $request->file('documents', []);

            foreach ($uploadedFiles as $reqId => $file) {
                if (! $file) {
                    continue;
                }

                $requirement = SchemeRequirement::find($reqId);
                $docName = $requirement ? $requirement->nama_dokumen : 'Dokumen Pendukung';

                $storedPath = $file->store("documents/applications/{$application->nomor_pendaftaran}", 'public');

                // Cari dokumen lama jika ada
                $existingDoc = ApplicationDocument::where('application_id', $application->id)
                    ->where('requirement_id', $reqId)
                    ->first();

                if ($existingDoc) {
                    // Delete old file
                    Storage::disk('public')->delete($existingDoc->file_path);
                    $existingDoc->update([
                        'file_path' => $storedPath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'status_verifikasi' => DocumentVerificationStatus::Pending,
                        'catatan' => null,
                        'verified_at' => null,
                        'verified_by' => null,
                    ]);
                } else {
                    ApplicationDocument::create([
                        'application_id' => $application->id,
                        'requirement_id' => $reqId,
                        'nama_dokumen' => $docName,
                        'file_path' => $storedPath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'status_verifikasi' => DocumentVerificationStatus::Pending,
                    ]);
                }
            }

            // Kembalikan status ke diajukan (menunggu verifikasi ulang)
            $application->update([
                'status' => ApplicationStatus::Diajukan,
                'tgl_pengajuan' => now(),
            ]);

            DB::commit();

            return redirect()->route('asesi.applications.show', $application)
                ->with('success', 'Perbaikan berkas berhasil diajukan ulang! Menunggu verifikasi admin LSP.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memperbarui permohonan: '.$e->getMessage());
        }
    }
}
