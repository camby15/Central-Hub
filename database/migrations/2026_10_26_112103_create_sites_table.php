<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url');
            $table->string('api_key_prefix', 32)->nullable();
            $table->text('hmac_secret')->nullable();
            $table->json('allowed_origins')->nullable();
            $table->string('framework');
            $table->boolean('is_active')->default(true);
            $table->string('primary_color', 16)->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
