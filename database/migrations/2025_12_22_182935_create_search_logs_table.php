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
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // 'people' ou 'films'
            $table->string('search_term')->nullable();
            $table->integer('results_count');
            $table->float('response_time_ms'); // response time in ms
            $table->timestamp('searched_at');
            $table->timestamps();
            $table->index(['entity_type', 'search_term']);
            $table->index('searched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
