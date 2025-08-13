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
        Schema::table('log_data', function (Blueprint $table) {
            $table->double('warning')->nullable()->after('condition');
            $table->double('danger')->nullable()->after('warning');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_data', function (Blueprint $table) {
            $table->dropColumn(['warning', 'danger']);
        });
    }
};
