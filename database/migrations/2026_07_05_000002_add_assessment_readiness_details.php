<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->string('category')->nullable()->after('help_text');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->json('category_scores')->nullable()->after('summary');
            $table->json('risks')->nullable()->after('category_scores');
            $table->json('next_steps')->nullable()->after('risks');
            $table->json('recommendations')->nullable()->after('next_steps');
            $table->string('service_cta')->nullable()->after('recommendations');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['category_scores', 'risks', 'next_steps', 'recommendations', 'service_cta']);
        });

        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
