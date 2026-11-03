<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assessment_plans')) {
            Schema::create('assessment_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')->constrained('t_schedules')->cascadeOnDelete();
                $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('scheme_id')->constrained('m_schemes')->cascadeOnDelete();
                $table->foreignId('scheme_version_id')->nullable();
                $table->foreignId('assessment_id')->nullable()->constrained('t_assessments')->cascadeOnDelete();
                $table->unsignedInteger('revision')->default(1);
                $table->string('status', 30)->default('draft'); // draft, submitted, validated, locked
                $table->boolean('is_locked')->default(false);
                $table->timestamp('locked_at')->nullable();
                $table->unsignedInteger('lock_version')->default(1);
                $table->json('approach_data')->nullable(); // Section 1: 1.1 Asesi, Tujuan, Konteks, 1.2 Acuan
                $table->json('work_groups')->nullable();   // Section 2: Kelompok pekerjaan, unit, bukti, metode
                $table->json('modifications')->nullable(); // Section 3: 3.1 - 3.4
                $table->json('confirmation_data')->nullable(); // Section 4: Penyusun & Validator
                $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('validated_at')->nullable();
                $table->text('validation_notes')->nullable();
                $table->unsignedBigInteger('superseded_by')->nullable();
                $table->text('superseded_reason')->nullable();
                $table->timestamps();

                $table->index(['schedule_id', 'assessor_id', 'revision']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_plans');
    }
};
