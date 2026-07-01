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
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('property_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->uuid('property_id')->change();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }
};
