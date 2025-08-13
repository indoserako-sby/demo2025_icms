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
        Schema::table('list_data', function (Blueprint $table) {
            $table->timestamp('warning_started_at')->nullable()->comment('waktu mulai warning');
            $table->timestamp('danger_started_at')->nullable()->comment('waktu mulai danger');
            $table->string('last_warning_state', 10)->nullable();
            $table->string('last_danger_state', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_data', function (Blueprint $table) {
            $table->dropColumn('warning_started_at');
            $table->dropColumn('danger_started_at');
            $table->dropColumn('last_warning_state');
            $table->dropColumn('last_danger_state');
        });
    }
};
