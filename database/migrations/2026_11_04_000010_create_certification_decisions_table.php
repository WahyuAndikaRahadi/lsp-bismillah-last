<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('t_certification_decisions')) {
            Schema::create('t_certification_decisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->foreignId('schedule_id')->nullable()->constrained('t_schedules')->cascadeOnDelete();
                $table->foreignId('pleno_meeting_id')->nullable()->constrained('pleno_meetings')->nullOnDelete();
                $table->enum('decision', ['KOMPETEN', 'BELUM_KOMPETEN', 'CORRECTION_REQUESTED']);
                $table->text('decision_notes')->nullable();
                $table->text('corrective_action')->nullable();
                $table->text('validation_notes')->nullable();
                $table->foreignId('decided_by')->nullable()->constrained('users');
                $table->string('authority_role', 100)->default('Ketua Komite Teknis');
                $table->timestamp('decided_at')->nullable();
                $table->boolean('evidence_verified')->default(true);
                $table->boolean('is_superseded')->default(false);
                $table->foreignId('superseded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('superseded_at')->nullable();
                $table->text('supersede_reason')->nullable();
                $table->enum('status', ['FINAL', 'SUPERSEDED', 'CANCELLED'])->default('FINAL');
                $table->unsignedInteger('lock_version')->default(1);
                $table->timestamps();
            });
        } else {
            Schema::table('t_certification_decisions', function (Blueprint $table) {
                if (! Schema::hasColumn('t_certification_decisions', 'schedule_id')) {
                    $table->unsignedBigInteger('schedule_id')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'pleno_meeting_id')) {
                    $table->unsignedBigInteger('pleno_meeting_id')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'decision_notes')) {
                    $table->text('decision_notes')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'corrective_action')) {
                    $table->text('corrective_action')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'validation_notes')) {
                    $table->text('validation_notes')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'decided_by')) {
                    $table->unsignedBigInteger('decided_by')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'authority_role')) {
                    $table->string('authority_role', 100)->default('Ketua Komite Teknis');
                }
                if (! Schema::hasColumn('t_certification_decisions', 'decided_at')) {
                    $table->timestamp('decided_at')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'evidence_verified')) {
                    $table->boolean('evidence_verified')->default(true);
                }
                if (! Schema::hasColumn('t_certification_decisions', 'is_superseded')) {
                    $table->boolean('is_superseded')->default(false);
                }
                if (! Schema::hasColumn('t_certification_decisions', 'superseded_by')) {
                    $table->unsignedBigInteger('superseded_by')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'superseded_at')) {
                    $table->timestamp('superseded_at')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'supersede_reason')) {
                    $table->text('supersede_reason')->nullable();
                }
                if (! Schema::hasColumn('t_certification_decisions', 'status')) {
                    $table->string('status', 30)->default('FINAL');
                }
                if (! Schema::hasColumn('t_certification_decisions', 'lock_version')) {
                    $table->unsignedInteger('lock_version')->default(1);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_certification_decisions');
    }
};
