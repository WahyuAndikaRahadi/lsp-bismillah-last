<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConflictDeclaration extends Model
{
    use HasFactory;

    protected $table = 't_conflict_declarations';

    protected $fillable = [
        'declarant_id',
        'schedule_id',
        'assessment_id',
        'related_asesi_id',
        'has_conflict',
        'conflict_nature',
        'status',
        'declared_at',
    ];

    protected function casts(): array
    {
        return [
            'has_conflict' => 'boolean',
            'declared_at' => 'datetime',
        ];
    }

    public function declarant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'declarant_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function relatedAsesi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'related_asesi_id');
    }

    /**
     * Memeriksa apakah asesor memiliki benturan kepentingan terbuka yang belum terselesaikan terhadap asesmen ini.
     */
    public static function hasUnresolvedConflict(int $assessorId, Assessment $assessment): bool
    {
        return static::where('declarant_id', $assessorId)
            ->where('has_conflict', true)
            ->where('status', 'OPEN')
            ->where(function ($query) use ($assessment) {
                $query->where('assessment_id', $assessment->id)
                    ->orWhere('related_asesi_id', $assessment->asesi_id);
            })
            ->exists();
    }
}
