<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Category::firstOrCreate(
            ['slug' => 'dat'],
            [
                'name' => 'Đất',
                'description' => 'Đất thổ cư, đất nền dự án, đất nông nghiệp cho thuê'
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'kho-nha-xuong'],
            [
                'name' => 'Kho, nhà xưởng',
                'description' => 'Kho bãi, nhà xưởng, mặt bằng sản xuất cho thuê'
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Category::whereIn('slug', ['dat', 'kho-nha-xuong'])->delete();
    }
};
