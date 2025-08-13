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
        Schema::table('data_alarms', function (Blueprint $table) {
            if (!Schema::hasColumn('data_alarms', 'alarm_cause')) {
                $table->string('alarm_cause')->nullable();
            }
            if (!Schema::hasColumn('data_alarms', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('data_alarms', 'acknowledged_by')) {
                $table->foreignId('acknowledged_by')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('data_alarms', 'acknowledged_at')) {
                $table->timestamp('acknowledged_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_alarms', function (Blueprint $table) {
            $table->dropColumn(['alarm_cause', 'notes']);
            $table->dropForeign(['acknowledged_by']);
            $table->dropColumn(['acknowledged_by', 'acknowledged_at']);
        });
    }
};
