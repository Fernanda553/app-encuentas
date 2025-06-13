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
        Schema::create('custom_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vote_id')->constrained()->onDelete('cascade');
            $table->text('custom_text');
            $table->timestamps();

            $table->index('vote_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_answers');
    }
};
