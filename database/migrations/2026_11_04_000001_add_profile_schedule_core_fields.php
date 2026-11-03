<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('m_user_profiles')) {
            Schema::table('m_user_profiles', function (Blueprint $table) {
                if (! Schema::hasColumn('m_user_profiles', 'identity_type')) {
                    $table->string('identity_type', 20)->default('KTP')->after('user_id');
                }
                if (! Schema::hasColumn('m_user_profiles', 'signature_version')) {
                    $table->unsignedInteger('signature_version')->default(1)->after('signature_data');
                }
                if (! Schema::hasColumn('m_user_profiles', 'status_pekerjaan')) {
                    $table->string('status_pekerjaan', 100)->nullable()->after('pendidikan_terakhir');
                }
                if (! Schema::hasColumn('m_user_profiles', 'email_kantor')) {
                    $table->string('email_kantor', 255)->nullable()->after('alamat_kantor');
                }
                if (! Schema::hasColumn('m_user_profiles', 'no_fax_kantor')) {
                    $table->string('no_fax_kantor', 50)->nullable()->after('no_telepon_kantor');
                }
                if (! Schema::hasColumn('m_user_profiles', 'kode_pos_kantor')) {
                    $table->string('kode_pos_kantor', 20)->nullable()->after('alamat_kantor');
                }
            });
        }

        if (Schema::hasTable('t_schedules')) {
            Schema::table('t_schedules', function (Blueprint $table) {
                if (! Schema::hasColumn('t_schedules', 'capacity')) {
                    $table->unsignedInteger('capacity')->default(20)->after('status');
                }
                if (! Schema::hasColumn('t_schedules', 'scheme_version_id')) {
                    $table->foreignId('scheme_version_id')->nullable()->after('scheme_id');
                }
                if (! Schema::hasColumn('t_schedules', 'letter_number')) {
                    $table->string('letter_number', 100)->nullable()->after('capacity');
                }
                if (! Schema::hasColumn('t_schedules', 'assigned_by')) {
                    $table->string('assigned_by', 255)->nullable()->after('letter_number');
                }
                if (! Schema::hasColumn('t_schedules', 'assigned_at')) {
                    $table->timestamp('assigned_at')->nullable()->after('assigned_by');
                }
            });
        }

        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessments', 'snapshot_profile')) {
                    $table->longText('snapshot_profile')->nullable()->after('asesi_id');
                }
                if (! Schema::hasColumn('t_assessments', 'scheme_version_id')) {
                    $table->foreignId('scheme_version_id')->nullable()->after('schedule_id');
                }
            });
        }

        if (Schema::hasTable('tuk_verifications')) {
            Schema::table('tuk_verifications', function (Blueprint $table) {
                if (! Schema::hasColumn('tuk_verifications', 'spt_number')) {
                    $table->string('spt_number', 100)->nullable()->after('asesor_id');
                }
                if (! Schema::hasColumn('tuk_verifications', 'assigner_name')) {
                    $table->string('assigner_name', 255)->nullable()->after('spt_number');
                }
                if (! Schema::hasColumn('tuk_verifications', 'assigner_position')) {
                    $table->string('assigner_position', 255)->nullable()->after('assigner_name');
                }
                if (! Schema::hasColumn('tuk_verifications', 'verifier_name')) {
                    $table->string('verifier_name', 255)->nullable()->after('assigner_position');
                }
                if (! Schema::hasColumn('tuk_verifications', 'verifier_position')) {
                    $table->string('verifier_position', 255)->nullable()->after('verifier_name');
                }
            });
        }
    }

    public function down(): void
    {
    }
};
