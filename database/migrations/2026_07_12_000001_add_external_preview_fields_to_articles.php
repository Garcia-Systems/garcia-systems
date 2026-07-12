<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('external_url', 2048)->nullable()->after('substack_embed_code');
            $table->string('external_preview_image_url', 2048)->nullable()->after('external_url');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['external_url', 'external_preview_image_url']);
        });
    }
};
