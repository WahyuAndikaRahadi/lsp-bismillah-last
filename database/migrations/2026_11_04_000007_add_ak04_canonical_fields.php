<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('t_assessment_appeals')) {
            Schema::table('t_assessment_appeals', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessment_appeals', 'prosedur_dijelaskan')) {
                    $table->boolean('prosedur_dijelaskan')->nullable()->after('appeal_date');
                }
                if (! Schema::hasColumn('t_assessment_appeals', 'diskusi_dengan_asesor')) {
                    $table->boolean('diskusi_dengan_asesor')->nullable()->after('prosedur_dijelaskan');
                }
                if (! Schema::hasColumn('t_assessment_appeals', 'melibatkan_orang_lain')) {
                    $table->boolean('melibatkan_orang_lain')->nullable()->after('diskusi_dengan_asesor');
                }
                if (! Schema::hasColumn('t_assessment_appeals', 'asesi_signature')) {
                    $table->longText('asesi_signature')->nullable()->after('melibatkan_orang_lain');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('t_assessment_appeals')) {
            Schema::table('t_assessment_appeals', function (Blueprint $table) {
                if (Schema::hasColumn('t_assessment_appeals', 'prosedur_dijelaskan')) {
                    $table->dropColumn(['prosedur_dijelaskan', 'diskusi_dengan_asesor', 'melibatkan_orang_lain', 'asesi_signature']);
                }
            });
        }
    }
};
