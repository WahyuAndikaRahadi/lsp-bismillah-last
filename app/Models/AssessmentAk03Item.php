<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAk03Item extends Model
{
    use HasFactory;

    protected $table = 't_assessment_ak03_items';

    protected $fillable = [
        'assessment_id',
        'ratings_json',
        'general_notes',
    ];

    protected function casts(): array
    {
        return [
            'ratings_json' => 'array',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
