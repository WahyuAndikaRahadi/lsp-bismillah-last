<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyIdentityMap extends Model
{
    protected $table = 't_legacy_identity_maps';

    protected $fillable = [
        'source_table',
        'legacy_id',
        'target_table',
        'target_id',
        'checksum',
        'status',
        'reason',
        'reconciler_id',
    ];
}
