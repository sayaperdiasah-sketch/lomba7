<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_confirmations', function (Blueprint $table) {
            $table->json('items')->nullable()->after('done_count');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_confirmations', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }
};
