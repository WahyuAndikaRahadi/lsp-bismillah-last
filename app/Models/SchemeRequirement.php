<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchemeRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheme_id',
        'nama_dokumen',
        'is_required',
        'keterangan',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }
}
