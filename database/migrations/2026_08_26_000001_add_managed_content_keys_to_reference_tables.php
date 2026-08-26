<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLES = [
        'categories',
        'capabilities',
        'solution_patterns',
        'industries',
        'company_types',
        'departments',
        'assessment_questions',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->string('managed_content_key')->nullable()->unique();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropUnique($table.'_managed_content_key_unique');
                $blueprint->dropColumn('managed_content_key');
            });
        }
    }
};
