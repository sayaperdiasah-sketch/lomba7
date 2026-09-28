<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('tinggi')->nullable()->change();
            $table->integer('berat')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->decimal('tinggi', 5, 2)->nullable()->change();
            $table->decimal('berat', 5, 2)->nullable()->change();
        });
    }
};