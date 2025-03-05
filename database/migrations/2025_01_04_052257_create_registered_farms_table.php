<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registered_farms', function (Blueprint $table) {
            $table->id();
            $table->string('owner_firstname');
            $table->string('owner_middle')->nullable();
            $table->string('owner_lastname');
            $table->string('owner_suffix')->nullable();

            $table->string('farm_municipality');
            $table->string('farm_barangay');
            $table->string('farm_address');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->json('location')->nullable();
            $table->string('farm_type');
            $table->string('farm_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registered_farms');
    }
};
