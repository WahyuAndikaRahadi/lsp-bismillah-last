<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkKeyCriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'element_id',
        'nomor_kuk',
        'pernyataan',
        'pertanyaan_observasi',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(CompetencyElement::class, 'element_id');
    }
}
