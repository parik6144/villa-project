<?php

namespace Database\Seeders;

use App\Models\CompanyEmployee;
use App\Models\UserMeta;
use App\Models\CompanyMeta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserAndAgentMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('12345'),
            ]);

            $user->assignRole('agent');

            UserMeta::create([
                'user_id' => $user->id,
                'company_name' => "Company {$i}",
                'company_type' => 'agency',
                'role_in_company' => 'manager',
                'country_code' => 'US',
                'website_link' => "https://company{$i}.example.com",
                'number' => '123-456-7890',
                'about_agency' => "This is a description for Company {$i}.",
                'heard_about_us' => 'search',
                'additional_comments' => "Additional comments for Company {$i}.",
                'rent' => true,
                'real_estate' => false,
                'service' => true,
                'parent_id' => null,
            ]);
        }

        $user = User::create([
            'name' => "Owner",
            'email' => "owner@example.com",
            'password' => Hash::make('12345'),
        ]);

        $user->assignRole('property_owner');

        UserMeta::create([
            'user_id' => $user->id,
            'country_code' => 'US',
            'number' => '123-456-7890',
            'heard_about_us' => 'search',
            'additional_comments' => "Additional comments for Company.",
            'parent_id' => null,
        ]);



        $accountant = User::create([
            'name' => "Accountant",
            'email' => "accountant@example.com",
            'password' => Hash::make('12345'),
        ]);

        $accountant->assignRole('accountant');

        UserMeta::create([
            'user_id' => $accountant->id,
            'country_code' => 'US',
            'website_link' => "https://company.example.com",
            'number' => '123-456-7890',
            'heard_about_us' => 'search',
            'parent_id' => null,
        ]);


        $manager = User::create([
            'name' => "Manager",
            'email' => "manager@example.com",
            'password' => Hash::make('12345'),
        ]);

        $manager->assignRole('manager');

        UserMeta::create([
            'user_id' => $manager->id,
            'company_name' => "Company Manager",
            'company_type' => 'agency',
            'role_in_company' => 'manager',
            'country_code' => 'US',
            'website_link' => "https://company.example.com",
            'number' => '123-456-7890',
            'about_agency' => "This is a description for Company.",
            'heard_about_us' => 'search',
            'additional_comments' => "Additional comments for Company.",
            'parent_id' => null,
        ]);

        $company = User::createCompany([
            'name' => 'LTD Real Estate',
            'email' => 'relal.estate@example.com',
        ]);
        CompanyMeta::create([
            'user_id' => $company->id,
            'type' => 'Property Management Company',
            'about' => 'About LTD Real Estate',
            'phone' => '123-456-7890',
            'country' => 'AR',
            'city' => 'Cordoba',
            'address' => 'my Street',
            'address2' => '123',
            'state' => 'Cordoba',
            'postal_code' => '12345',
            'website' => 'https://company.example.com',
            'tax_id' => '123456789',
            'iban' => 'AR123456789123456789',
            'beneficiary' => 'User',
        ]);

        CompanyEmployee::saveCompanyEmployeeData($company->id, [
            [
                'employee_user_id' => 3,
                'role' => 'Owner',
            ],
            [
                'employee_user_id' => 2,
                'role' => 'Agent',
            ]
        ]);
    }
}
