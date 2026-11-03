<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessments', 'rekomendasi_asesor')) {
                    $table->string('rekomendasi_asesor', 20)->nullable()->after('final_result');
                }
            });
        }

        if (Schema::hasTable('t_assessment_ak02_items')) {
            Schema::table('t_assessment_ak02_items', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessment_ak02_items', 'finding_notes')) {
                    $table->text('finding_notes')->nullable()->after('is_competent');
                }
                if (! Schema::hasColumn('t_assessment_ak02_items', 'assessor_id')) {
                    $table->foreignId('assessor_id')->nullable()->after('finding_notes')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('t_assessment_ak02_items', 'recorded_at')) {
                    $table->timestamp('recorded_at')->nullable()->after('assessor_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessments', 'rekomendasi_asesor')) {
                    $table->string('rekomendasi_asesor', 20)->nullable()->after('final_result');
                }
            });
        }

        if (Schema::hasTable('t_assessment_ak02_items')) {
            Schema::table('t_assessment_ak02_items', function (Blueprint $table) {
                if (Schema::hasColumn('t_assessment_ak02_items', 'finding_notes')) {
                    $table->dropColumn('finding_notes');
                }
                if (Schema::hasColumn('t_assessment_ak02_items', 'assessor_id')) {
                    $table->dropForeign(['assessor_id']);
                    $table->dropColumn('assessor_id');
                }
                if (Schema::hasColumn('t_assessment_ak02_items', 'recorded_at')) {
                    $table->dropColumn('recorded_at');
                }
            });
        }
    }
};
