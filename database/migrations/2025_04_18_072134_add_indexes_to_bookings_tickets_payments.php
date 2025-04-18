<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['screening_id', 'status'], 'idx_bookings_screening_status');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('issued_at', 'idx_tickets_issued_at');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status','expires_at'], 'idx_payments_status_expires');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_screening_status');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_issued_at');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_status_expires');
        });
    }
};
