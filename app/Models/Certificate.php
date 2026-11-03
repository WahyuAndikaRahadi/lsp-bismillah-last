<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    protected $table = 't_certificates';

    protected $fillable = [
        'assessment_id',
        'decision_id',
        'certificate_number',
        'blanko_number',
        'issue_date',
        'valid_from',
        'valid_until',
        'private_file_path',
        'file_hash',
        'uploader_id',
        'status',
        'revision',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'revision' => 'integer',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(CertificationDecision::class, 'decision_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function receiptEvents(): HasMany
    {
        return $this->hasMany(CertificateReceiptEvent::class, 'certificate_id');
    }
}
