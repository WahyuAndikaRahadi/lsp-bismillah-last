<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assessment extends Model
{
    use HasFactory;

    protected $table = 't_assessments';

    protected $fillable = [
        'schedule_id',
        'asesi_id',
        'assessor_id',
        'assessor_signature',
        'assessor_signed_at',
        'asesi_signature',
        'asesi_signed_at',
        'apl_status',
        'apl_revision_notes',
        'last_active_step',
        'unlocked_instruments',
        'status',
        'admin_status',
        'admin_notes',
        'admin_approved_at',
        'admin_id',
        'admin_reviewed_at',
        'admin_reviewer_id',
        'final_result',
        'final_result_at',
        'rekomendasi_asesor',
        'final_notes',
        'follow_up_action',
        'ak01_agreed_at',
        'attendance_at',
        'esign_consent_at',
        'pleno_decision',
        'pleno_notes',
        'no_sertifikat',
        'tgl_terbit_sertifikat',
        'masa_berlaku_sertifikat',
        'registered_at',
        'approved_at',
        'completed_at',
        'prior_certificate_id',
        'snapshot_profile',
        'scheme_version_id',
        'lock_version',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'admin_approved_at' => 'datetime',
            'admin_reviewed_at' => 'datetime',
            'ak01_agreed_at' => 'datetime',
            'attendance_at' => 'datetime',
            'esign_consent_at' => 'datetime',
            'final_result_at' => 'datetime',
            'assessor_signed_at' => 'datetime',
            'asesi_signed_at' => 'datetime',
            'tgl_terbit_sertifikat' => 'date',
            'masa_berlaku_sertifikat' => 'date',
            'unlocked_instruments' => 'array',
            'snapshot_profile' => 'array',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function scheme(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Scheme::class,
            Schedule::class,
            'id',
            'id',
            'schedule_id',
            'scheme_id'
        );
    }

    public function getSchemeAttribute(): ?Scheme
    {
        if ($this->relationLoaded('scheme') && $this->getRelation('scheme')) {
            return $this->getRelation('scheme');
        }

        return $this->schedule?->scheme ?? $this->schemeVersion?->scheme;
    }

    public function schemeVersion(): BelongsTo
    {
        return $this->belongsTo(SchemeVersion::class, 'scheme_version_id');
    }

    public function asesi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesi_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function proofs(): HasMany
    {
        return $this->hasMany(AssessmentProof::class);
    }

    public function apl02Items(): HasMany
    {
        return $this->hasMany(AssessmentApl02Item::class);
    }

    public function ak07Items(): HasMany
    {
        return $this->hasMany(AssessmentAk07Item::class);
    }

    public function ak02Items(): HasMany
    {
        return $this->hasMany(AssessmentAk02Item::class);
    }

    public function ak03Item(): HasOne
    {
        return $this->hasOne(AssessmentAk03Item::class);
    }

    public function ak03Items(): HasMany
    {
        return $this->hasMany(AssessmentAk03Item::class);
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(AssessmentAppeal::class)->latest();
    }

    public function latestAppeal(): HasOne
    {
        return $this->hasOne(AssessmentAppeal::class)->latestOfMany();
    }

    public function iaResponses(): HasMany
    {
        return $this->hasMany(AssessmentIaResponse::class);
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(CertificationDecision::class, 'assessment_id');
    }

    public function certificationDecisions(): HasMany
    {
        return $this->decisions();
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'assessment_id');
    }

    public function activeCertificate(): ?Certificate
    {
        return $this->certificates()->where('status', 'ACTIVE')->latest('revision')->first();
    }

    public function priorCertificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'prior_certificate_id');
    }

    public function adminReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_reviewer_id');
    }

    public function canAccessFeedback(): bool
    {
        return $this->status === 'COMPLETED' && in_array($this->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true);
    }

    public function canAccessAppeal(): bool
    {
        return $this->status === 'COMPLETED' && in_array($this->final_result, ['KOMPETEN', 'BELUM_KOMPETEN', 'K', 'BK'], true);
    }

    public function isCompetent(): bool
    {
        return in_array($this->final_result, ['KOMPETEN', 'kompeten', 'K'], true)
            || in_array($this->pleno_decision, ['KOMPETEN', 'kompeten', 'K', 'disahkan'], true);
    }

    public function isApprovedByAdmin(): bool
    {
        return $this->admin_status === 'APPROVED';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }

    /**
     * Get the active/unlocked MUK instruments for this candidate.
     * Defaults to the schedule's effective instruments when not customized.
     */
    public function getUnlockedInstruments(): array
    {
        if (! empty($this->unlocked_instruments) && is_array($this->unlocked_instruments)) {
            return array_values(array_map('strtoupper', $this->unlocked_instruments));
        }

        $effective = $this->schedule?->getEffectiveInstruments() ?? ['IA01', 'IA01B', 'IA02', 'IA03', 'IA05'];
        // Filter out non-IA instruments from ruang uji if any
        return array_values(array_filter($effective, fn ($code) => ! in_array(strtoupper($code), ['AK01', 'AK07'])));
    }

    /**
     * Determine if a specific MUK instrument is unlocked for this candidate.
     */
    public function isInstrumentUnlocked(string $code): bool
    {
        return in_array(strtoupper($code), $this->getUnlockedInstruments(), true);
    }
}
