<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->string('key')->nullable()->unique()->after('id');
            $table->boolean('is_active')->default(true)->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->dropColumn(['key', 'is_active']);
        });
    }
};
