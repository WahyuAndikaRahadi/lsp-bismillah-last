<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kuk extends Model
{
    use HasFactory;

    protected $table = 'm_kuks';

    protected $fillable = [
        'unit_element_id',
        'code_number',
        'statement',
        'sequence',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn ($q) => $q->orderBy('sequence'));
    }

    public function unitElement(): BelongsTo
    {
        return $this->belongsTo(UnitElement::class);
    }

    public function apl02Items(): HasMany
    {
        return $this->hasMany(AssessmentApl02Item::class);
    }

    public function getDescriptionAttribute(): string
    {
        return $this->statement;
    }

    public function getElementTitleAttribute(): ?string
    {
        return $this->unitElement?->title;
    }

    public function getSchemeAttribute(): ?Scheme
    {
        return $this->unitElement?->competencyUnit?->scheme;
    }
}
