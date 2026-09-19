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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('market_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('stall_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('device_uid')->unique();

            $table->string('device_name')->nullable();

            $table->string('microcontroller')->default('Arduino');
            $table->string('communication_module')->default('ESP8266');

            $table->string('temperature_sensor')->default('DHT22');
            $table->string('humidity_sensor')->default('DHT22');
            $table->string('gas_sensor')->default('MQ-135');

            $table->string('firmware_version')->nullable();

            $table->timestamp('last_seen_at')->nullable();

            $table->enum('status', [
                'online',
                'offline',
                'maintenance',
                'inactive'
            ])->default('offline');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
