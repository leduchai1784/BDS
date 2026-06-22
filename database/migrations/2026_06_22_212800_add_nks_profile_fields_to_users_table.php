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
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->nullable()->after('name');
            $table->string('lastname')->nullable()->after('firstname');
            $table->integer('gender')->default(0)->after('avatar');
            $table->string('dob')->nullable()->after('gender');
            $table->string('pob')->nullable()->after('dob');
            $table->string('id_number')->nullable()->after('pob');
            $table->string('id_date')->nullable()->after('id_number');
            $table->string('id_place')->nullable()->after('id_date');
            $table->string('cccd_front')->nullable()->after('id_place');
            $table->string('cccd_back')->nullable()->after('cccd_front');
            $table->string('add_street')->nullable()->after('cccd_back');
            $table->string('add_ward')->nullable()->after('add_street');
            $table->string('add_district')->nullable()->after('add_ward');
            $table->string('add_province')->nullable()->after('add_district');
            $table->string('zalo_id')->nullable()->after('add_province');
            $table->string('zalo_key')->nullable()->after('zalo_id');
            $table->text('intro')->nullable()->after('zalo_key');
            $table->string('website')->nullable()->after('intro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'firstname', 'lastname', 'gender', 'dob', 'pob',
                'id_number', 'id_date', 'id_place', 'cccd_front', 'cccd_back',
                'add_street', 'add_ward', 'add_district', 'add_province',
                'zalo_id', 'zalo_key', 'intro', 'website'
            ]);
        });
    }
};
