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
        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedBigInteger('deposit')->nullable()->after('price_label');
            $table->string('lease_term')->nullable()->after('deposit');
            $table->decimal('frontage', 8, 2)->nullable()->after('lease_term');
            $table->decimal('road_width', 8, 2)->nullable()->after('frontage');
            $table->unsignedInteger('floors')->nullable()->after('road_width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['deposit', 'lease_term', 'frontage', 'road_width', 'floors']);
        });
    }
};
