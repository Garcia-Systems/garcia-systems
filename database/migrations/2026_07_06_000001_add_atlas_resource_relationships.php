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

        Schema::create('workflow_article', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->unique(['workflow_id', 'article_id']);
        });

        Schema::create('workflow_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->unique(['workflow_id', 'video_id']);
        });

        Schema::create('workflow_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->unique(['workflow_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_service');
        Schema::dropIfExists('workflow_video');
        Schema::dropIfExists('workflow_article');

        Schema::table('workflows', function (Blueprint $table) {
            $table->dropColumn('assessment_path');
        });

        Schema::dropIfExists('services');
    }
};
