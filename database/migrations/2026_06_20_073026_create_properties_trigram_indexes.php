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
        // 1. Enable pg_trgm extension (required for trigram search in PostgreSQL)
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm;');

        // 2. Create GIN (Generalized Inverted Index) trigram indexes for partial substring matching
        DB::statement('CREATE INDEX IF NOT EXISTS properties_title_trgm_idx ON properties USING gin (title gin_trgm_ops);');
        DB::statement('CREATE INDEX IF NOT EXISTS properties_city_trgm_idx ON properties USING gin (city gin_trgm_ops);');
        DB::statement('CREATE INDEX IF NOT EXISTS properties_district_trgm_idx ON properties USING gin (district gin_trgm_ops);');
        DB::statement('CREATE INDEX IF NOT EXISTS properties_ward_trgm_idx ON properties USING gin (ward gin_trgm_ops);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS properties_title_trgm_idx;');
        DB::statement('DROP INDEX IF EXISTS properties_city_trgm_idx;');
        DB::statement('DROP INDEX IF EXISTS properties_district_trgm_idx;');
        DB::statement('DROP INDEX IF EXISTS properties_ward_trgm_idx;');
    }
};
