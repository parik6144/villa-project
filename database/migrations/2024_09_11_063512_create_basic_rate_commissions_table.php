<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('basic_rate_commissions', function (Blueprint $table) {
            $table->id();
            
            $table->enum('commission_type', [
                'Management company',
                'Property owner'
            ])->notNullable();
            $table->string('revenue_level')->notNullable(); 
            $table->decimal('taxes', 5, 2)->notNullable(); 
            $table->decimal('agent_commission', 5, 2)->nullable();
            $table->decimal('service', 5, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->notNullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basic_rate_commissions');
    }
};
