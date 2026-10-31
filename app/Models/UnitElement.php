<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitElement extends Model
{
    use HasFactory;

    protected $table = 'm_unit_elements';

    protected $fillable = [
        'competency_unit_id',
        'title',
        'sequence',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn ($q) => $q->orderBy('sequence'));
    }

    public function competencyUnit(): BelongsTo
    {
        return $this->belongsTo(CompetencyUnit::class);
    }

    public function kuks(): HasMany
    {
        return $this->hasMany(Kuk::class);
    }
}
