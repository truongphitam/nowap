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
            $table->uuid('id')->primary();
            $table->string('competition_id');
            $table->uuid('home_team_id');
            $table->uuid('away_team_id');
            $table->unsignedInteger('status_id');
            $table->integer('match_time');
            $table->json('home_scores')->nullable();
            $table->json('away_scores')->nullable();
            $table->timestamps();

            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            $table->foreign('home_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('away_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('matches_statuses')->onDelete('cascade');
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
