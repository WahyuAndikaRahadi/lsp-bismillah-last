<?php

namespace App\Http\Requests;

use App\Models\Assessment;
use App\Models\Kuk;
use App\Rules\ValidSignature;
use Illuminate\Foundation\Http\FormRequest;

class PmoResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $assessment = $this->route('assessment');
        if ($assessment instanceof Assessment) {
            return $user->isAdmin()
                || $assessment->schedule?->assessors()->where('users.id', $user->id)->exists();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'questions' => 'required|array|min:1',
            'questions.*.number' => 'nullable',
            'questions.*.question' => 'required|string',
            'questions.*.expected_answer' => 'nullable|string',
            'questions.*.tanggapan' => 'nullable|string',
            'questions.*.is_fulfilled_by_observation' => 'nullable|boolean',
            'questions.*.observation_reference' => 'nullable|string',
            'questions.*.observation_assessment_id' => 'nullable|integer',
            'questions.*.pencapaian' => 'required|in:YA,TIDAK,ya,tidak,Y,T,K,BK,k,bk',
            'supplementary_questions' => 'nullable|array',
            'supplementary_questions.*.question' => 'required|string',
            'supplementary_questions.*.tanggapan' => 'nullable|string',
            'supplementary_questions.*.pencapaian' => 'required|in:YA,TIDAK,ya,tidak,Y,T,K,BK,k,bk',
            'supplementary_questions.*.reason' => 'required|string',
            'pmo_variant' => 'nullable|string',
            'signature_data' => ['nullable', 'string', new ValidSignature],
            'assessor_signature' => ['nullable', 'string', new ValidSignature],
            'notes' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'lock_version' => 'nullable|integer',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $assessment = $this->route('assessment');
            if (! ($assessment instanceof Assessment)) {
                return;
            }

            $scheme = $assessment->schedule?->scheme ?? $assessment->scheme;

            // 1. Conflicting PMO in Scheme 3 must not be auto-selected
            if ($scheme && $scheme->code === 'SKM-03-2026') {
                $variant = $this->input('pmo_variant');
                if (empty($variant)) {
                    $validator->errors()->add(
                        'pmo_variant',
                        'Skema S3 memiliki varian PMO yang berpotensi konflik; varian approved wajib dipilih secara eksplisit dan tidak dapat dipilih otomatis.'
                    );
                }
            }

            // 2. Questions validation
            $questions = $this->input('questions', []);
            foreach ($questions as $idx => $q) {
                $pencapaian = strtoupper((string) ($q['pencapaian'] ?? ''));
                $tanggapan = trim((string) ($q['tanggapan'] ?? ''));
                $fulfilledByObs = ! empty($q['is_fulfilled_by_observation']);
                $obsRef = trim((string) ($q['observation_reference'] ?? ''));
                $foreignAssessmentId = $q['observation_assessment_id'] ?? null;

                // Empty tanggapan cannot be marked YA / K without observation proof
                if (in_array($pencapaian, ['YA', 'Y', 'K']) && empty($tanggapan) && ! $fulfilledByObs) {
                    $validator->errors()->add(
                        "questions.{$idx}.tanggapan",
                        'Tanggapan kosong tidak dapat dinyatakan tercapai (YA) tanpa bukti konfirmasi observasi demonstrasi.'
                    );
                }

                // If marked fulfilled by observation, observation_reference must be non-empty
                if ($fulfilledByObs && empty($obsRef)) {
                    $validator->errors()->add(
                        "questions.{$idx}.observation_reference",
                        'Rujukan bukti observasi wajib diisi apabila pertanyaan dipenuhi melalui unjuk kerja praktik.'
                    );
                }

                // Cross-assessment reference check: must belong to the same assessment
                if ($foreignAssessmentId && (int) $foreignAssessmentId !== (int) $assessment->id) {
                    $validator->errors()->add(
                        "questions.{$idx}.observation_reference",
                        'Rujukan bukti observasi harus berasal dari asesmen yang sama. Cross-assessment reference ditolak.'
                    );
                }
            }
        });
    }
}
