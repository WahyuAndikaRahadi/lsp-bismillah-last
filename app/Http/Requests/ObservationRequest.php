<?php

namespace App\Http\Requests;

use App\Models\Assessment;
use App\Models\Kuk;
use App\Rules\ValidSignature;
use Illuminate\Foundation\Http\FormRequest;

class ObservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $assessment = $this->route('assessment');
        if ($assessment instanceof Assessment) {
            return $user->isAdmin() || $assessment->schedule?->assessors()->where('users.id', $user->id)->exists();
        }

        return $user->isAdmin() || $user->isAsesor();
    }

    public function rules(): array
    {
        return [
            'kuks' => 'required|array|min:1',
            'kuks.*' => 'nullable|in:K,BK,k,bk',
            'general_notes' => 'nullable|string',
            'notes' => 'nullable|string',
            'signature_data' => ['nullable', 'string', new ValidSignature],
            'assessor_signature' => ['nullable', 'string', new ValidSignature],
            'lock_version' => 'nullable|integer',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $assessment = $this->route('assessment');
            if (! $assessment instanceof Assessment) {
                return;
            }

            $scheme = $assessment->schedule?->scheme ?? $assessment->scheme;
            if ($scheme) {
                $submittedKukIds = array_keys($this->input('kuks', []));
                $validKukIds = Kuk::whereHas('element.competencyUnit', function ($q) use ($scheme) {
                    $q->where('scheme_id', $scheme->id);
                })->pluck('id')->map(fn ($id) => (string) $id)->toArray();

                foreach ($submittedKukIds as $kId) {
                    if (! empty($validKukIds) && ! in_array((string) $kId, $validKukIds, true)) {
                        $validator->errors()->add(
                            "kuks.{$kId}",
                            "KUK ID #{$kId} tidak dikenal atau bukan bagian dari skema asesmen ini. Foreign KUK ditolak."
                        );
                    }
                }
            }
        });
    }
}
