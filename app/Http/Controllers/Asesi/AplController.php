<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentApl02Item;
use App\Models\AssessmentProof;
use App\Rules\ValidSignature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AplController extends Controller
{
    public function show(Assessment $assessment): View
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        $assessment->load([
            'schedule.scheme.competencyUnits.elements.kuks',
            'schedule.assessors',
            'proofs',
            'apl02Items',
        ]);

        $profile = auth()->user()->profile;

        return view('asesi.apl', compact('assessment', 'profile'));
    }

    public function store(Assessment $assessment, Request $request): RedirectResponse
    {
        abort_if($assessment->asesi_id !== auth()->id(), 403);

        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }

        if ($assessment->apl_status === 'APPROVED') {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('info', 'Permohonan APL Anda sudah disetujui oleh Asesor.');
        }

        if ($assessment->apl_status === 'SUBMITTED') {
            return back()->with('info', 'Permohonan APL Anda sudah diajukan dan sedang menunggu verifikasi asesor.');
        }

        $scheduleAssessors = $assessment->schedule->assessors ?? collect();
        if ($scheduleAssessors->isNotEmpty() && empty($assessment->assessor_id)) {
            $request->validate([
                'assessor_id' => ['required', 'exists:users,id'],
            ], [
                'assessor_id.required' => 'Pilih salah satu asesor penguji yang ditugaskan pada jadwal ini.',
            ]);
        } elseif ($request->filled('assessor_id')) {
            $request->validate([
                'assessor_id' => ['exists:users,id'],
            ]);
        }

        $request->validate([
            'proofs' => 'nullable|array',
            'proofs.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'pas_foto' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:3072',
            'ijazah_smk' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'surat_siswa_aktif' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'ijazah_sma_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'pengalaman_kerja' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'proofs.*.mimes' => 'Format file bukti harus berupa PDF, JPG, PNG, atau WEBP.',
            'proofs.*.max' => 'Ukuran file bukti maksimal 5 MB.',
            'ktp.mimes' => 'Format KTP harus berupa PDF, JPG, PNG, atau WEBP.',
            'pas_foto.mimes' => 'Format Pas Foto harus berupa JPG, PNG, atau WEBP.',
        ]);

        $proofCategories = [
            'IJAZAH_SMK' => 'PERSYARATAN_DASAR',
            'SURAT_SISWA_AKTIF' => 'PERSYARATAN_DASAR',
            'IJAZAH_SMA_SERTIFIKAT' => 'PERSYARATAN_DASAR',
            'PENGALAMAN_KERJA' => 'PERSYARATAN_DASAR',
            'KTP' => 'ADMINISTRATIF',
            'PAS_FOTO' => 'ADMINISTRATIF',
        ];

        DB::transaction(function () use ($assessment, $request, $proofCategories) {
            foreach ($proofCategories as $type => $category) {
                $file = $request->file("proofs.{$type}")
                    ?? $request->file(strtolower($type))
                    ?? $request->file("proof_{$type}")
                    ?? $request->file('proof_'.strtolower($type));

                if ($file) {
                    $path = $file->store('proofs', 'local');
                    AssessmentProof::updateOrCreate(
                        ['assessment_id' => $assessment->id, 'proof_type' => $type],
                        [
                            'proof_category' => $category,
                            'file_path' => $path,
                            'original_filename' => $file->getClientOriginalName(),
                        ]
                    );
                }
            }
        });

        $savedProofTypes = $assessment->proofs()->pluck('proof_type')->toArray();
        $hasKtp = in_array('KTP', $savedProofTypes);
        $hasPasFoto = in_array('PAS_FOTO', $savedProofTypes);
        $hasDasar = (bool) array_intersect(
            ['IJAZAH_SMK', 'SURAT_SISWA_AKTIF', 'IJAZAH_SMA_SERTIFIKAT', 'PENGALAMAN_KERJA'],
            $savedProofTypes
        );

        if (! $hasKtp || ! $hasPasFoto || ! $hasDasar) {
            return back()->withErrors([
                'proofs' => 'Dokumen belum lengkap. KTP dan Pas Foto wajib diunggah, serta minimal 1 bukti persyaratan dasar.',
            ]);
        }

        $request->validate([
            'statement_integrity' => 'accepted',
        ], [
            'statement_integrity.accepted' => 'Pernyataan kejujuran dan integritas asesmen mandiri (FR.APL.02) wajib dicentang.',
        ]);

        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->first();

        // Validasi kelengkapan data diri pemohon FR.APL.01
        if ($profile && $request->has('signature_data')) {
            $missingApl01Fields = [];
            if (empty($user->full_name)) {
                $missingApl01Fields[] = 'Nama Lengkap';
            }
            if (empty($user->phone)) {
                $missingApl01Fields[] = 'Nomor HP';
            }
            if (empty($profile->nik)) {
                $missingApl01Fields[] = 'NIK';
            }
            if (empty($profile->tempat_lahir)) {
                $missingApl01Fields[] = 'Tempat Lahir';
            }
            if (empty($profile->tanggal_lahir)) {
                $missingApl01Fields[] = 'Tanggal Lahir';
            }
            if (empty($profile->jenis_kelamin)) {
                $missingApl01Fields[] = 'Jenis Kelamin';
            }
            if (empty($profile->alamat_rumah)) {
                $missingApl01Fields[] = 'Alamat Rumah';
            }
            if (empty($profile->pendidikan_terakhir)) {
                $missingApl01Fields[] = 'Pendidikan Terakhir';
            }
            if (empty($profile->jabatan)) {
                $missingApl01Fields[] = 'Jabatan / Posisi Pekerjaan';
            }

            if (! empty($missingApl01Fields)) {
                return back()->withErrors([
                    'profile_incomplete' => 'Data FR.APL.01 belum lengkap: '.implode(', ', $missingApl01Fields).'. Silakan lengkapi profil Anda terlebih dahulu.',
                ])->withInput();
            }
        }

        // Validasi tanda tangan asesi (Wajib diisi di pad atau dari master tanda tangan profil)
        $signature = $request->input('signature_data')
            ?: $profile?->signature_data
            ?: $assessment->asesi_signature;

        if ($request->has('signature_data') && empty($signature)) {
            return back()->withErrors([
                'signature_data' => 'Tanda tangan pemohon (asesi) wajib diisi untuk mengajukan permohonan sertifikasi FR.APL.01.',
            ])->withInput();
        }

        if ($request->filled('signature_data')) {
            $request->validate([
                'signature_data' => ['required', 'string', new ValidSignature],
            ], [
                'signature_data.required' => 'Tanda tangan pemohon (asesi) wajib diisi.',
            ]);
        }

        $kukInputs = $request->input('kuks', $request->input('apl02', []));

        DB::transaction(function () use ($assessment, $kukInputs, $request, $signature, $profile) {
            foreach ($kukInputs as $kukId => $data) {
                $val = is_array($data) ? ($data['assessment_value'] ?? $data['value'] ?? 'K') : $data;
                $proofType = is_array($data)
                    ? ($data['selected_proof_type'] ?? $data['proof_type'] ?? null)
                    : $request->input("proof_type.{$kukId}");

                if (in_array($val, ['K', 'BK'])) {
                    AssessmentApl02Item::updateOrCreate(
                        ['assessment_id' => $assessment->id, 'kuk_id' => $kukId],
                        [
                            'assessment_value' => $val,
                            'selected_proof_type' => $proofType,
                        ]
                    );
                }
            }

            $updateData = [
                'apl_status' => 'SUBMITTED',
                'last_active_step' => 'apl',
                'asesi_signature' => $signature,
                'asesi_signed_at' => now(),
            ];

            if ($request->filled('assessor_id')) {
                $updateData['assessor_id'] = $request->assessor_id;
            }

            $assessment->update($updateData);

            if ($profile && empty($profile->signature_data)) {
                $profile->update(['signature_data' => $signature]);
            }
        });

        return redirect()->route('asesi.assessment.apl', $assessment)->with('success', 'Menunggu verifikasi asesor');
    }
}
