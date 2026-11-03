<?php

namespace App\Http\Requests;

use App\Models\AssessmentPlan;
use Illuminate\Foundation\Http\FormRequest;

class Mapa02Request extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAsesor()) {
            $plan = $this->route('plan');
            if ($plan instanceof AssessmentPlan) {
                return $plan->isEditableBy($user);
            }

            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => 'nullable|exists:t_schedules,id',
            'lock_version' => 'nullable|integer',
            'mapa02_data' => 'required|array|min:1',
            'mapa02_data.*.group_name' => 'required|string',
            'mapa02_data.*.instruments' => 'required|array|min:1',
            'mapa02_data.*.instruments.*.code' => 'required|string',
            'mapa02_data.*.instruments.*.name' => 'required|string',
            'mapa02_data.*.instruments.*.potensi' => 'nullable|array',
            'mapa02_data.*.instruments.*.is_active' => 'nullable|boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $blockedPrefixes = ['IA.04', 'IA-04', 'IA.06', 'IA-06', 'IA.07', 'IA-07', 'IA.08', 'IA-08', 'IA.09', 'IA-09', 'IA.10', 'IA-10', 'IA.11', 'IA-11'];

            $data = $this->input('mapa02_data', []);
            foreach ($data as $group) {
                foreach ($group['instruments'] ?? [] as $inst) {
                    $isActive = ! empty($inst['is_active']);
                    $code = strtoupper($inst['code'] ?? '');

                    if ($isActive) {
                        foreach ($blockedPrefixes as $prefix) {
                            if (str_contains($code, strtoupper($prefix))) {
                                $validator->errors()->add(
                                    'mapa02_data',
                                    "Instrumen {$inst['code']} ({$inst['name']}) belum memiliki dokumen sumber resmi (missing-source) dan tidak dapat diaktifkan."
                                );
                                break;
                            }
                        }
                    }
                }
            }
        });
    }
}
