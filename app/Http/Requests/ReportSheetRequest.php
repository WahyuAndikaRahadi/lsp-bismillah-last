<?php

namespace App\Http\Requests;

use App\Models\Assessment;
use App\Rules\ValidSignature;
use Illuminate\Foundation\Http\FormRequest;

class ReportSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $assessment = $this->route('assessment');
        if ($assessment instanceof Assessment) {
            // Asesi can record measurements; Assessor can evaluate
            return $user->isAdmin()
                || $user->id === $assessment->asesi_id
                || $assessment->schedule?->assessors()->where('users.id', $user->id)->exists();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.item' => 'nullable|string',
            'items.*.standard' => 'nullable|string',
            'items.*.measurement_value' => 'nullable', // Allows 0, numeric, negative, or string; 0 is valid
            'items.*.unit' => 'nullable|string',
            'items.*.condition' => 'nullable|string',
            'items.*.result' => 'nullable|in:sesuai,tidak,SESUAI,TIDAK,K,BK,k,bk',
            'signature_data' => ['nullable', 'string', new ValidSignature],
            'notes' => 'nullable|string',
            'lock_version' => 'nullable|integer',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $assessment = $this->route('assessment');

            // If an Asesi is submitting, they cannot forge the Assessor evaluation column
            if ($user && $assessment && $user->id === $assessment->asesi_id && ! $user->isAdmin()) {
                foreach ($this->input('items', []) as $idx => $item) {
                    if (isset($item['result']) && trim((string) $item['result']) !== '') {
                        $validator->errors()->add(
                            "items.{$idx}.result",
                            'Kolom penilaian kesesuaian merupakan kewenangan asesor. Unauthorized role column ditolak.'
                        );
                    }
                }
            }
        });
    }
}
