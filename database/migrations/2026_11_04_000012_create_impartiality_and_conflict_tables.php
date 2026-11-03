<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Impartiality Commitments & Pakta Integritas (FR-SOP-01.1, FR-SOP-01.4, FR-SOP-01.5)
        if (! Schema::hasTable('t_impartiality_commitments')) {
            Schema::create('t_impartiality_commitments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('commitment_type', 50); // LEMBAGA, PERSONIL, PAKTA_INTEGRITAS_ASESOR
                $table->text('statement_text');
                $table->timestamp('signed_at');
                $table->longText('signature_data')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Conflict of Interest Declarations (FR-SOP-01.5)
        if (! Schema::hasTable('t_conflict_declarations')) {
            Schema::create('t_conflict_declarations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('declarant_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('schedule_id')->nullable()->constrained('t_schedules')->nullOnDelete();
                $table->foreignId('assessment_id')->nullable()->constrained('t_assessments')->nullOnDelete();
                $table->foreignId('related_asesi_id')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('has_conflict')->default(false);
                $table->text('conflict_nature')->nullable(); // Hubungan keluarga/rekan kerja/pelatih
                $table->enum('status', ['OPEN', 'RESOLVED', 'TRANSFERRED'])->default('OPEN');
                $table->timestamp('declared_at');
                $table->timestamps();
            });
        }

        // 3. Assessor Replacement Events (FR-SOP-01.7)
        if (! Schema::hasTable('t_assessor_replacement_events')) {
            Schema::create('t_assessor_replacement_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained('t_assessments')->cascadeOnDelete();
                $table->foreignId('schedule_id')->nullable()->constrained('t_schedules')->nullOnDelete();
                $table->foreignId('previous_assessor_id')->constrained('users');
                $table->foreignId('replacement_assessor_id')->constrained('users');
                $table->text('reason'); // Dasar pergantian (benturan kepentingan, dll)
                $table->foreignId('approved_by')->constrained('users');
                $table->timestamp('approved_at');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_assessor_replacement_events');
        Schema::dropIfExists('t_conflict_declarations');
        Schema::dropIfExists('t_impartiality_commitments');
    }
};
