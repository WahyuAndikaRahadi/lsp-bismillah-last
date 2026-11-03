<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('t_schedule_minutes')) {
            Schema::create('t_schedule_minutes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')->constrained('t_schedules')->cascadeOnDelete();
                $table->string('document_code', 50)->default('FR-SOP-04.4');
                $table->unsignedInteger('revision')->default(1);
                $table->date('actual_date');
                $table->string('actual_start_time', 20)->nullable();
                $table->string('actual_end_time', 20)->nullable();
                $table->string('location')->nullable();
                $table->unsignedInteger('enrolled_count')->default(0);
                $table->unsignedInteger('present_count')->default(0);
                $table->unsignedInteger('absent_count')->default(0);
                $table->unsignedInteger('competent_count')->default(0);
                $table->unsignedInteger('not_competent_count')->default(0);
                $table->unsignedInteger('pending_count')->default(0);
                $table->json('roster_summary_json')->nullable();
                $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('coordinator_name')->nullable();
                $table->longText('coordinator_signature')->nullable();
                $table->timestamp('coordinator_signed_at')->nullable();
                $table->foreignId('lead_assessor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->longText('lead_assessor_signature')->nullable();
                $table->timestamp('lead_assessor_signed_at')->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['DRAFT', 'FINAL'])->default('DRAFT');
                $table->unsignedInteger('lock_version')->default(1);
                $table->timestamps();

                $table->unique(['schedule_id', 'revision'], 'minutes_schedule_rev_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_schedule_minutes');
    }
};
