<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeValueColumnTypeInPropertyAttributes extends Migration
{
    public function up()
    {
        Schema::table('property_attributes', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('property_attributes', function (Blueprint $table) {
            $table->string('value', 255)->nullable()->change();
        });
    }
}
