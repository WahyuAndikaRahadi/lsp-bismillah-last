<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('t_appeal_investigations')) {
            Schema::create('t_appeal_investigations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appeal_id')->constrained('t_assessment_appeals')->cascadeOnDelete();
                $table->foreignId('investigator_id')->constrained('users');
                $table->string('investigator_role', 100)->default('Investigator Banding Independen');
                $table->date('investigation_date');
                $table->string('investigation_place', 255)->default('Kantor LSP ProIndo');
                $table->text('factual_findings');
                $table->string('recommendation', 50); // MAINTAIN_DECISION, REASSESSMENT_REQUIRED, REVOKE_DECISION
                $table->text('recommendation_notes')->nullable();
                $table->foreignId('decided_by')->nullable()->constrained('users');
                $table->timestamp('decided_at')->nullable();
                $table->string('status', 30)->default('COMPLETED');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_appeal_investigations');
    }
};
