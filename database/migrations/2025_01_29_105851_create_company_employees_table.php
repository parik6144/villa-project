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
        Schema::create('company_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_user_id')->constrained('users')->onDelete('cascade'); // Компания
            $table->foreignId('employee_user_id')->constrained('users')->onDelete('cascade'); // Сотрудник
            $table->string('role');
            $table->timestamps();

            // Запрещаем дублирование сотрудника в одной компании
            $table->unique(['company_user_id', 'employee_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_employees');
    }
};
