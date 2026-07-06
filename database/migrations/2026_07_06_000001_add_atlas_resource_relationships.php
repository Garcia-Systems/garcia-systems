<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('workflows', function (Blueprint $table) {
            $table->string('assessment_path')->nullable()->after('description');
        });

        Schema::create('article_workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->unique(['article_id', 'workflow_id']);
        });

        Schema::create('video_workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->unique(['video_id', 'workflow_id']);
        });

        Schema::create('service_workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->unique(['service_id', 'workflow_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_workflow');
        Schema::dropIfExists('video_workflow');
        Schema::dropIfExists('article_workflow');

        Schema::table('workflows', function (Blueprint $table) {
            $table->dropColumn('assessment_path');
        });

        Schema::dropIfExists('services');
    }
};
