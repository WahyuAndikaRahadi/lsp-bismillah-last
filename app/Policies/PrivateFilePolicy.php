<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\PrivateFile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivateFilePolicy
{
    use HandlesAuthorization;

    public function view(User $user, PrivateFile $file): bool
    {
        // 1. Revoked files are inaccessible
        if ($file->status === 'revoked') {
            return false;
        }

        // 2. Secret answer keys are strictly prohibited for asesi
        $isAnswerKey = in_array(strtolower($file->original_name), ['answer_key', 'kunci_jawaban'], true)
            || str_contains(strtolower($file->path), 'answer_key')
            || str_contains(strtolower($file->path), 'kunci_jawaban');

        if ($user->isAsesi() && $isAnswerKey) {
            return false;
        }

        // 3. Admin has audit/review access
        if ($user->isAdmin()) {
            return true;
        }

        // 4. File owned by user or user profile
        if ($file->owner_type === User::class && $file->owner_id === $user->id) {
            return true;
        }

        if ($file->owner_type === UserProfile::class || $file->owner_type === 'm_user_profiles') {
            $profile = UserProfile::find($file->owner_id);
            if ($profile && $profile->user_id === $user->id) {
                return true;
            }
        }

        // 5. File attached to an assessment
        if ($file->owner_type === Assessment::class || $file->owner_type === 't_assessments') {
            $assessment = Assessment::find($file->owner_id);
            if (! $assessment) {
                return false;
            }

            if ($user->isAsesi()) {
                return $assessment->asesi_id === $user->id && ! $isAnswerKey;
            }

            if ($user->isAsesor()) {
                if ($assessment->assessor_id === $user->id) {
                    return true;
                }

                if ($assessment->schedule && $assessment->schedule->assessors()->where('users.id', $user->id)->exists()) {
                    return true;
                }
            }
        }

        // 6. Direct uploader (if owner matches current user context)
        if ($file->uploaded_by === $user->id && ! ($user->isAsesi() && $isAnswerKey)) {
            return true;
        }

        return false;
    }

    public function delete(User $user, PrivateFile $file): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($file->uploaded_by === $user->id && $file->status !== 'verified') {
            return true;
        }

        return false;
    }
}
