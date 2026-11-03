<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rencana Surveilans Periodik (SOP 04 §4 / SOP 19)
        if (! Schema::hasTable('t_surveillance_plans')) {
            Schema::create('t_surveillance_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('certificate_id')->constrained('t_certificates')->cascadeOnDelete();
                $table->string('title', 255);
                $table->unsignedTinyInteger('surveillance_year')->default(1);
                $table->date('due_date');
                $table->text('required_evidence_description');
                $table->string('status', 30)->default('SCHEDULED'); // SCHEDULED, REPORTED, UNDER_REVIEW, COMPLETED, OVERDUE
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }

        // 2. Laporan & Evaluasi Surveilans (FR.SP.01, FR.SPS.01, FR.SK.01)
        if (! Schema::hasTable('t_surveillance_reports')) {
            Schema::create('t_surveillance_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('surveillance_plan_id')->constrained('t_surveillance_plans')->cascadeOnDelete();
                $table->foreignId('certificate_id')->constrained('t_certificates')->cascadeOnDelete();
                $table->foreignId('asesi_id')->constrained('users')->cascadeOnDelete();
                $table->text('activity_summary');
                $table->string('evidence_file_path', 500)->nullable();
                $table->timestamp('submitted_at');
                $table->foreignId('evaluator_id')->nullable()->constrained('users');
                $table->text('evaluator_notes')->nullable(); // FR.SPS.01
                $table->timestamp('evaluated_at')->nullable();
                $table->string('decision', 50)->nullable(); // COMPLIANT, ACTION_REQUIRED, SUSPENDED
                $table->text('decision_notes')->nullable();
                $table->foreignId('decided_by')->nullable()->constrained('users'); // FR.SK.01
                $table->timestamp('decided_at')->nullable();
                $table->string('status', 30)->default('SUBMITTED'); // SUBMITTED, UNDER_REVIEW, VERIFIED, REVISION_REQUESTED
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_surveillance_reports');
        Schema::dropIfExists('t_surveillance_plans');
    }
};
