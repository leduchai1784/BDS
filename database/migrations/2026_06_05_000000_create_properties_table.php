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
            $table->id();
            $table->string('title');
            $table->string('type'); // Apartment, house, room, office, villa
            $table->unsignedBigInteger('price'); // Monthly price in VND
            $table->string('price_label'); // Short price label (e.g. 6.5tr, 18tr)
            $table->unsignedInteger('area'); // Area in sqm
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);
            $table->string('location'); // Full address
            $table->string('district', 10); // District abbreviation (Q1, Q3, BT, CG, GL, TH)
            $table->double('lat', 10, 6); // Latitude
            $table->double('lng', 10, 6); // Longitude
            $table->string('image'); // Main feature image path
            $table->json('images'); // Extra gallery images
            $table->string('direction')->nullable(); // Feng shui direction
            $table->text('furniture')->nullable(); // Furniture description
            $table->string('legal')->nullable(); // Legal documents details
            $table->boolean('is_vip')->default(false);
            $table->boolean('is_new')->default(true);
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->text('description');
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
