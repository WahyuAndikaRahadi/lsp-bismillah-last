<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CompetencyUnit extends Model
{
    use HasFactory;

    protected $table = 'm_competency_units';

    protected $fillable = [
        'scheme_id',
        'code',
        'title',
        'standard_reference',
        'sequence',
        'kode_unit',
        'judul_unit',
        'jenis_standar',
        'urutan',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn ($q) => $q->orderBy('sequence'));

        static::saving(function ($model) {
            if (empty($model->code) && ! empty($model->attributes['kode_unit'])) {
                $model->code = $model->attributes['kode_unit'];
            } elseif (! empty($model->code) && empty($model->attributes['kode_unit'])) {
                $model->attributes['kode_unit'] = $model->code;
            }

            if (empty($model->title) && ! empty($model->attributes['judul_unit'])) {
                $model->title = $model->attributes['judul_unit'];
            } elseif (! empty($model->title) && empty($model->attributes['judul_unit'])) {
                $model->attributes['judul_unit'] = $model->title;
            }

            if (empty($model->sequence) && ! empty($model->attributes['urutan'])) {
                $model->sequence = $model->attributes['urutan'];
            } elseif (! empty($model->sequence) && empty($model->attributes['urutan'])) {
                $model->attributes['urutan'] = $model->sequence;
            }

            if (empty($model->standard_reference) && ! empty($model->attributes['jenis_standar'])) {
                $model->standard_reference = $model->attributes['jenis_standar'];
            }
        });

        static::created(function ($model) {
            if (\Illuminate\Support\Facades\Schema::hasTable('competency_units')) {
                $schemeId = $model->scheme_id;
                $schemeExists = \Illuminate\Support\Facades\DB::table('certification_schemes')->where('id', $schemeId)->exists();
                if (! $schemeExists) {
                    $firstScheme = \Illuminate\Support\Facades\DB::table('certification_schemes')->first();
                    $schemeId = $firstScheme?->id;
                }
                if ($schemeId) {
                    \Illuminate\Support\Facades\DB::table('competency_units')->insertOrIgnore([
                        'id' => $model->id,
                        'scheme_id' => $schemeId,
                        'kode_unit' => $model->code ?? $model->kode_unit ?? ('UNIT.' . $model->id),
                        'judul_unit' => $model->title ?? $model->judul_unit ?? 'Unit Kompetensi',
                        'jenis_standar' => $model->standard_reference ?? $model->jenis_standar ?? 'SKKNI',
                        'urutan' => $model->sequence ?? $model->urutan ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function setKodeUnitAttribute($value): void
    {
        $this->attributes['code'] = $value;
        $this->attributes['kode_unit'] = $value;
    }

    public function getKodeUnitAttribute(): ?string
    {
        return $this->attributes['code'] ?? $this->attributes['kode_unit'] ?? null;
    }

    public function setJudulUnitAttribute($value): void
    {
        $this->attributes['title'] = $value;
        $this->attributes['judul_unit'] = $value;
    }

    public function getJudulUnitAttribute(): ?string
    {
        return $this->attributes['title'] ?? $this->attributes['judul_unit'] ?? null;
    }

    public function setJenisStandarAttribute($value): void
    {
        $this->attributes['standard_reference'] = $value;
        $this->attributes['jenis_standar'] = $value;
    }

    public function getJenisStandarAttribute(): ?string
    {
        return $this->attributes['standard_reference'] ?? $this->attributes['jenis_standar'] ?? null;
    }

    public function setUrutanAttribute($value): void
    {
        $this->attributes['sequence'] = $value;
        $this->attributes['urutan'] = $value;
    }

    public function getUrutanAttribute(): ?int
    {
        return $this->attributes['sequence'] ?? $this->attributes['urutan'] ?? null;
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(UnitElement::class);
    }

    public function unitElements(): HasMany
    {
        return $this->elements();
    }

    public function kuks(): HasManyThrough
    {
        return $this->hasManyThrough(Kuk::class, UnitElement::class);
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }
}
