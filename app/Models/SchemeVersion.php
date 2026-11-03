<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchemeVersion extends Model
{
    protected $table = 'm_scheme_versions';

    protected $fillable = [
        'scheme_id',
        'version',
        'code',
        'title',
        'status',
        'source_hash',
        'notes',
    ];

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }
}
