<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentProof extends Model
{
    use HasFactory;

    protected $table = 't_assessment_proofs';

    protected $fillable = [
        'assessment_id',
        'proof_type',
        'proof_category',
        'file_path',
        'original_filename',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
