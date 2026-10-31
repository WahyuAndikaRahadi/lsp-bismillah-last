<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend users table for ProIndo compatibility
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 30)->default('ACTIVE')->after('role');
            }
            if (! Schema::hasColumn('users', 'assessor_registration_number')) {
                $table->string('assessor_registration_number', 50)->nullable()->after('status');
            }
            if (! Schema::hasColumn('users', 'is_committee_head')) {
                $table->boolean('is_committee_head')->default(false)->after('assessor_registration_number');
            }
            if (! Schema::hasColumn('users', 'tuk_id')) {
                $table->unsignedBigInteger('tuk_id')->nullable()->after('is_committee_head');
            }
        });

        // 2. m_tuks
        if (! Schema::hasTable('m_tuks')) {
            Schema::create('m_tuks', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('address')->nullable();
                $table->string('phone', 30)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. m_user_profiles
        if (! Schema::hasTable('m_user_profiles')) {
            Schema::create('m_user_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->string('nik', 20)->nullable();
                $table->string('tempat_lahir', 100)->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
                $table->string('kebangsaan', 50)->default('Indonesia');
                $table->text('alamat_rumah')->nullable();
                $table->string('kode_pos', 10)->nullable();
                $table->string('no_telepon_rumah', 30)->nullable();
                $table->string('no_telepon_kantor', 30)->nullable();
                $table->string('pendidikan_terakhir', 50)->nullable();
                $table->string('nama_institusi_perusahaan', 255)->nullable();
                $table->string('jabatan', 100)->nullable();
                $table->text('alamat_kantor')->nullable();
                $table->longText('signature_data')->nullable();
                $table->timestamps();
            });
        }

        // 4. m_schemes
        if (! Schema::hasTable('m_schemes')) {
            Schema::create('m_schemes', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('name');
                $table->enum('scheme_type', ['OKUPASI', 'KLASTER', 'KKNI'])->default('KLASTER');
                $table->string('version', 20)->default('1.0');
                $table->text('description')->nullable();
                $table->decimal('passing_grade_theory', 5, 2)->default(75.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 5. m_competency_units
        if (! Schema::hasTable('m_competency_units')) {
            Schema::create('m_competency_units', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('scheme_id')->index();
                $table->string('code', 50)->default('');
                $table->string('title')->default('');
                $table->string('standard_reference', 100)->nullable()->default('SKKNI');
                $table->integer('sequence')->default(1);
                $table->string('kode_unit', 50)->nullable();
                $table->string('judul_unit')->nullable();
                $table->string('jenis_standar', 50)->nullable();
                $table->integer('urutan')->nullable();
                $table->timestamps();
            });
        }

        // 6. m_unit_elements
        if (! Schema::hasTable('m_unit_elements')) {
            Schema::create('m_unit_elements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('competency_unit_id')->constrained('m_competency_units')->cascadeOnDelete();
                $table->text('title');
                $table->integer('sequence')->default(1);
                $table->timestamps();
            });
        }

        // 7. m_kuks
        if (! Schema::hasTable('m_kuks')) {
            Schema::create('m_kuks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('unit_element_id')->constrained('m_unit_elements')->cascadeOnDelete();
                $table->string('code_number', 20);
                $table->text('statement');
                $table->integer('sequence')->default(1);
                $table->timestamps();
            });
        }

        // 8. m_question_banks
        if (! Schema::hasTable('m_question_banks')) {
            Schema::create('m_question_banks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('scheme_id')->constrained('m_schemes')->cascadeOnDelete();
                $table->foreignId('competency_unit_id')->nullable()->constrained('m_competency_units')->nullOnDelete();
                $table->text('question_text');
                $table->string('question_image')->nullable();
                $table->text('option_a');
                $table->text('option_b');
                $table->text('option_c');
                $table->text('option_d');
                $table->enum('correct_answer', ['A', 'B', 'C', 'D']);
                $table->timestamps();
            });
        }

        // 9. m_settings
        if (! Schema::hasTable('m_settings')) {
            Schema::create('m_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // 10. t_schedules
        if (! Schema::hasTable('t_schedules')) {
            Schema::create('t_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('name')->nullable();
                $table->foreignId('scheme_id')->constrained('m_schemes');
                $table->foreignId('tuk_id')->nullable()->constrained('m_tuks')->nullOnDelete();
                $table->string('tuk_name');
                $table->string('pic_name')->nullable();
                $table->string('pic_position', 100)->nullable();
                $table->longText('pic_signature')->nullable();
                $table->date('date');
                $table->time('start_time');
                $table->time('end_time');
                $table->integer('duration_minutes')->nullable();
                $table->json('selected_instruments');
                $table->text('notes')->nullable();
                $table->json('ak05_report')->nullable();
                $table->json('ak06_review')->nullable();
                $table->boolean('is_closed')->default(false);
                $table->timestamp('closed_at')->nullable();
                $table->enum('status', ['DRAFT', 'ACTIVE', 'FINISHED'])->default('ACTIVE');
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }

        // 11. t_schedule_assessors
        if (! Schema::hasTable('t_schedule_assessors')) {
            Schema::create('t_schedule_assessors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')->constrained('t_schedules')->cascadeOnDelete();
                $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['schedule_id', 'assessor_id']);
            });
        }

        // 12. t_assessments
        if (! Schema::hasTable('t_assessments')) {
            Schema::create('t_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')->constrained('t_schedules')->cascadeOnDelete();
                $table->foreignId('asesi_id')->constrained('users');
                $table->foreignId('assessor_id')->nullable()->constrained('users');
                $table->longText('assessor_signature')->nullable();
                $table->timestamp('assessor_signed_at')->nullable();
                $table->longText('asesi_signature')->nullable();
                $table->timestamp('asesi_signed_at')->nullable();
                $table->enum('apl_status', ['DRAFT', 'SUBMITTED', 'REVISION_REQUESTED', 'APPROVED', 'REJECTED'])->default('DRAFT');
                $table->enum('admin_status', ['PENDING', 'APPROVED', 'REVISION', 'REJECTED'])->default('PENDING');
                $table->text('admin_notes')->nullable();
                $table->timestamp('admin_approved_at')->nullable();
                $table->timestamp('admin_reviewed_at')->nullable();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('admin_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('apl_revision_notes')->nullable();
                $table->string('last_active_step', 50)->default('apl');
                $table->string('status', 50)->default('IN_PROGRESS');
                $table->string('final_result', 50)->default('BELUM_SELESAI');
                $table->timestamp('final_result_at')->nullable();
                $table->text('final_notes')->nullable();
                $table->text('follow_up_action')->nullable();
                $table->timestamp('ak01_agreed_at')->nullable();
                $table->timestamp('esign_consent_at')->nullable();
                $table->enum('pleno_decision', ['KOMPETEN', 'BELUM_KOMPETEN'])->nullable();
                $table->text('pleno_notes')->nullable();
                $table->timestamp('registered_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->unique(['schedule_id', 'asesi_id']);
            });
        }

        // 13. t_assessment_proofs
        if (! Schema::hasTable('t_assessment_proofs')) {
            Schema::create('t_assessment_proofs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->enum('proof_type', ['IJAZAH_SMK', 'SURAT_SISWA_AKTIF', 'IJAZAH_SMA_SERTIFIKAT', 'PENGALAMAN_KERJA', 'KTP', 'PAS_FOTO']);
                $table->enum('proof_category', ['PERSYARATAN_DASAR', 'ADMINISTRATIF']);
                $table->string('file_path');
                $table->string('original_filename');
                $table->timestamps();
                $table->unique(['assessment_id', 'proof_type'], 't_assessment_proofs_unique');
            });
        }

        // 14. t_assessment_apl02_items
        if (! Schema::hasTable('t_assessment_apl02_items')) {
            Schema::create('t_assessment_apl02_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->foreignId('kuk_id')->constrained('m_kuks')->cascadeOnDelete();
                $table->enum('assessment_value', ['K', 'BK']);
                $table->string('selected_proof_type', 50)->nullable();
                $table->timestamps();
                $table->unique(['assessment_id', 'kuk_id'], 't_assessment_apl02_unique');
            });
        }

        // 15. t_assessment_ak07_items
        if (! Schema::hasTable('t_assessment_ak07_items')) {
            Schema::create('t_assessment_ak07_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->string('adjustment_category', 100);
                $table->text('adjustment_detail')->nullable();
                $table->foreignId('assessor_id')->constrained('users');
                $table->timestamps();
                $table->unique(['assessment_id', 'adjustment_category'], 't_assessment_ak07_unique');
            });
        }

        // 16. t_assessment_ia_responses
        if (! Schema::hasTable('t_assessment_ia_responses')) {
            Schema::create('t_assessment_ia_responses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->enum('instrument_code', ['IA01', 'IA01B', 'IA02', 'IA03', 'IA05']);
                $table->json('data_json')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                $table->text('assessor_notes')->nullable();
                $table->longText('assessor_signature')->nullable();
                $table->text('asesi_feedback')->nullable();
                $table->longText('asesi_signature')->nullable();
                $table->timestamp('assessor_signed_at')->nullable();
                $table->timestamp('asesi_signed_at')->nullable();
                $table->timestamps();
                $table->unique(['assessment_id', 'instrument_code'], 't_assessment_ia_unique');
            });
        }

        // 17. t_assessment_ak02_items
        if (! Schema::hasTable('t_assessment_ak02_items')) {
            Schema::create('t_assessment_ak02_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->foreignId('competency_unit_id')->constrained('m_competency_units')->cascadeOnDelete();
                $table->json('methods_json')->nullable();
                $table->boolean('is_competent')->default(true);
                $table->timestamps();
                $table->unique(['assessment_id', 'competency_unit_id'], 't_assessment_ak02_unique');
            });
        }

        // 18. t_assessment_ak03_items
        if (! Schema::hasTable('t_assessment_ak03_items')) {
            Schema::create('t_assessment_ak03_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->json('ratings_json')->nullable();
                $table->text('general_notes')->nullable();
                $table->timestamps();
                $table->unique('assessment_id', 't_assessment_ak03_unique');
            });
        }

        // 19. t_assessment_appeals
        if (! Schema::hasTable('t_assessment_appeals')) {
            Schema::create('t_assessment_appeals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->date('appeal_date');
                $table->text('appeal_reasons');
                $table->json('contested_units')->nullable();
                $table->enum('status', ['SUBMITTED', 'UNDER_REVIEW', 'ACCEPTED', 'REJECTED'])->default('SUBMITTED');
                $table->text('decision_notes')->nullable();
                $table->date('decision_date')->nullable();
                $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('appeals_committee_chair')->nullable();
                $table->text('assessor_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_assessment_appeals');
        Schema::dropIfExists('t_assessment_ak03_items');
        Schema::dropIfExists('t_assessment_ak02_items');
        Schema::dropIfExists('t_assessment_ia_responses');
        Schema::dropIfExists('t_assessment_ak07_items');
        Schema::dropIfExists('t_assessment_apl02_items');
        Schema::dropIfExists('t_assessment_proofs');
        Schema::dropIfExists('t_assessments');
        Schema::dropIfExists('t_schedule_assessors');
        Schema::dropIfExists('t_schedules');
        Schema::dropIfExists('m_settings');
        Schema::dropIfExists('m_question_banks');
        Schema::dropIfExists('m_kuks');
        Schema::dropIfExists('m_unit_elements');
        Schema::dropIfExists('m_competency_units');
        Schema::dropIfExists('m_schemes');
        Schema::dropIfExists('m_user_profiles');
        Schema::dropIfExists('m_tuks');
    }
};
