<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use App\Rules\ValidSignature;
use Illuminate\Foundation\Http\FormRequest;

class AssessmentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $schedule = $this->route('schedule');
        if ($schedule instanceof Schedule) {
            return $user->isAdmin()
                || $schedule->assessors()->where('users.id', $user->id)->exists();
        }

        return true;
    }

    protected function getReportType(): string
    {
        if ($this->has('report_type')) {
            return strtoupper((string) $this->input('report_type'));
        }
        if ($this->route('type')) {
            return strtoupper((string) $this->route('type'));
        }
        if (str_contains($this->path(), 'ak06') || str_contains($this->route()?->getName() ?? '', 'ak06')) {
            return 'AK06';
        }

        return 'AK05';
    }

    public function rules(): array
    {
        $type = $this->getReportType();

        if ($type === 'AK06') {
            return [
                'procedure_matrix' => 'required|array|min:4',
                'procedure_matrix.*.name' => 'required|string',
                'procedure_matrix.*.valid' => 'required|string',
                'procedure_matrix.*.reliabel' => 'required|string',
                'procedure_matrix.*.fleksibel' => 'required|string',
                'procedure_matrix.*.adil' => 'required|string',
                'procedure_matrix.*.notes' => 'nullable|string',
                'dimensions_review' => 'required|array|min:5',
                'dimensions_review.task_skills' => 'required|string',
                'dimensions_review.task_management_skills' => 'required|string',
                'dimensions_review.contingency_management_skills' => 'required|string',
                'dimensions_review.job_role_environment_skills' => 'required|string',
                'dimensions_review.transfer_skills' => 'required|string',
                'evidence_references' => 'nullable|string',
                'comments' => 'nullable|string',
                'signature_data' => ['nullable', 'string', new ValidSignature],
                'lock_version' => 'nullable|integer',
            ];
        }

        return [
            'aspects_positive' => 'required|string|min:5',
            'aspects_negative' => 'required|string|min:5',
            'rejections_record' => 'nullable|string',
            'improvements' => 'required|string|min:5',
            'notes' => 'nullable|string',
            'signature_data' => ['nullable', 'string', new ValidSignature],
            'lock_version' => 'nullable|integer',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->getReportType();

            if ($type === 'AK06') {
                $matrix = $this->input('procedure_matrix', []);
                foreach ($matrix as $idx => $row) {
                    $keys = ['valid', 'reliabel', 'fleksibel', 'adil'];
                    foreach ($keys as $k) {
                        $val = strtoupper(trim((string) ($row[$k] ?? '')));
                        if (in_array($val, ['NA', 'N/A', 'TIDAK_APLIKATIF'], true)) {
                            if (empty(trim((string) ($row['notes'] ?? '')))) {
                                $validator->errors()->add(
                                    "procedure_matrix.{$idx}.notes",
                                    "Alasan wajib dicantumkan pada kolom catatan untuk prosedur {$row['name']} jika penilaian bernilai N/A."
                                );
                            }
                        }
                    }
                }
            }
        });
    }
}
