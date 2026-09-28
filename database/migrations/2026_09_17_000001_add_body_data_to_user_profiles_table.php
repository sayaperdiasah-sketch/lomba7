<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('user_profiles', 'goldar')) {
                $table->string('goldar', 5)->nullable()->after('gender');
            }
            if (! Schema::hasColumn('user_profiles', 'tinggi')) {
                $table->integer('tinggi')->nullable()->after('goldar');
            }
            if (! Schema::hasColumn('user_profiles', 'berat')) {
                $table->integer('berat')->nullable()->after('tinggi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $columns = array_filter(['goldar', 'tinggi', 'berat'], fn (string $column): bool => Schema::hasColumn('user_profiles', $column));
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
