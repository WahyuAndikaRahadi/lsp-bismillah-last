<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'm_user_profiles';

    protected $fillable = [
        'user_id',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'kebangsaan',
        'alamat_rumah',
        'kode_pos',
        'no_telepon_rumah',
        'no_telepon_kantor',
        'pendidikan_terakhir',
        'nama_institusi_perusahaan',
        'nama_institusi',
        'jabatan',
        'alamat_kantor',
        'signature_data',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAddressAttribute(): ?string
    {
        return $this->alamat_rumah;
    }

    public function getBirthPlaceAttribute(): ?string
    {
        return $this->tempat_lahir;
    }

    public function getBirthDateAttribute()
    {
        return $this->tanggal_lahir;
    }

    public function getGenderAttribute(): ?string
    {
        return $this->jenis_kelamin;
    }

    public function getInstitutionAttribute(): ?string
    {
        return $this->nama_institusi_perusahaan;
    }

    public function getCompanyAttribute(): ?string
    {
        return $this->nama_institusi_perusahaan;
    }

    public function getNamaInstitusiAttribute(): ?string
    {
        return $this->nama_institusi_perusahaan;
    }

    public function setNamaInstitusiAttribute(?string $value): void
    {
        $this->attributes['nama_institusi_perusahaan'] = $value;
    }

    public function getJobTitleAttribute(): ?string
    {
        return $this->jabatan;
    }

    public function getOccupationAttribute(): ?string
    {
        return $this->jabatan;
    }

    public function getMetNumberAttribute(): ?string
    {
        return $this->user?->assessor_registration_number;
    }

    public function isComplete(): bool
    {
        return ! empty($this->nik)
            && ! empty($this->tempat_lahir)
            && ! empty($this->tanggal_lahir)
            && ! empty($this->jenis_kelamin)
            && ! empty($this->kebangsaan)
            && ! empty($this->alamat_rumah)
            && ! empty($this->pendidikan_terakhir)
            && ! empty($this->jabatan)
            && ! empty($this->signature_data)
            && ! empty($this->user?->phone);
    }
}
