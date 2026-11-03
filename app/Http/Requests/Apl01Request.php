<?php

namespace App\Http\Requests;

use App\Models\Assessment;
use App\Models\AssessmentProof;
use App\Rules\ValidSignature;
use Illuminate\Foundation\Http\FormRequest;

class Apl01Request extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $assessment = $this->route('assessment');
        if ($assessment instanceof Assessment) {
            return $user->id === $assessment->asesi_id || $user->isAdmin();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            // Bagian 1: Data Pribadi
            'full_name' => 'nullable|string|max:255',
            'nik' => 'nullable|string|digits:16',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P,Laki-laki,Perempuan',
            'kebangsaan' => 'nullable|string|max:50',
            'alamat_rumah' => 'nullable|string',
            'kode_pos' => 'nullable|string|max:10',
            'no_telepon_rumah' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'pendidikan_terakhir' => 'nullable|string|max:50',

            // Bagian 2: Data Pekerjaan
            'status_pekerjaan' => 'nullable|string|max:50',
            'nama_institusi_perusahaan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:100',
            'alamat_kantor' => 'nullable|string',
            'kode_pos_kantor' => 'nullable|string|max:10',
            'no_telepon_kantor' => 'nullable|string|max:20',
            'email_kantor' => 'nullable|email|max:100',

            // Bagian 3: Bukti Persyaratan (Files)
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

            // Tanda Tangan Asesi
            'signature_data' => ['nullable', 'string', new ValidSignature],
            'assessor_id' => 'nullable|exists:users,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $assessment = $this->route('assessment');
            if (! $assessment instanceof Assessment) {
                return;
            }

            // Check for cross-user proof ID injection
            $proofIds = $this->input('proof_ids', []);
            if (! empty($proofIds)) {
                $foreignProofs = AssessmentProof::whereIn('id', $proofIds)
                    ->where('assessment_id', '!=', $assessment->id)
                    ->count();

                if ($foreignProofs > 0) {
                    $validator->errors()->add(
                        'proof_ids',
                        'Ditemukan referensi file bukti dari asesi lain. Cross-user file reference ditolak.'
                    );
                }
            }
        });
    }
}
