<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessments', 'unlocked_instruments')) {
                    $table->json('unlocked_instruments')->nullable()->after('last_active_step');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (Schema::hasColumn('t_assessments', 'unlocked_instruments')) {
                    $table->dropColumn('unlocked_instruments');
                }
            });
        }
    }
};
