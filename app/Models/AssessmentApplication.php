<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentVerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pendaftaran',
        'user_id',
        'scheme_id',
        'schedule_id',
        'asesor_id',
        'tujuan_asesmen',
        'status',
        'catatan_revisi',
        'rekomendasi_admin',
        'catatan_admin',
        'admin_id',
        'admin_verified_at',
        'tgl_pengajuan',
        'ttd_asesi',
        'tgl_ttd_asesi',
        'ttd_admin',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'admin_verified_at' => 'datetime',
            'tgl_pengajuan' => 'datetime',
            'tgl_ttd_asesi' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AssessmentSchedule::class, 'schedule_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function apl02(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Apl02Assessment::class, 'application_id');
    }

    public function ak01(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Ak01Agreement::class, 'application_id');
    }

    public function ak02(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Ak02Assessment::class, 'application_id');
    }

    public function ak03(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Ak03Feedback::class, 'application_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class, 'application_id');
    }

    /**
     * Generate unique registration number: REG-{YYYYMM}-{0001}
     */
    public static function generateRegistrationNumber(): string
    {
        $prefix = 'REG-' . date('Ym') . '-';
        $latest = static::where('nomor_pendaftaran', 'LIKE', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->nomor_pendaftaran, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nomor_pendaftaran', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, function ($q, $status) {
                if ($status !== 'all' && ApplicationStatus::tryFrom($status)) {
                    $q->where('status', $status);
                }
            })
            ->when($filters['scheme_id'] ?? null, function ($q, $schemeId) {
                $q->where('scheme_id', $schemeId);
            })
            ->when($filters['schedule_id'] ?? null, function ($q, $scheduleId) {
                $q->where('schedule_id', $scheduleId);
            });
    }

    public function isAllDocumentsValid(): bool
    {
        if ($this->documents->isEmpty()) {
            return false;
        }

        return $this->documents->every(fn($doc) => $doc->status_verifikasi === DocumentVerificationStatus::Memenuhi);
    }

    public function hasInvalidDocument(): bool
    {
        return $this->documents->contains(fn($doc) => $doc->status_verifikasi === DocumentVerificationStatus::TidakMemenuhi);
    }
}
