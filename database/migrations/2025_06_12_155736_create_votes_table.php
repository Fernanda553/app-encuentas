<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained()->onDelete('cascade');
            $table->string('ip_address');
            $table->string('session_id');
            $table->timestamps();

            $table->unique(['answer_id', 'ip_address', 'session_id']);
            $table->index(['ip_address', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
}; 