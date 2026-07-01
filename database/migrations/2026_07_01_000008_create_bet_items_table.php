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
        Schema::create('bet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bet_id')->constrained('bets')->cascadeOnDelete();
            $table->foreignId('odd_id')->constrained('odds');
            $table->decimal('odd_value_at_placement', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_items');
    }
};
