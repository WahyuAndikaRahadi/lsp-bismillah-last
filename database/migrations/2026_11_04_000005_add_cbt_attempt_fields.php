<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('t_assessment_ia_responses')) {
            Schema::table('t_assessment_ia_responses', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessment_ia_responses', 'started_at')) {
                    $table->timestamp('started_at')->nullable()->after('instrument_code');
                }
                if (! Schema::hasColumn('t_assessment_ia_responses', 'duration_seconds')) {
                    $table->integer('duration_seconds')->default(3600)->after('started_at');
                }
                if (! Schema::hasColumn('t_assessment_ia_responses', 'time_remaining_seconds')) {
                    $table->integer('time_remaining_seconds')->nullable()->after('duration_seconds');
                }
                if (! Schema::hasColumn('t_assessment_ia_responses', 'submitted_at')) {
                    $table->timestamp('submitted_at')->nullable()->after('time_remaining_seconds');
                }
                if (! Schema::hasColumn('t_assessment_ia_responses', 'attempt_version')) {
                    $table->integer('attempt_version')->default(1)->after('submitted_at');
                }
                if (! Schema::hasColumn('t_assessment_ia_responses', 'is_expired')) {
                    $table->boolean('is_expired')->default(false)->after('attempt_version');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('t_assessment_ia_responses')) {
            Schema::table('t_assessment_ia_responses', function (Blueprint $table) {
                $table->dropColumn([
                    'started_at',
                    'duration_seconds',
                    'time_remaining_seconds',
                    'submitted_at',
                    'attempt_version',
                    'is_expired',
                ]);
            });
        }
    }
};
