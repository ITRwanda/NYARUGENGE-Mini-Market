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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('market_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('stall_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('inspector_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->dateTime('inspection_date');

            $table->enum('status', [
                'pending',
                'completed',
                'follow_up'
            ])->default('pending');

            $table->text('general_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
