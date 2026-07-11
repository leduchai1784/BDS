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
        Schema::create('ai_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->uuid('property_id')->nullable()->constrained('properties')->onDelete('cascade');
            $table->string('type'); // 'marketing' or 'content_studio'
            $table->string('title');
            $table->string('goal')->nullable();
            $table->string('tone');
            $table->json('content'); // Contains all AI generated texts, templates, prompts
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_campaigns');
    }
};
