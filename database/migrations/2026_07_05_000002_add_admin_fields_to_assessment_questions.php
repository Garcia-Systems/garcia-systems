<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->string('category')->nullable()->after('help_text');
            $table->decimal('weight', 8, 2)->default(1)->after('sort_order');
            $table->boolean('is_active')->default(true)->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn(['category', 'weight', 'is_active']);
        });
    }
};
