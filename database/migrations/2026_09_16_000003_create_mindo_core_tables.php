<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // ─── Identitas ─────────────────────────────
            $table->string('nama')->nullable();
            $table->integer('usia')->nullable();
            $table->string('gender', 20)->nullable();

            // ─── Body data ─────────────────────────────
            $table->string('goldar', 5)->nullable();
            $table->integer('tinggi')->nullable();
            $table->integer('berat')->nullable();

            // ─── Profil ────────────────────────────────
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->timestamp('profile_completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};