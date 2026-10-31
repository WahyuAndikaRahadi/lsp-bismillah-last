<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CompetencyUnit extends Model
{
    use HasFactory;

    protected $table = 'm_competency_units';

    protected $fillable = [
        'scheme_id',
        'code',
        'title',
        'standard_reference',
        'sequence',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn ($q) => $q->orderBy('sequence'));
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(UnitElement::class);
    }

    public function unitElements(): HasMany
    {
        return $this->elements();
    }

    public function kuks(): HasManyThrough
    {
        return $this->hasManyThrough(Kuk::class, UnitElement::class);
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }
}
