<?php

namespace App\Http\Requests;

use App\Models\AssessmentPlan;
use Illuminate\Foundation\Http\FormRequest;

class Mapa01Request extends FormRequest
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
            'schedule_id' => 'required|exists:t_schedules,id',
            'assessment_id' => 'nullable|exists:t_assessments,id',
            'lock_version' => 'nullable|integer',
            'status' => 'nullable|string|in:draft,submitted,validated,locked',

            // Section 1: Pendekatan Asesmen
            'approach_data' => 'nullable|array',
            'approach_data.asesi_type' => 'nullable|string',
            'approach_data.purpose' => 'nullable|string',
            'approach_data.context_environment' => 'nullable|string',
            'approach_data.context_opportunities' => 'nullable|string',
            'approach_data.context_relationship' => 'nullable|string',
            'approach_data.context_conducted_by' => 'nullable|string',
            'approach_data.benchmarks' => 'nullable|array',

            // Section 2: Peta Kelompok Pekerjaan & Unit Kompetensi
            'work_groups' => 'required|array|min:1',
            'work_groups.*.name' => 'required|string',
            'work_groups.*.units' => 'required|array|min:1',
            'work_groups.*.units.*.code' => 'required|string',
            'work_groups.*.units.*.title' => 'required|string',
            'work_groups.*.units.*.evidence_text' => 'nullable|string',
            'work_groups.*.units.*.evidence_types' => 'nullable|array', // ['L', 'TL', 'T']
            'work_groups.*.units.*.methods' => 'nullable|array', // ['CL', 'DIT', 'DPL', 'DPT', 'VPK', 'CVP', 'CRP', 'PW']

            // Section 3: Modifikasi dan Kontekstualisasi
            'modifications' => 'nullable|array',
            'modifications.has_special_characteristics' => 'nullable|in:ada,tidak_ada',
            'modifications.special_characteristics_notes' => 'required_if:modifications.has_special_characteristics,ada|nullable|string',

            'modifications.has_workplace_context' => 'nullable|in:ada,tidak_ada',
            'modifications.workplace_context_notes' => 'required_if:modifications.has_workplace_context,ada|nullable|string',

            'modifications.has_training_package_suggestions' => 'nullable|in:ada,tidak_ada',
            'modifications.training_package_suggestions_notes' => 'required_if:modifications.has_training_package_suggestions,ada|nullable|string',

            'modifications.has_instrument_adjustments' => 'nullable|in:ada,tidak_ada',
            'modifications.instrument_adjustments_notes' => 'required_if:modifications.has_instrument_adjustments,ada|nullable|string',

            'modifications.has_integrated_assessment_opportunities' => 'nullable|in:ada,tidak_ada',
            'modifications.integrated_assessment_notes' => 'required_if:modifications.has_integrated_assessment_opportunities,ada|nullable|string',

            // Section 4: Konfirmasi & Pengesahan
            'confirmation_data' => 'nullable|array',
            'confirmation_data.relevant_persons' => 'nullable|array',
            'confirmation_data.compilers' => 'nullable|array',
            'confirmation_data.validators' => 'nullable|array',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $scheduleId = $this->input('schedule_id');
            $schedule = \App\Models\Schedule::find($scheduleId);
            if (! $schedule) {
                return;
            }

            $scheme = $schedule->scheme;
            if (! $scheme) {
                return;
            }

            $service = app(\App\Services\SchemeInstrumentService::class);
            $validUnits = collect($service->getUnitsForScheme($scheme))->pluck('code')->toArray();

            $submittedGroups = $this->input('work_groups', []);
            foreach ($submittedGroups as $group) {
                foreach ($group['units'] ?? [] as $unit) {
                    $unitCode = $unit['code'] ?? null;
                    if ($unitCode && ! empty($validUnits) && ! in_array($unitCode, $validUnits, true)) {
                        $validator->errors()->add(
                            'work_groups',
                            "Unit kompetensi {$unitCode} tidak terdaftar pada skema {$scheme->name} ({$scheme->code}). Unit asing ditolak."
                        );
                    }
                }
            }
        });
    }
}
