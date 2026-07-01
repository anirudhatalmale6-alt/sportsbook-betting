<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add API-related fields for The Odds API integration.
     */
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('api_id')->nullable()->unique()->after('id');
        });

        Schema::table('leagues', function (Blueprint $table) {
            $table->string('api_key')->nullable()->after('slug');
        });

        Schema::table('markets', function (Blueprint $table) {
            $table->string('bookmaker')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('api_id');
        });

        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn('api_key');
        });

        Schema::table('markets', function (Blueprint $table) {
            $table->dropColumn('bookmaker');
        });
    }
};
