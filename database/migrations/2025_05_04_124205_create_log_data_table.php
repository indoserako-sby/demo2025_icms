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
        Schema::create('log_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('list_data_id')->constrained('list_data')->onDelete('cascade');
            $table->float('value')->nullable();
            $table->date('date');
            $table->string('state')->nullable();
            $table->enum('condition', ['good', 'warning', 'danger'])->default('good');
            $table->timestamps();

            // Index for faster queries
            $table->index(['asset_id', 'list_data_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_data');
    }
};
