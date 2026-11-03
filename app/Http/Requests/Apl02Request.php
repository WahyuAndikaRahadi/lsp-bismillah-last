<?php

namespace App\Http\Requests;

use App\Models\Assessment;
use App\Models\Kuk;
use App\Rules\ValidSignature;
use Illuminate\Foundation\Http\FormRequest;

class Apl02Request extends FormRequest
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
            'kuks' => 'required|array|min:1',
            'kuks.*.assessment_value' => 'required|in:K,BK',
            'kuks.*.selected_proof_type' => 'nullable|string',
            'statement_integrity' => 'accepted',
            'signature_data' => ['nullable', 'string', new ValidSignature],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $assessment = $this->route('assessment');
            if (! $assessment instanceof Assessment) {
                return;
            }

            // Verify that all submitted KUKs belong to this assessment's scheme
            $scheme = $assessment->schedule?->scheme ?? $assessment->scheme;
            if ($scheme) {
                $submittedKukIds = array_keys($this->input('kuks', []));
                $schemeKukIds = Kuk::whereHas('element.competencyUnit', function ($q) use ($scheme) {
                    $q->where('scheme_id', $scheme->id);
                })->pluck('id')->map(fn ($id) => (string) $id)->toArray();

                foreach ($submittedKukIds as $kukId) {
                    if (! empty($schemeKukIds) && ! in_array((string) $kukId, $schemeKukIds, true)) {
                        $validator->errors()->add(
                            "kuks.{$kukId}",
                            "KUK ID #{$kukId} bukan bagian dari unit kompetensi skema asesmen ini. Invalid foreign KUK ditolak."
                        );
                    }
                }
            }
        });
    }
}
