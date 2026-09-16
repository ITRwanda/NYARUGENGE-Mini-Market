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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('stall_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('sensor_reading_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('threshold_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('parameter');

            $table->decimal('measured_value', 10, 2);
            $table->decimal('threshold_value', 10, 2)->nullable();

            $table->enum('severity', [
                'info',
                'warning',
                'critical'
            ])->default('warning');

            $table->text('message');

            $table->enum('status', [
                'open',
                'acknowledged',
                'resolved'
            ])->default('open');

            $table->foreignId('acknowledged_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('acknowledged_at')->nullable();

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index([
                'status',
                'severity'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
