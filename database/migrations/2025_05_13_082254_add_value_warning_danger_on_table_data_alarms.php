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
            $table->float('warning')->nullable()->after('value');
            $table->float('danger')->nullable()->after('warning');
            $table->float('value')->nullable()->after('alert_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_alarms', function (Blueprint $table) {
            $table->dropColumn('warning');
            $table->dropColumn('danger');
            $table->dropColumn('value');
        });
    }
};
