<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            if (! Schema::hasColumn('assessment_questions', 'category')) {
                $table->string('category')->nullable()->after('help_text');
            }

            if (! Schema::hasColumn('assessment_questions', 'weight')) {
                $table->decimal('weight', 8, 2)->default(1)->after('sort_order');
            }

            if (! Schema::hasColumn('assessment_questions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('weight');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $columns = collect(['category', 'weight', 'is_active'])
                ->filter(fn (string $column) => Schema::hasColumn('assessment_questions', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
