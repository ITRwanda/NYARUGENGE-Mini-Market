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
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('market_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('device_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('parameter');

            $table->decimal('minimum_value', 10, 2)->nullable();
            $table->decimal('maximum_value', 10, 2)->nullable();

            $table->string('unit')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'parameter',
                'is_active'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thresholds');
    }
};
