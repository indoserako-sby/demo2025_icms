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
        Schema::create('list_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->foreignId('asset_id')->constrained();
            $table->foreignId('machine_parameter_id')->constrained();
            $table->foreignId('position_id')->constrained();
            $table->foreignId('datvar_id')->constrained();
            $table->double('value')->nullable();
            $table->enum('state', ['ok', 'warning', 'danger', 'error', 'inactive', 'unknown'])->default('unknown');
            $table->enum('condition', ['good', 'warning', 'danger'])->default('good');
            $table->double('warning_limit')->nullable();
            $table->double('danger_limit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_data');
    }
};
