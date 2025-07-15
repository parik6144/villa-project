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
        Schema::create('company_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); 
            $table->string('type'); 
            $table->text('about')->nullable(); 
            $table->string('phone')->nullable(); 
            $table->string('country')->nullable(); 
            $table->string('city')->nullable(); 
            $table->string('address')->nullable(); 
            $table->string('address2')->nullable(); 
            $table->string('state')->nullable(); 
            $table->string('postal_code')->nullable(); 
            $table->string('website')->nullable(); 
            $table->string('telegram')->nullable();
            $table->string('viber')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('tax_id')->nullable(); 
            $table->string('iban')->nullable(); 
            $table->string('beneficiary')->nullable(); 
            $table->timestamps();

            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_meta');
    }
};
