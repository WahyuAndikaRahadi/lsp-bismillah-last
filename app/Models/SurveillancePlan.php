<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SurveillancePlan extends Model
{
    use HasFactory;

    protected $table = 't_surveillance_plans';

    protected $fillable = [
        'certificate_id',
        'title',
        'surveillance_year',
        'due_date',
        'required_evidence_description',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'surveillance_year' => 'integer',
        ];
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(SurveillanceReport::class, 'surveillance_plan_id');
    }

    public function latestReport(): HasOne
    {
        return $this->hasOne(SurveillanceReport::class, 'surveillance_plan_id')->latestOfMany();
    }
}
