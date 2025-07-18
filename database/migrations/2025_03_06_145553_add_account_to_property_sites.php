<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_sites', function (Blueprint $table) {
            $table->string('account_id')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_sites', function (Blueprint $table) {
            $table->dropColumn('account_id');
            $table->dropColumn('api_key');
            $table->dropColumn('api_url');
        });
    }
};
