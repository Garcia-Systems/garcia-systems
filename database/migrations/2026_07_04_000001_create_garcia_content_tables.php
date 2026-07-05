<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {$table->id();$table->string('name');$table->string('slug')->unique();$table->text('description')->nullable();$table->timestamps();});
        Schema::create('tags', function (Blueprint $table) {$table->id();$table->string('name');$table->string('slug')->unique();$table->timestamps();});
        Schema::create('articles', function (Blueprint $table) {$table->id();$table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();$table->string('title');$table->string('slug')->unique();$table->string('seo_title')->nullable();$table->text('seo_description')->nullable();$table->string('featured_image_url')->nullable();$table->string('excerpt');$table->longText('body');$table->boolean('is_published')->default(true);$table->timestamp('published_at')->nullable();$table->timestamps();});
        Schema::create('article_tag', function (Blueprint $table) {$table->id();$table->foreignId('article_id')->constrained()->cascadeOnDelete();$table->foreignId('tag_id')->constrained()->cascadeOnDelete();$table->unique(['article_id','tag_id']);});
        Schema::create('videos', function (Blueprint $table) {$table->id();$table->string('title');$table->string('slug')->unique();$table->string('url');$table->string('thumbnail_url')->nullable();$table->text('description');$table->longText('transcript')->nullable();$table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();$table->boolean('is_published')->default(true);$table->timestamps();});
        Schema::create('industries', function (Blueprint $table) {$table->id();$table->string('name');$table->string('slug')->unique();$table->text('description')->nullable();$table->timestamps();});
        Schema::create('company_types', function (Blueprint $table) {$table->id();$table->string('name');$table->string('slug')->unique();$table->text('description')->nullable();$table->timestamps();});
        Schema::create('departments', function (Blueprint $table) {$table->id();$table->string('name');$table->string('slug')->unique();$table->text('description')->nullable();$table->timestamps();});
        Schema::create('workflows', function (Blueprint $table) {$table->id();$table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();$table->foreignId('company_type_id')->nullable()->constrained()->nullOnDelete();$table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();$table->string('name');$table->string('slug')->unique();$table->text('description');$table->timestamps();});
        Schema::create('friction_points', function (Blueprint $table) {$table->id();$table->foreignId('workflow_id')->nullable()->constrained()->nullOnDelete();$table->string('name');$table->string('slug')->unique();$table->text('description');$table->string('impact')->nullable();$table->timestamps();});
        Schema::create('solution_patterns', function (Blueprint $table) {$table->id();$table->string('name');$table->string('slug')->unique();$table->text('description');$table->timestamps();});
        Schema::create('capabilities', function (Blueprint $table) {$table->id();$table->string('name');$table->string('slug')->unique();$table->text('description');$table->timestamps();});
        Schema::create('friction_point_solution_pattern', function (Blueprint $table) {$table->id();$table->foreignId('friction_point_id')->constrained()->cascadeOnDelete();$table->foreignId('solution_pattern_id')->constrained()->cascadeOnDelete();});
        Schema::create('capability_solution_pattern', function (Blueprint $table) {$table->id();$table->foreignId('capability_id')->constrained()->cascadeOnDelete();$table->foreignId('solution_pattern_id')->constrained()->cascadeOnDelete();});
        Schema::create('leads', function (Blueprint $table) {$table->id();$table->string('name');$table->string('email');$table->string('company')->nullable();$table->string('source')->nullable();$table->text('notes')->nullable();$table->timestamps();});
        Schema::create('contact_submissions', function (Blueprint $table) {$table->id();$table->string('name');$table->string('email');$table->string('company')->nullable();$table->string('service_interest')->nullable();$table->text('message');$table->timestamps();});
        Schema::create('assessments', function (Blueprint $table) {$table->id();$table->string('name')->nullable();$table->string('email')->nullable();$table->string('company')->nullable();$table->unsignedInteger('score')->default(0);$table->string('result_tier')->nullable();$table->text('summary')->nullable();$table->timestamps();});
        Schema::create('assessment_questions', function (Blueprint $table) {$table->id();$table->string('question');$table->text('help_text')->nullable();$table->unsignedInteger('sort_order')->default(0);$table->timestamps();});
        Schema::create('assessment_responses', function (Blueprint $table) {$table->id();$table->foreignId('assessment_id')->constrained()->cascadeOnDelete();$table->foreignId('assessment_question_id')->constrained()->cascadeOnDelete();$table->unsignedTinyInteger('score');$table->text('answer')->nullable();$table->timestamps();});
    }
    public function down(): void { foreach (array_reverse(['categories','tags','articles','article_tag','videos','industries','company_types','departments','workflows','friction_points','solution_patterns','capabilities','friction_point_solution_pattern','capability_solution_pattern','leads','contact_submissions','assessments','assessment_questions','assessment_responses']) as $t) Schema::dropIfExists($t); }
};