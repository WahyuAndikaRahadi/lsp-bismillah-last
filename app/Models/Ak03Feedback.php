<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ak03Feedback extends Model
{
    use HasFactory;

    protected $table = 'ak03_feedbacks';

    protected $fillable = [
        'application_id',
        'q1_penjelasan',
        'q2_kesepakatan',
        'q3_hak_banding',
        'q4_adil_objektif',
        'q5_umpan_balik',
        'catatan_rekomendasi',
        'ttd_asesi',
        'tgl_ttd_asesi',
    ];

    protected function casts(): array
    {
        return [
            'q1_penjelasan' => 'boolean',
            'q2_kesepakatan' => 'boolean',
            'q3_hak_banding' => 'boolean',
            'q4_adil_objektif' => 'boolean',
            'q5_umpan_balik' => 'boolean',
            'tgl_ttd_asesi' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(AssessmentApplication::class, 'application_id');
    }
}
