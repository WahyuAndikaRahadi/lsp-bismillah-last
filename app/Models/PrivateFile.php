<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PrivateFile extends Model
{
    protected $table = 't_private_files';

    protected $fillable = [
        'disk',
        'path',
        'original_name',
        'mime_type',
        'file_size',
        'file_hash',
        'owner_type',
        'owner_id',
        'uploaded_by',
        'status',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
