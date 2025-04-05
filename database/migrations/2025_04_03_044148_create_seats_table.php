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
        //Model Seat
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('screening_room_id'); // ห้องฉายที่ที่นั่งนี้อยู่
            $table->string('seat_number'); // รหัสที่นั่ง เช่น A1, B5
            $table->string('row')->nullable(); // ถ้าอยากจัดกลุ่มแถว
            $table->string('column')->nullable(); // ถ้าต้องการ
            $table->string('seat_type')->default('regular'); // regular, VIP ฯลฯ
            $table->boolean('is_active')->default(true); // ใช้งาน/ปิด
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('screening_room_id')->references('id')->on('screening_rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
