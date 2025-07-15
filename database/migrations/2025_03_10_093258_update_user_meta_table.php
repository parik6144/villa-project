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
        Schema::table('user_meta', function (Blueprint $table) {
            $table->string('mobile_number')->nullable()->after('number');
            $table->unsignedBigInteger('user_planyo_id')->unique()->nullable()->after('street_address_line_2');
            $table->dateTime('registration_time')->nullable()->after('user_planyo_id');
            $table->integer('reservation_count')->default(0)->after('registration_time');
            $table->dateTime('last_reservation')->nullable()->after('reservation_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_meta', function (Blueprint $table) {
            $table->dropColumn([
                'mobile_number',
                'user_planyo_id',
                'registration_time',
                'reservation_count',
                'last_reservation'
            ]);
        });
    }
};
