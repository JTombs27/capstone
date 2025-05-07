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
            $table->integer("sample_count")->nullable();
            $table->integer("positive_count")->nullable();
            $table->dateTime("date_reported")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helplines', function (Blueprint $table) {
            //
            $table->integer("sample_count")->nullable();
            $table->integer("positive_count")->nullable();
            $table->dateTime("date_reported")->nullable();
        });
    }
};
