<?php

namespace Database\Seeders;

use App\Models\PropertySites;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertySetting;
use App\Models\Season;
use App\Models\LicenceType;
use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\UserAndAgentMetaSeeder;
use App\Models\BasicRateCommission;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'agent']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'property_owner']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'company']);


        $user1 = User::factory()->create([
            'name' => 'admin',
            'email' => 'web@dits.md',
            'password' => bcrypt('9&e$#R2@%##j!39&'),
            'email_verified_at' => now(),
        ]);
        $user1->assignRole('admin');

        $this->call([
            AttributeSeeder::class,
            UserAndAgentMetaSeeder::class,
        ]);

        PropertySetting::create([]);
        BasicRateCommission::create(['commission_type' => 'Property owner','revenue_level' => '0-12000','commission_rate' => 20.75, 'taxes' => 5.00, 'agent_commission' => 2.00, 'service' => 1.00]);
        BasicRateCommission::create(['commission_type' => 'Property owner','revenue_level' => '12001-35000','commission_rate' => 10.00, 'taxes' => 8.00, 'agent_commission' => 2.00, 'service' => 1.00]);
        BasicRateCommission::create(['commission_type' => 'Property owner','revenue_level' => '35001-100000','commission_rate' => 10.00, 'taxes' => 10.00, 'agent_commission' => 2.00, 'service' => 1.00 ]);
        BasicRateCommission::create(['commission_type' => 'Management company','revenue_level' => '0-12000','commission_rate' => 15.00, 'taxes' => 5.00, 'agent_commission'=> 2.00, 'service' => 1.00]);
        BasicRateCommission::create(['commission_type' => 'Management company','revenue_level' => '12001-35000','commission_rate' => 35.50, 'taxes' => 8.00, 'agent_commission'=> 2.00, 'service' => 1.00]);
        BasicRateCommission::create(['commission_type' => 'Management company','revenue_level' => '35001-100000','commission_rate' => 45.50, 'taxes' => 10.00, 'agent_commission'=> 2.00, 'service' => 1.00]);
        

        $sites = [
            [
                'site' => 'Site 1',
                'url' => 'https://site1.com',
            ],
            [
                'site' => 'Site 2',
                'url' => 'https://site2.com',
            ],
        ];

        foreach ($sites as $site) {
            PropertySites::updateOrCreate(
                ['url' => $site['url']],
                $site
            );
        }

        $seasons = [
            ['date_from' => '2025-05-01', 'date_to' => '2025-05-31', 'season_title' => 'May Season 2025'],
            ['date_from' => '2025-06-01', 'date_to' => '2025-06-30', 'season_title' => 'June Season 2025'],
            ['date_from' => '2025-07-01', 'date_to' => '2025-07-31', 'season_title' => 'July Season 2025'],
            ['date_from' => '2025-08-01', 'date_to' => '2025-08-31', 'season_title' => 'August Season 2025'],
            ['date_from' => '2025-09-01', 'date_to' => '2025-09-30', 'season_title' => 'September Season 2025'],
            ['date_from' => '2024-05-01', 'date_to' => '2024-05-31', 'season_title' => 'May Season 2024'],
            ['date_from' => '2024-06-01', 'date_to' => '2024-06-30', 'season_title' => 'June Season 2024'],
            ['date_from' => '2024-07-01', 'date_to' => '2024-07-31', 'season_title' => 'July Season 2024'],
            ['date_from' => '2024-08-01', 'date_to' => '2024-08-31', 'season_title' => 'August Season 2024'],
            ['date_from' => '2024-09-01', 'date_to' => '2024-09-30', 'season_title' => 'September Season 2024'],
        ];

        foreach ($seasons as $season) {
            Season::create($season);
        }

        LicenceType::create(['name'=>'EOT licence (ESL - MHTE)']);
        LicenceType::create(['name'=>'My property is exempt']);
        LicenceType::create(['name'=>'Notify Business Number (MAG)']);
        LicenceType::create(['name'=>'Short term rental leasing']);

        Property::factory(10)->create([
            'user_id' => 1,
            'property_type_id' => 1,
        ]);

    }
}
