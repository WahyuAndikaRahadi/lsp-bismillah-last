<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assessment_plans')) {
            Schema::table('assessment_plans', function (Blueprint $table) {
                if (! Schema::hasColumn('assessment_plans', 'mapa02_data')) {
                    $table->json('mapa02_data')->nullable()->after('confirmation_data');
                }
            });
        }

        if (Schema::hasTable('t_assessment_ak07_items')) {
            Schema::table('t_assessment_ak07_items', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessment_ak07_items', 'category_number')) {
                    $table->unsignedTinyInteger('category_number')->nullable()->after('adjustment_category');
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'decision')) {
                    $table->string('decision', 10)->nullable()->after('category_number'); // 'ya' or 'tidak'
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'selected_options')) {
                    $table->json('selected_options')->nullable()->after('decision');
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'timing')) {
                    $table->string('timing', 50)->nullable()->after('selected_options');
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'is_agreed')) {
                    $table->boolean('is_agreed')->default(false)->after('timing');
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'agreed_benchmark')) {
                    $table->string('agreed_benchmark', 255)->nullable()->after('is_agreed');
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'agreed_method')) {
                    $table->string('agreed_method', 255)->nullable()->after('agreed_benchmark');
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'agreed_instrument')) {
                    $table->string('agreed_instrument', 255)->nullable()->after('agreed_method');
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'asesi_signed_at')) {
                    $table->timestamp('asesi_signed_at')->nullable()->after('agreed_instrument');
                }
                if (! Schema::hasColumn('t_assessment_ak07_items', 'asesor_signed_at')) {
                    $table->timestamp('asesor_signed_at')->nullable()->after('asesi_signed_at');
                }
            });
        }
    }

    public function down(): void
    {
    }
};
