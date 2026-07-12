<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_installation_runs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('dataset');
            $table->string('status')->default('running');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rolled_back_at')->nullable();
            $table->string('executed_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('content_installation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_installation_run_id')->constrained()->cascadeOnDelete();
            $table->string('item_type');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('stable_key')->nullable();
            $table->string('action');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['model_type', 'model_id']);
            $table->index(['stable_key', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_installation_items');
        Schema::dropIfExists('content_installation_runs');
    }
};
