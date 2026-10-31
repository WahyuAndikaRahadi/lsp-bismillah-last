<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentIaResponse;
use App\Models\QuestionBank;

class AutoGradeService
{
    public function grade(Assessment $assessment, array $answers): AssessmentIaResponse
    {
        $scheme = $assessment->schedule->scheme;
        $questions = QuestionBank::where('scheme_id', $scheme->id)->get();
        $total = $questions->count();
        $correct = 0;

        foreach ($questions as $q) {
            if (isset($answers[$q->id]) && strtoupper($answers[$q->id]) === $q->correct_answer) {
                $correct++;
            }
        }

        $score = $total > 0 ? round(($correct / $total) * 100, 2) : 0;
        $asesiSig = $assessment->asesi_signature ?? $assessment->asesi?->profile?->signature_data;

        return AssessmentIaResponse::updateOrCreate(
            ['assessment_id' => $assessment->id, 'instrument_code' => 'IA05'],
            [
                'data_json' => $answers,
                'score' => $score,
                'asesi_signature' => $asesiSig,
                'asesi_signed_at' => now(),
            ]
        );
    }
}
