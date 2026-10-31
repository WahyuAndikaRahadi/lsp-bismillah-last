<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidSignature implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if (! is_string($value)) {
            $fail('Format tanda tangan digital tidak valid.');

            return;
        }

        // Must match data:image/png (or jpg/webp);base64,...
        if (! preg_match('/^data:image\/(png|jpe?g|webp);base64,([A-Za-z0-9+\/=\r\n]+)$/', $value, $matches)) {
            $fail('Tanda tangan digital harus berupa gambar data URL base64 (PNG/JPG/WEBP).');

            return;
        }

        $decoded = base64_decode($matches[2], false);
        if ($decoded === false) {
            $fail('Data base64 tanda tangan digital rusak atau tidak valid.');

            return;
        }

        // Reject if > 500 KB (512,000 bytes)
        if (strlen($decoded) > 512000) {
            $fail('Ukuran data tanda tangan digital terlalu besar (maksimal 500 KB).');

            return;
        }
    }
}
