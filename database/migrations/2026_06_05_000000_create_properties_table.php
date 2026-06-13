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
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->unsignedBigInteger('price');
            $table->string('price_label');
            $table->unsignedInteger('area');
            $table->unsignedTinyInteger('bedroom')->default(0);
            $table->unsignedTinyInteger('bathroom')->default(0);
            $table->string('address');
            $table->string('ward');
            $table->string('district', 10);
            $table->string('city');
            $table->double('latitude', 10, 6);
            $table->double('longitude', 10, 6);
            $table->string('phone');
            $table->string('zalo')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedInteger('views_count')->default(0);
            $table->string('direction')->nullable();
            $table->text('furniture')->nullable();
            $table->string('legal')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->boolean('is_new')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
