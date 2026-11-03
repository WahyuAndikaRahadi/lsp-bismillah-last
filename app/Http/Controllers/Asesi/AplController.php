<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentApl02Item;
use App\Models\AssessmentProof;
use App\Models\AuditEvent;
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
        $user = auth()->user();
        abort_if($assessment->asesi_id !== $user->id, 403);

        if ($assessment->schedule && $assessment->schedule->is_closed) {
            return back()->with('error', 'Jadwal asesmen sudah resmi ditutup.');
        }

        if ($assessment->apl_status === 'APPROVED') {
            return redirect()->route('asesi.assessment.ruang-uji', $assessment)
                ->with('info', 'Permohonan APL Anda sudah disetujui oleh Asesor.');
        }

        if ($assessment->apl_status === 'SUBMITTED') {
            return redirect()->route('asesi.dashboard')->with('info', 'Permohonan APL Anda sudah diajukan dan sedang menunggu verifikasi asesor.');
        }

        $request->validate([
            'proofs' => 'nullable|array',
            'proofs.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'proof_ids' => 'nullable|array',
            'proof_ids.*' => 'integer',
            'ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'pas_foto' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:3072',
            'ijazah_smk' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'surat_siswa_aktif' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'ijazah_sma_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'pengalaman_kerja' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'signature_data' => ['nullable', 'string', new ValidSignature],
        ], [
            'proofs.*.mimes' => 'Format file bukti harus berupa PDF, JPG, PNG, atau WEBP.',
            'proofs.*.max' => 'Ukuran file bukti maksimal 5 MB.',
            'ktp.mimes' => 'Format KTP harus berupa PDF, JPG, PNG, atau WEBP.',
            'pas_foto.mimes' => 'Format Pas Foto harus berupa JPG, PNG, atau WEBP.',
        ]);

        // Security gate: Reject any cross-user proof ID injection
        $proofIds = $request->input('proof_ids', []);
        if (! empty($proofIds)) {
            $foreignCount = AssessmentProof::whereIn('id', $proofIds)
                ->where('assessment_id', '!=', $assessment->id)
                ->count();

            if ($foreignCount > 0) {
                return back()->withErrors([
                    'proof_ids' => 'Ditemukan referensi file bukti dari asesi lain. Cross-user file reference ditolak.',
                ])->withInput();
            }
        }

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

        // Verify that required administrative documents and at least 1 alternative basic requirement exist
        $savedProofTypes = $assessment->proofs()->pluck('proof_type')->toArray();
        $hasKtp = in_array('KTP', $savedProofTypes);
        $hasPasFoto = in_array('PAS_FOTO', $savedProofTypes);
        $hasDasar = (bool) array_intersect(
            ['IJAZAH_SMK', 'SURAT_SISWA_AKTIF', 'IJAZAH_SMA_SERTIFIKAT', 'PENGALAMAN_KERJA'],
            $savedProofTypes
        );

        if (! $hasKtp || ! $hasPasFoto || ! $hasDasar) {
            $missing = [];
            if (! $hasKtp) {
                $missing['ktp'] = 'KTP wajib diunggah untuk verifikasi identitas.';
            }
            if (! $hasPasFoto) {
                $missing['pas_foto'] = 'Pas Foto resmi wajib diunggah.';
            }
            if (! $hasDasar) {
                $missing['proofs_dasar'] = 'Wajib mengunggah minimal satu dokumen bukti persyaratan dasar yang sah (Ijazah SMK / Surat Siswa Aktif / Ijazah SMA + Sertifikat Pelatihan / Pengalaman Kerja).';
            }

            return back()->withErrors($missing)->withInput();
        }

        // Validate self-assessment KUKs if provided
        $kukInputs = $request->input('kuks', $request->input('apl02', $request->input('kuk', [])));

        $profile = $user->profile;
        $signature = $request->input('signature_data')
            ?: $profile?->signature_data
            ?: $assessment->asesi_signature;

        DB::transaction(function () use ($assessment, $kukInputs, $request, $signature, $profile, $user) {
            foreach ($kukInputs as $kukId => $data) {
                $val = is_array($data) ? ($data['assessment_value'] ?? $data['value'] ?? 'K') : $data;
                $proofType = is_array($data)
                    ? ($data['selected_proof_type'] ?? $data['proof_type'] ?? null)
                    : $request->input("proof_type.{$kukId}");

                if (in_array(strtoupper((string) $val), ['K', 'BK'])) {
                    AssessmentApl02Item::updateOrCreate(
                        ['assessment_id' => $assessment->id, 'kuk_id' => $kukId],
                        [
                            'assessment_value' => strtoupper((string) $val),
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

            if ($profile && empty($profile->signature_data) && $signature) {
                $profile->update(['signature_data' => $signature]);
            }

            AuditEvent::record(
                action: 'apl.submitted',
                subject: $assessment,
                reason: "Formulir APL-01 dan APL-02 diajukan oleh asesi {$user->name}.",
                details: [
                    'assessment_id' => $assessment->id,
                    'proofs_count' => $assessment->proofs()->count(),
                    'apl02_items_count' => count($kukInputs),
                ],
                userId: $user->id
            );
        });

        return redirect()->route('asesi.dashboard')->with('success', 'Formulir APL-01 dan APL-02 berhasil dikirim. Menunggu verifikasi berkas oleh asesor.');
    }
}
