<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateReceiptEvent extends Model
{
    protected $table = 't_certificate_receipt_events';

    protected $fillable = [
        'certificate_id',
        'assessment_id',
        'recipient_id',
        'event_type',
        'occurred_at',
        'signature_data',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
