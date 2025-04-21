<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiresAtToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // เพิ่มคอลัมน์วันหมดอายุ (datetime) หลัง booking_datetime
            $table->dateTime('expires_at')->nullable()->after('booking_datetime');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
}
