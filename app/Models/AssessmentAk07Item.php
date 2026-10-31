<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAk07Item extends Model
{
    use HasFactory;

    protected $table = 't_assessment_ak07_items';

    protected $fillable = [
        'assessment_id',
        'adjustment_category',
        'adjustment_detail',
        'assessor_id',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }
}
