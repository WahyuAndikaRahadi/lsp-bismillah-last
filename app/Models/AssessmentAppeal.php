<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAppeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_banding',
        'application_id',
        'asesi_id',
        'alasan_banding',
        'bukti_pendukung',
        'status',
        'keputusan_komite',
        'komite_id',
        'tgl_keputusan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_keputusan' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(AssessmentApplication::class, 'application_id');
    }

    public function asesi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesi_id');
    }

    public function komite(): BelongsTo
    {
        return $this->belongsTo(User::class, 'komite_id');
    }

    public static function generateNomorBanding(): string
    {
        $year = date('Y');
        $prefix = "BDG-{$year}-";
        $last = self::where('nomor_banding', 'LIKE', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->nomor_banding);
            $seq = intval(end($parts)) + 1;
        }

        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }
}
