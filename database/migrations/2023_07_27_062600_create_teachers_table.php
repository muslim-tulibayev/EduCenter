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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique()->index('idx_email');
            $table->string('password');
            $table->string('contact');
            $table->boolean('is_assistant');
            $table->boolean('status')->default(false);
            $table->foreignId('role_id')->constrained();
            $table->enum('lang', ['en', 'ru', 'uz'])->default('en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
