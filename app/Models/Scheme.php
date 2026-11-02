<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scheme extends Model
{
    use HasFactory;

    protected $table = 'm_schemes';

    protected $fillable = [
        'code',
        'name',
        'scheme_type',
        'version',
        'description',
        'passing_grade_theory',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'passing_grade_theory' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function competencyUnits(): HasMany
    {
        return $this->hasMany(CompetencyUnit::class);
    }

    public function units(): HasMany
    {
        return $this->competencyUnits();
    }

    public function getUnitsCountAttribute(): int
    {
        return $this->attributes['competency_units_count']
            ?? $this->attributes['units_count']
            ?? $this->competencyUnits()->count();
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
