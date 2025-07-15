<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_meta', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('country_code');
            $table->string('number');

            $table->string('telegram')->nullable();
            $table->string('viber')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();

            $table->string('company_name')->nullable();
            $table->enum('company_type', ['management', 'agency', 'broker', 'other'])->nullable();
            $table->enum('role_in_company', ['owner', 'co-owner', 'manager', 'operator', 'other'])->nullable();
            $table->string('website_link')->nullable();

            $table->string('street_address')->nullable();
            $table->string('street_address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();

            $table->text('about_agency')->nullable();
            $table->enum('heard_about_us', [
                'partner_recommendation',
                'search',
                'social_media',
                'ad',
                'conference',
                'other'
            ])->nullable();
            $table->date('birthday')->nullable();
            $table->text('additional_comments')->nullable();
            $table->boolean('rent')->default(false)->nullable();
            $table->boolean('real_estate')->default(false)->nullable();
            $table->boolean('service')->default(false)->nullable();
            $table->string('tax_id')->nullable();
            $table->string('iban')->nullable();
            $table->string('beneficiary')->nullable();
            $table->foreignId('accountant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('approval_status', ['pending', 'approved', 'declined'])->default('pending');
            $table->boolean('disabled')->default(false);
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_meta');
    }
};
