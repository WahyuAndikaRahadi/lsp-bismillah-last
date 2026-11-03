<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('t_attendance_events')) {
            Schema::table('t_attendance_events', function (Blueprint $table) {
                if (! Schema::hasColumn('t_attendance_events', 'is_reversed')) {
                    $table->boolean('is_reversed')->default(false)->after('recorded_by');
                }
                if (! Schema::hasColumn('t_attendance_events', 'reversed_at')) {
                    $table->timestamp('reversed_at')->nullable()->after('is_reversed');
                }
                if (! Schema::hasColumn('t_attendance_events', 'reversed_by')) {
                    $table->unsignedBigInteger('reversed_by')->nullable()->after('reversed_at');
                }
                if (! Schema::hasColumn('t_attendance_events', 'reversal_reason')) {
                    $table->text('reversal_reason')->nullable()->after('reversed_by');
                }

                $table->index(['schedule_id', 'user_id', 'event_type'], 'idx_att_sched_user_evt');
            });
        }

        if (Schema::hasTable('t_schedule_consents')) {
            Schema::table('t_schedule_consents', function (Blueprint $table) {
                if (! Schema::hasColumn('t_schedule_consents', 'revoked_by')) {
                    $table->unsignedBigInteger('revoked_by')->nullable()->after('revoked_at');
                }
                if (! Schema::hasColumn('t_schedule_consents', 'revocation_reason')) {
                    $table->text('revocation_reason')->nullable()->after('revoked_by');
                }
                if (! Schema::hasColumn('t_schedule_consents', 'scope_version')) {
                    $table->integer('scope_version')->default(1)->after('purpose');
                }
            });
        }

        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessments', 'attendance_at')) {
                    $table->timestamp('attendance_at')->nullable()->after('ak01_agreed_at');
                }
            });
        }

        if (Schema::hasTable('t_schedules')) {
            Schema::table('t_schedules', function (Blueprint $table) {
                if (! Schema::hasColumn('t_schedules', 'start_date')) {
                    $table->date('start_date')->nullable()->after('date');
                }
                if (! Schema::hasColumn('t_schedules', 'end_date')) {
                    $table->date('end_date')->nullable()->after('start_date');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('t_schedules')) {
            Schema::table('t_schedules', function (Blueprint $table) {
                $table->dropColumn(['start_date', 'end_date']);
            });
        }

        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (Schema::hasColumn('t_assessments', 'attendance_at')) {
                    $table->dropColumn('attendance_at');
                }
            });
        }

        if (Schema::hasTable('t_schedule_consents')) {
            Schema::table('t_schedule_consents', function (Blueprint $table) {
                $table->dropColumn(['revoked_by', 'revocation_reason', 'scope_version']);
            });
        }

        if (Schema::hasTable('t_attendance_events')) {
            Schema::table('t_attendance_events', function (Blueprint $table) {
                $table->dropIndex('idx_att_sched_user_evt');
                $table->dropColumn(['is_reversed', 'reversed_at', 'reversed_by', 'reversal_reason']);
            });
        }
    }
};
