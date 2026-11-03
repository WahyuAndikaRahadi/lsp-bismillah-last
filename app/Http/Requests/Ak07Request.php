<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Ak07Request extends FormRequest
{
    public static array $CATEGORY_OPTIONS = [
        1 => [
            'Memerlukan dukungan pembaca, penerjemah, pelayan, penulis untuk merekam jawaban asesi.',
            'Melakukan asesmen verbal (gunakan pertanyaan lisan/pertanyaan wawancara) dengan dilengkapi gambar diagram dan bentuk-bentuk visual.',
            'Menggunakan Hasil produksi.',
            'Menggunakan Ceklis observasi/demonstrasi.',
            'Menggunakan daftar instruksi terstruktur.',
        ],
        2 => [
            'Menggunakan pertanyaan lisan dengan dilengkapi gambar diagram dan bentuk-bentuk visual.',
            'Menggunakan pertanyaan wawancara dengan dilengkapi gambar diagram dan bentuk-bentuk visual.',
        ],
        3 => [
            'Ceklis observasi/demonstrasi',
            'Demonstrasi',
            'Pertanyaan lisan',
            'Pertanyaan tertulis',
            'Pertanyaan wawancara',
            'Daftar instruksi terstruktur',
            'Ceklis verifikasi portofolio',
            'Menggunakan dukungan operator komputer',
        ],
        4 => [
            'Menggunakan juru tulis.',
            'Menggunakan kameramen perekam video/audio.',
            'Memperbolehkan periode waktu yang lebih panjang untuk menyelesaikan tugas pekerjaan dalam asesmen.',
            'Melakukan tugas pekerjaan dalam asesmen dengan waktu lebih pendek.',
            'Menggunakan instruksi-instruksi spesifik pada proyek yang dapat dilakukan pada berbagai tingkatan.',
        ],
        5 => [
            'Menggunakan pertanyaan lisan.',
            'Menggunakan pertanyaan wawancara.',
        ],
        6 => [
            'Pertanyaan lisan.',
            'Pertanyaan tulis.',
            'Pertanyaan wawancara.',
            'Ceklis Verifikasi portofolio.',
            'Ceklis reviu produk.',
            'Daftar instruksi terstruktur.',
        ],
        7 => [
            'Menggunakan studi kasus / daftar instruksi terstruktur.',
            'Menggunakan instrumen asesmen dengan huruf normal jangan terlalu kecil.',
            'Menggunakan asesor dengan jenis kelamin yang sama dengan asesi.',
            'Menggunakan instrumen asesmen yang sama walaupun berbeda jenis kelamin (tidak membedakan jenis kelamin).',
        ],
        8 => [
            'Menggunakan studi kasus daftar instruksi terstruktur.',
            'Menggunakan asesor tanpa pertimbangan budaya/tradisi/agama.',
            'Menggunakan instrumen asesmen yang sama walaupun berbeda budaya/tradisi/agama.',
        ],
    ];

    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $user->isAsesor() || $user->isAsesi();
    }

    public function rules(): array
    {
        return [
            'assessment_id' => 'required_without:assessment_ids|nullable|exists:t_assessments,id',
            'assessment_ids' => 'required_without:assessment_id|nullable|array',
            'assessment_ids.*' => 'exists:t_assessments,id',
            'timing' => 'required|in:sebelum_pra_asesmen,saat_pra_asesmen,setelah_pra_asesmen',
            'items' => 'required|array|min:1',
            'items.*.category_number' => 'nullable|integer|between:1,8',
            'items.*.title' => 'required|string',
            'items.*.decision' => 'required_without:items.*.needed|nullable|in:ya,tidak,YA,TIDAK',
            'items.*.needed' => 'required_without:items.*.decision|nullable|in:ya,tidak,YA,TIDAK',
            'items.*.options' => 'nullable|array',
            'items.*.detail' => 'nullable|string',
            'items.*.custom_note' => 'nullable|string',
            'is_agreed' => 'nullable|boolean',
            'agreement_status' => 'nullable|in:ya,tidak,YA,TIDAK',
            'agreed_benchmark' => 'nullable|string',
            'agreement_benchmark' => 'nullable|string',
            'agreed_method' => 'nullable|string',
            'agreement_method' => 'nullable|string',
            'agreed_instrument' => 'nullable|string',
            'agreement_instrument' => 'nullable|string',
            'agreement_notes' => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $idx => $item) {
                $catNum = (int) ($item['category_number'] ?? $idx);
                $decision = strtolower($item['decision'] ?? $item['needed'] ?? '');
                $options = $item['options'] ?? [];
                $detail = trim(($item['detail'] ?? '') . ($item['custom_note'] ?? ''));

                if ($decision === 'ya') {
                    if (empty($options) && $detail === '') {
                        $validator->errors()->add(
                            "items.{$idx}.decision",
                            "Kategori #{$catNum} dijawab 'Ya' namun tidak menyertakan rincian opsi atau catatan penyesuaian. Inconsistent Ya ditolak."
                        );
                    }

                    // Unknown option check
                    $allowedOptions = self::$CATEGORY_OPTIONS[$catNum] ?? [];
                    if (! empty($options) && ! empty($allowedOptions)) {
                        foreach ($options as $opt) {
                            if (! in_array($opt, $allowedOptions, true)) {
                                $validator->errors()->add(
                                    "items.{$idx}.options",
                                    "Opsi '{$opt}' tidak dikenal untuk kategori #{$catNum}. Unknown option ditolak."
                                );
                            }
                        }
                    }
                }
            }
        });
    }
}
