<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesTable extends Migration
{
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained('attribute_groups')->onDelete('set null');
            $table->string('name');
            $table->enum('type', ['select', 'text', 'textarea', 'checkbox', 'number', 'multi-checkbox']);
            $table->text('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->text('description')->nullable();
            $table->text('default')->nullable();
            $table->string('notification')->nullable();
            $table->string('example')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attributes');
    }
}
