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
        Schema::create('s3_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->string('secret')->nullable();
            $table->string('token')->nullable();
            $table->string('region')->default('auto')->nullable();
            $table->string('bucket')->nullable();
            $table->string('endpoint')->nullable();
            $table->boolean('use_path_style_endpoint')->default(false)->nullable();
            $table->string('visibility')->default('public')->nullable();
            $table->string('url')->nullable();
            $table->boolean('throw')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s3_settings');
    }
};
