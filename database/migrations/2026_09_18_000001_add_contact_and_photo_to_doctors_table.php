<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('whatsapp', 30)->nullable()->after('bio');
            $table->string('website', 2048)->nullable()->after('whatsapp');
            $table->mediumText('photo')->nullable()->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'website', 'photo']);
        });
    }
};