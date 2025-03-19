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
        Schema::create('sms_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer("helpline_id");
            $table->unsignedBigInteger('farm_id');
            $table->string('phone_number');
            $table->text('message');
            $table->string('status')->default('pending'); // Default status
            $table->string('message_id')->nullable(); // Store Semaphore Message ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_notifications');
    }
};
