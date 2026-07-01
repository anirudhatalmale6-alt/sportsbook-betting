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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('leagues');
            $table->string('home_team');
            $table->string('away_team');
            $table->string('home_team_logo')->nullable();
            $table->string('away_team_logo')->nullable();
            $table->dateTime('start_time');
            $table->enum('status', ['upcoming', 'live', 'finished', 'cancelled'])->default('upcoming');
            $table->integer('score_home')->nullable();
            $table->integer('score_away')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
