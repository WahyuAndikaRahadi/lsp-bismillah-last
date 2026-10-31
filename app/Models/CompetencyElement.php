<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetencyElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'nomor_elemen',
        'nama_elemen',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'nomor_elemen' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(CompetencyUnit::class, 'unit_id');
    }

    public function criterias(): HasMany
    {
        return $this->hasMany(WorkKeyCriteria::class, 'element_id')->orderBy('urutan');
    }
}
