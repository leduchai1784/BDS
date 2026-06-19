<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('transaction_type')->nullable()->after('category_id');
            $table->string('property_type')->nullable()->after('transaction_type');
            $table->string('province')->nullable()->after('city');

            // Add indexes
            $table->index('transaction_type');
            $table->index('property_type');
            $table->index('province');
            $table->index('district');
            $table->index('price');
            $table->index('area');
            $table->index('status');
        });

        // Populate new columns for existing records
        try {
            // Fill province from city
            DB::statement("UPDATE properties SET province = city WHERE province IS NULL");
            
            // Fill transaction_type from price
            DB::statement("UPDATE properties SET transaction_type = CASE WHEN price <= 150000000 THEN 'rent' ELSE 'sale' END WHERE transaction_type IS NULL");

            // Fill property_type from categories table
            DB::statement("
                UPDATE properties p
                SET property_type = (
                    SELECT CASE 
                        WHEN c.slug = 'chung-cu' THEN 'apartment'
                        WHEN c.slug = 'nha-nguyen-can' THEN 'house'
                        WHEN c.slug = 'phong-tro' THEN 'room'
                        WHEN c.slug = 'dat' THEN 'land'
                        WHEN c.slug = 'mat-bang' THEN 'premises'
                        WHEN c.slug = 'van-phong' THEN 'office'
                        ELSE 'warehouse'
                    END
                    FROM categories c
                    WHERE c.id = p.category_id
                )
                WHERE p.property_type IS NULL
            ");
        } catch (\Exception $e) {
            // Prevent failure during clean seeding
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['transaction_type']);
            $table->dropIndex(['property_type']);
            $table->dropIndex(['province']);
            $table->dropIndex(['district']);
            $table->dropIndex(['price']);
            $table->dropIndex(['area']);
            $table->dropIndex(['status']);

            $table->dropColumn(['transaction_type', 'property_type', 'province']);
        });
    }
};
