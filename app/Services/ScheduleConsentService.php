<?php

namespace App\Services;

use App\Models\AuditEvent;
use App\Models\Schedule;
use App\Models\ScheduleConsent;
use App\Models\User;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ScheduleConsentService
{
    /**
     * Record an explicit, attributable consent event.
     */
    public function recordConsent(
        Schedule $schedule,
        User $user,
        string $purpose,
        string $consentText,
        ?string $signatureData,
        ?Request $request = null,
        int $scopeVersion = 1
    ): ScheduleConsent {
        if (trim($consentText) === '') {
            throw new InvalidArgumentException('Teks pernyataan persetujuan tidak boleh kosong.');
        }

        $currentUser = auth()->user();
        if ($currentUser && $currentUser->id !== $user->id && ! $currentUser->isAdmin()) {
            throw new InvalidArgumentException('Tidak dapat memberikan persetujuan atas nama pengguna lain.');
        }

        $textHash = hash('sha256', $consentText);

        $consent = ScheduleConsent::create([
            'schedule_id' => $schedule->id,
            'user_id' => $user->id,
            'purpose' => $purpose,
            'scope_version' => $scopeVersion,
            'text_hash' => $textHash,
            'signature_data' => $signatureData,
            'ip_address' => $request?->ip() ?? request()?->ip() ?? '127.0.0.1',
            'user_agent' => $request?->userAgent() ?? request()?->userAgent(),
            'granted_at' => now(),
            'revoked_at' => null,
        ]);

        AuditEvent::record(
            action: 'consent.granted',
            subject: $schedule,
            reason: "Persetujuan {$purpose} (versi {$scopeVersion}) disetujui secara eksplisit oleh {$user->name}.",
            details: [
                'user_id' => $user->id,
                'purpose' => $purpose,
                'scope_version' => $scopeVersion,
                'text_hash' => $textHash,
            ],
            userId: $user->id
        );

        return $consent;
    }

    /**
     * Revoke an existing consent with an attributable audit trail.
     */
    public function revokeConsent(ScheduleConsent $consent, User $revoker, string $reason): void
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan pencabutan persetujuan wajib disertakan.');
        }

        $consent->update([
            'revoked_at' => now(),
            'revoked_by' => $revoker->id,
            'revocation_reason' => $reason,
        ]);

        AuditEvent::record(
            action: 'consent.revoked',
            subject: $consent->schedule,
            reason: "Persetujuan {$consent->purpose} dicabut oleh {$revoker->name}: {$reason}",
            details: [
                'consent_id' => $consent->id,
                'user_id' => $consent->user_id,
                'purpose' => $consent->purpose,
                'revoked_by' => $revoker->id,
                'reason' => $reason,
            ],
            userId: $revoker->id
        );
    }

    /**
     * Check if user has active (non-revoked) consent for schedule.
     */
    public function hasActiveConsent(Schedule $schedule, User $user, string $purpose): bool
    {
        return ScheduleConsent::where('schedule_id', $schedule->id)
            ->where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNotNull('granted_at')
            ->whereNull('revoked_at')
            ->exists();
    }
}
