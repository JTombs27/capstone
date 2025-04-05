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
        Schema::table('helplines', function (Blueprint $table) {
            //
            $table->string("farm_type")->default('Backyard');
            $table->date("start_date")->default('1970-01-01');
            $table->string("middle_name")->nullable();
            $table->string("suffix")->nullable();
            $table->string("owner_firstname")->default('');
            $table->string("owner_lastname")->default('');
            $table->string("owner_middlename")->nullable();
            $table->string("owner_suffix")->nullable();
            $table->string("owner_contactnumber")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helplines', function (Blueprint $table) {
            //
            $table->string("farm_type")->default('Backyard');
            $table->date("start_date")->default('1970-01-01');
            $table->string("middle_name")->nullable();
            $table->string("suffix")->nullable();
            $table->string("owner_firstname")->default('');
            $table->string("owner_lastname")->default('');
            $table->string("owner_middlename")->nullable();
            $table->string("owner_suffix")->nullable();
        });
    }
};
