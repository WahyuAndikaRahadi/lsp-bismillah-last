<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('full_name') && ! $this->has('name')) {
            $this->merge(['name' => $this->full_name]);
        }
        if ($this->has('name') && ! $this->has('full_name')) {
            $this->merge(['full_name' => $this->name]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'nik' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/', 'unique:users,nik'],
            'phone' => ['required', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\s\-]+$/'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'terms' => ['accepted'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi sesuai KTP.',
            'email.required' => 'Alamat email aktif wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.size' => 'NIK harus berjumlah tepat 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh memuat angka.',
            'nik.unique' => 'NIK ini sudah terdaftar di sistem.',
            'phone.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'phone.min' => 'Nomor HP / WhatsApp minimal 10 digit.',
            'phone.regex' => 'Format nomor HP tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui syarat & ketentuan pendaftaran sertifikasi.',
        ];
    }
}
