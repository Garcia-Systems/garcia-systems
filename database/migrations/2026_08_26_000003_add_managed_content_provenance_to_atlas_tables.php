<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['workflows', 'friction_points'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->string('managed_content_key')->nullable()->unique();
                $blueprint->string('managed_content_hash', 64)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['workflows', 'friction_points'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropUnique($table.'_managed_content_key_unique');
                $blueprint->dropColumn(['managed_content_key', 'managed_content_hash']);
            });
        }
    }
};
