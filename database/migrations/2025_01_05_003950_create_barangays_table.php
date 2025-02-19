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
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->integer("municipality_id");
            $table->string("barangay_name");
            $table->string("color")->nullable();
            $table->integer("report_threshold")->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
    }
};
