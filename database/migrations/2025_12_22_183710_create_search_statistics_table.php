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
        Schema::create('search_statistics', function (Blueprint $table) {
            $table->id();
            $table->json('top_queries'); // Top 5 queries com percentages
            $table->float('avg_response_time_ms');
            $table->json('popular_hours'); // Volume per hours
            $table->integer('total_searches');
            $table->timestamp('computed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_statistics');
    }
};
