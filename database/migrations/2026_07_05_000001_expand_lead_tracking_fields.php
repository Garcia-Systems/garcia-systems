<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('status')->default('new')->after('source');
            $table->unsignedInteger('assessment_score')->nullable()->after('status');
            $table->string('assessment_tier')->nullable()->after('assessment_score');
            $table->timestamp('last_contacted_at')->nullable()->after('notes');
            $table->timestamp('next_follow_up_at')->nullable()->after('last_contacted_at');
            $table->timestamp('latest_activity_at')->nullable()->after('next_follow_up_at');
            $table->index('email');
            $table->index(['status', 'source']);
        });

        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
        });

        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['status', 'source']);
            $table->dropColumn(['status', 'assessment_score', 'assessment_tier', 'last_contacted_at', 'next_follow_up_at', 'latest_activity_at']);
        });
    }
};
