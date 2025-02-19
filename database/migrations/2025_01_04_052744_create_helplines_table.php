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
        Schema::create('helplines', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id")->nullable();
            $table->string("first_name");
            $table->string("last_name");
            $table->integer('animal_id');
            $table->integer('disease_id')->nullable();
            $table->string('contact_number');
            $table->string('query_municipality');
            $table->string('query_barangay');
            $table->integer('affected_count');
            $table->integer('death_count')->nullable();
            $table->string('image_path')->nullable();
            $table->string('query_address')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->text('other_info')->nullable();
            $table->string("status")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helplines');
    }
};
