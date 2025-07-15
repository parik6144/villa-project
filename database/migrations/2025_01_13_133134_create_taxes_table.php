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
        Schema::create('property_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->enum('tax_type', [
                'City tax', 
                'Destination fee', 
                'Goods and services tax', 
                'Government tax', 
                'Local tax', 
                'Resort fee', 
                'Tax', 
                'Tourism fee', 
                'VAT (Value Added Tax)',
            ]);
            $table->enum('fee_basis', [
                '% of Rental Amount', 
                'Per adult / day', 
                'Per adult / stay', 
                'Per adult / week', 
                'Per day', 
                'Per person / day', 
                'Per person / stay', 
                'Per person / week', 
                'Per stayPer week',
            ]);
            $table->decimal('amount', 10, 2);
            $table->boolean('is_percent')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
