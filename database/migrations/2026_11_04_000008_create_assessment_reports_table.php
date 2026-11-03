<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('t_assessment_reports')) {
            Schema::create('t_assessment_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')->constrained('t_schedules')->cascadeOnDelete();
                $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete();
                $table->enum('report_type', ['AK05', 'AK06']);
                $table->unsignedInteger('revision')->default(1);
                $table->json('roster_json')->nullable();
                $table->json('content_json');
                $table->enum('status', ['DRAFT', 'SUBMITTED', 'APPROVED'])->default('DRAFT');
                $table->longText('assessor_signature')->nullable();
                $table->timestamp('assessor_signed_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('reviewer_notes')->nullable();
                $table->unsignedInteger('lock_version')->default(1);
                $table->timestamps();

                $table->unique(['schedule_id', 'assessor_id', 'report_type', 'revision'], 'report_unique_schedule_assessor_rev');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_assessment_reports');
    }
};
