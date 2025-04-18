<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            $table->unique(['booking_id', 'seat_id'], 'uq_booking_seat');
        });
    }

    public function down()
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            $table->dropUnique('uq_booking_seat');
        });
    }
};
