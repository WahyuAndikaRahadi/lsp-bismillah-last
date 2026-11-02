<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom sertifikat pada t_assessments
        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessments', 'no_sertifikat')) {
                    $table->string('no_sertifikat', 100)->nullable()->after('pleno_notes');
                }
                if (! Schema::hasColumn('t_assessments', 'tgl_terbit_sertifikat')) {
                    $table->date('tgl_terbit_sertifikat')->nullable()->after('no_sertifikat');
                }
                if (! Schema::hasColumn('t_assessments', 'masa_berlaku_sertifikat')) {
                    $table->date('masa_berlaku_sertifikat')->nullable()->after('tgl_terbit_sertifikat');
                }
            });
        }

        // 2. Lepas foreign key lama pleno_meetings ke assessment_schedules agar kompatibel dengan t_schedules
        if (Schema::hasTable('pleno_meetings') && DB::getDriverName() !== 'sqlite') {
            try {
                Schema::table('pleno_meetings', function (Blueprint $table) {
                    $table->dropForeign(['schedule_id']);
                });
            } catch (\Throwable $e) {
                // Abaikan jika foreign key sudah tidak ada atau nama berbeda
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('t_assessments', 'no_sertifikat')) {
                    $columnsToDrop[] = 'no_sertifikat';
                }
                if (Schema::hasColumn('t_assessments', 'tgl_terbit_sertifikat')) {
                    $columnsToDrop[] = 'tgl_terbit_sertifikat';
                }
                if (Schema::hasColumn('t_assessments', 'masa_berlaku_sertifikat')) {
                    $columnsToDrop[] = 'masa_berlaku_sertifikat';
                }
                if (! empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
