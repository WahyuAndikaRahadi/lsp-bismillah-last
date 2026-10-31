<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'password',
        'role',
        'status',
        'nik',
        'phone',
        'is_active',
        'avatar',
        'signature_image',
        'assessor_registration_number',
        'is_committee_head',
        'tuk_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_committee_head' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->attributes['full_name'] ?? $this->attributes['name'] ?? '';
    }

    public function setFullNameAttribute($val): void
    {
        $this->attributes['full_name'] = $val;
        if (empty($this->attributes['name'])) {
            $this->attributes['name'] = $val;
        }
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function assignedSchedules(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 't_schedule_assessors', 'assessor_id', 'schedule_id');
    }

    public function scheduleAssessments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assessment::class, 'asesi_id');
    }

    public function assessorAssessments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assessment::class, 'assessor_id');
    }

    public function isRole(string $role): bool
    {
        $currentRole = is_object($this->role) ? $this->role->value : (string) $this->role;
        $map = [
            'admin' => ['admin', 'ADMIN_LSP', 'SUPER_ADMIN'],
            'asesor' => ['asesor', 'ASESOR'],
            'asesi' => ['asesi', 'ASESI'],
            'SUPER_ADMIN' => ['SUPER_ADMIN', 'admin'],
            'ADMIN_LSP' => ['ADMIN_LSP', 'admin'],
            'ASESOR' => ['ASESOR', 'asesor'],
            'ASESI' => ['ASESI', 'asesi'],
        ];

        return in_array($currentRole, $map[$role] ?? [$role], true);
    }

    public function isProfileComplete(): bool
    {
        if (! $this->isRole('ASESI') && ! $this->isAsesi()) {
            return true;
        }

        $profile = $this->profile ?? $this->profile()->first();
        if (! $profile) {
            return false;
        }

        return ! empty($profile->nik)
            && ! empty($profile->tempat_lahir)
            && ! empty($profile->tanggal_lahir)
            && ! empty($profile->alamat_rumah)
            && ! empty($profile->signature_data);
    }

    public function isAdmin(): bool
    {
        $currentRole = is_object($this->role) ? $this->role->value : (string) $this->role;
        return in_array($currentRole, ['admin', 'SUPER_ADMIN', 'ADMIN_LSP'], true);
    }

    public function dashboardRoute(): string
    {
        if ($this->isAdmin() || $this->isRole('ADMIN_LSP') || $this->isRole('SUPER_ADMIN')) {
            return 'admin.dashboard';
        }
        if ($this->isAsesor() || $this->isRole('ASESOR')) {
            return 'asesor.dashboard';
        }
        return 'asesi.dashboard';
    }

    public function isAsesor(): bool
    {
        $currentRole = is_object($this->role) ? $this->role->value : (string) $this->role;
        return in_array($currentRole, ['asesor', 'ASESOR'], true);
    }

    public function isAsesi(): bool
    {
        $currentRole = is_object($this->role) ? $this->role->value : (string) $this->role;
        return in_array($currentRole, ['asesi', 'ASESI'], true);
    }

    public function getRoleLabelAttribute(): string
    {
        $roleStr = is_object($this->role) ? ($this->role->value ?? '') : (string) $this->role;
        return match (strtoupper($roleStr)) {
            'SUPER_ADMIN' => 'Super Admin',
            'ADMIN_LSP', 'ADMIN' => 'Admin LSP',
            'KOMITE_TEKNIS' => 'Komite Teknis',
            'ASESOR' => 'Asesor Kompetensi',
            'TUK_ADMIN' => 'Admin TUK',
            'ASESI' => 'Asesi (Peserta)',
            default => ucfirst(strtolower($roleStr ?: 'Pengguna')),
        };
    }

    public function getRoleBadgeClassesAttribute(): string
    {
        $roleStr = is_object($this->role) ? ($this->role->value ?? '') : (string) $this->role;
        return match (strtoupper($roleStr)) {
            'SUPER_ADMIN' => 'bg-purple-50 text-purple-700 border border-purple-200',
            'ADMIN_LSP', 'ADMIN' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
            'KOMITE_TEKNIS' => 'bg-amber-50 text-amber-700 border border-amber-200',
            'ASESOR' => 'bg-blue-50 text-blue-700 border border-blue-200',
            'TUK_ADMIN' => 'bg-cyan-50 text-cyan-700 border border-cyan-200',
            'ASESI' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            default => 'bg-slate-50 text-slate-700 border border-slate-200',
        };
    }

    public function asesorProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AsesorProfile::class, 'user_id');
    }

    public function asesiProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AsesiProfile::class, 'user_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'id');
    }

    public function getOrganizationAttribute(): ?string
    {
        return $this->asesiProfile?->nama_perusahaan;
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AssessmentApplication::class, 'user_id');
    }

    public function hasRole(UserRole|string $role): bool
    {
        if ($role instanceof UserRole) {
            return $this->role === $role;
        }

        return $this->role->value === $role;
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= mb_substr($w, 0, 1);
        }
        return strtoupper($initials ?: 'U');
    }
}
