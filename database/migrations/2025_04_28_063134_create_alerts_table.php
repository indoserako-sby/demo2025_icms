<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained()->onDelete('cascade');
            $table->foreignId('parameter_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['warning', 'danger']);
            $table->decimal('value', 10, 2);
            $table->text('message');
            $table->enum('status', ['active', 'acknowledged', 'resolved', 'comment'])->default('active');
            $table->timestamp('triggered_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alerts');
    }
};
