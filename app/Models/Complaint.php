<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'nama_pelapor',
        'email',
        'telepon',
        'jenis_responden',
        'judul',
        'isi_aduan',
        'status',
        'tindak_lanjut',
        'petugas_id',
        'tgl_tindak_lanjut',
    ];

    protected function casts(): array
    {
        return [
            'tgl_tindak_lanjut' => 'datetime',
        ];
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT-'.date('Ymd').'-';
        $last = self::where('ticket_number', 'LIKE', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->ticket_number);
            $seq = intval(end($parts)) + 1;
        }

        return $prefix.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'selesai' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'ditolak' => 'bg-rose-50 text-rose-700 border border-rose-200',
            default => 'bg-amber-50 text-amber-700 border border-amber-200',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'selesai' => 'Selesai Ditindaklanjuti',
            'ditolak' => 'Ditolak',
            default => 'Sedang Diproses',
        };
    }
}
