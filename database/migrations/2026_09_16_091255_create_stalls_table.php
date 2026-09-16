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
        Schema::create('stalls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('market_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('stall_number');
            $table->string('section')->nullable();

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique([
                'market_id',
                'stall_number'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stalls');
    }
};
