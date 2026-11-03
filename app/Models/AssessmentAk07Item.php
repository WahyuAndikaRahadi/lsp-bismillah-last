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
        'category_number',
        'decision',
        'selected_options',
        'adjustment_detail',
        'timing',
        'is_agreed',
        'agreed_benchmark',
        'agreed_method',
        'agreed_instrument',
        'asesi_signed_at',
        'asesor_signed_at',
        'assessor_id',
    ];

    protected $casts = [
        'category_number' => 'integer',
        'selected_options' => 'array',
        'is_agreed' => 'boolean',
        'asesi_signed_at' => 'datetime',
        'asesor_signed_at' => 'datetime',
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
