<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpartialityCommitment extends Model
{
    use HasFactory;

    protected $table = 't_impartiality_commitments';

    protected $fillable = [
        'user_id',
        'commitment_type',
        'statement_text',
        'signed_at',
        'signature_data',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
