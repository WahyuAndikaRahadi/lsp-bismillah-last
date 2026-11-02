<?php

namespace Database\Seeders;

use App\Models\Tuk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Schema::hasTable('m_tuks')) {
            DB::table('m_tuks')->updateOrInsert(
                ['name' => 'TUK Cikarang'],
                [
                    'address' => 'Cikarang, Bekasi, Jawa Barat',
                    'phone' => '08666666666',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        if (Schema::hasTable('tuks')) {
            Tuk::updateOrCreate(
                ['nama_tuk' => 'TUK Cikarang'],
                [
                    'kode_tuk' => 'TUK-CKR-01',
                    'jenis_tuk' => 'Mandiri',
                    'alamat' => 'Cikarang, Bekasi, Jawa Barat',
                    'telepon' => '021-8901234',
                    'email' => 'tuk.cikarang@gmail.com',
                    'penanggung_jawab' => 'TUK Admin Cikarang',
                    'pic_phone' => '08666666666',
                    'is_active' => true,
                ]
            );
        }
    }
}
