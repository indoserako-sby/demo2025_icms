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
        Schema::create('data_alarms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('list_data_id');
            $table->string('alert_type', 20)->check("alert_type IN ('warning', 'danger')");
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->boolean('resolved')->default(false);

            // Kolom tambahan yang diminta
            $table->boolean('acknowledged')->default(false);
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('alarm_cause')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // Foreign keys
            $table->foreign('list_data_id')->references('id')->on('list_data');
            $table->foreign('acknowledged_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_alarms');
    }
};
