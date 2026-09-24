<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot: which stalls each inspector is assigned to monitor.
     * Admin assigns inspectors to specific stalls.
     */
    public function up(): void
    {
        Schema::create('inspector_stall', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')   // inspector
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('stall_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'stall_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspector_stall');
    }
};
