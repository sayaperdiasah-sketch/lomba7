<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (! Schema::hasColumn('faqs', 'keywords')) {
                $table->json('keywords')->nullable()->after('question');
            }
            if (! Schema::hasColumn('faqs', 'category')) {
                $table->string('category')->nullable()->after('keywords');
            }
            if (! Schema::hasColumn('faqs', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $columns = array_filter(['keywords', 'category', 'is_active'], fn (string $column): bool => Schema::hasColumn('faqs', $column));
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};