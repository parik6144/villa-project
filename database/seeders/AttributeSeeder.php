<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttributeGroup;
use App\Models\Attribute;
use App\Models\PropertyType;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        PropertyType::insert([
            ['name' => 'Villa', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'House', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Flat', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Maisonette', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Detached House', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Apartment', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Studio', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Bungalow', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Loft', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Building', 'residential' => true, 'commercial' => true, 'land' => false],
            ['name' => 'Complex', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Farm', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Boat', 'residential' => true, 'commercial' => false, 'land' => false],
            ['name' => 'Hotel', 'residential' => false, 'commercial' => true, 'land' => false],
            ['name' => 'Inside of the City', 'residential' => false, 'commercial' => false, 'land' => true],
            ['name' => 'Outside of the City', 'residential' => false, 'commercial' => false, 'land' => true],
        ]);

        // Seed attribute groups
        $attributeGroups = [
            ['name' => 'Facilities'],
            ['name' => 'Distances']
        ];

        foreach ($attributeGroups as $groupData) {
            AttributeGroup::insert($groupData);
        }

        // Seed attributes
        $attributes = [
            [
                'group_id' => 1,
                'name' => 'Amenities',
                'type' => 'multi-checkbox',
                'options' => json_encode([
                    'air_conditioning' => 'Air Conditioning',
                    'attic' => 'Attic',
                    'baby_bed' => 'Baby bed',
                    'baby_chair' => 'Baby chair',
                    'balcony' => 'Balcony',
                    'barbeque' => 'Barbeque',
                    'breakfast' => 'Breakfast (10 / person)',
                    'ceiling_fan' => 'Ceiling Fan',
                    'central_heating' => 'Central Heating',
                    'cleaning_products' => 'Cleaning products',
                    'coffee_maker' => 'Coffee Maker',
                    'dining_room' => 'Dining Room',
                    'disabled_facilities' => 'Disabled facilities',
                    'dishwasher' => 'Dishwasher',
                    'dryer' => 'Dryer',
                    'electric_boiler' => 'Electric Boiler',
                    'electric_generator' => 'Electric Generator',
                    'espresso_machine' => 'Espresso Machine',
                    'filter_coffee_machine' => 'Filter coffee Machine',
                    'fireplace' => 'Fireplace',
                    'free_parking' => 'Free Parking',
                    'free_parking_secured' => 'Free Parking (Secured)',
                    'free_wifi' => 'Free Wi-Fi',
                    'freezer' => 'Freezer',
                    'full_equipped_kitchen' => 'Full Equipped Kitchen',
                    'furnished' => 'Furnished',
                    'furnished_terrace' => 'Furnished Terrace',
                    'gaming_console' => 'Gaming Console',
                    'garden' => 'Garden',
                    'guest_house' => 'Guest House',
                    'gym' => 'Gym',
                    'hair_dryer' => 'Hair Dryer',
                    'hairdryer' => 'Hairdryer',
                    'hammam' => 'Hammam',
                    'hand_antiseptic' => 'Hand Antiseptic',
                    'high_speed_wifi' => 'High speed Wi-Fi',
                    'home_cinema_room' => 'Home Cinema room',
                    'iron' => 'Iron',
                    'ironing_board' => 'Ironing board',
                    'jacuzzi' => 'Jacuzzi',
                    'juicer' => 'Juicer',
                    'kitchen_utensils' => 'Kitchen utensils',
                    'laundry' => 'Laundry',
                    'lawn' => 'Lawn',
                    'lounge_area' => 'Lounge area',
                    'microwave' => 'Microwave',
                    'netflix' => 'Netflix',
                    'office' => 'Office',
                    'outdoor_dining_area' => 'Outdoor dining area',
                    'outside_kitchen' => 'Outside Kitchen',
                    'patio' => 'Patio',
                    'pets_allowed' => 'Pets Friendly',
                    'pets_allowed_small' => 'Pets Friendly (small)',
                    'pets_not_allowed' => 'Pets not allowed',
                    'ping_pong' => 'Ping Pong',
                    'playstation_5' => 'Play Station 5',
                    'porch' => 'Porch',
                    'projector_tv' => 'Projector TV',
                    'refrigerator' => 'Refrigerator',
                    'sat_tv' => 'Sat TV',
                    'sauna' => 'Sauna',
                    'sea_view' => 'Sea view',
                    'security_system' => 'Security System',
                    'seif' => 'Seif',
                    'small_garden' => 'Small Garden',
                    'smart_tv' => 'Smart TV',
                    'spa' => 'Spa',
                    'stereo' => 'Stereo',
                    'sun_beds' => 'Sun Beds',
                    'swimming_pool_heating' => 'Swimming Pool Heating',
                    'swimming_pool_shared' => 'Swimming Pool (Shared)',
                    'swimming_pool_private' => 'Swimming Pool (Private)',
                    'toaster' => 'Toaster',
                    'towels_linen' => 'Towels/Linen',
                    'tv' => 'TV',
                    'washing_machine' => 'Washing machine',
                    'mosquito_net' => 'Mosquito net in all frames',
                    'sunbeds_umbrella' => 'Sunbeds and umbrella for the beach',
                ]),
                'is_required' => false,
            ],
            [
                'group_id' => 1,
                'name' => 'Kitchens',
                'type' => 'multi-checkbox',
                'options' => json_encode([
                    'basic_condiments'=>'Basic condiments',
                    'coffee_machine'=>'Coffee machine',
                    'dish_washer'=>'Dish-washer',
                    'electric kettle'=>'Electric kettle',
                    'electric_oven'=>'Electric oven',
                    'electric_stove_cooktop'=>'Electric stove / cooktop',
                    'freezer'=>'Freezer',
                    'fridge'=>'Fridge',
                    'gas_oven'=>'Gas oven',
                    'gas_stove_cooktop'=>'Gas stove / cooktop',
                    'kitchenware'=>'Kitchenware',
                    'microwave'=>'Microwave',
                    'toaster'=>'Toaster',
                    'wood_fired_oven'=>'Wood fired oven'
                ]),
                'is_required' => false,
            ],
            
            [
                'group_id' => 2,
                'name' => 'Beach distance',
                'type' => 'number',
                'description' => 'Distance from the beach (m)',
                'is_required' => false,
            ],
            [
                'group_id' => 2,
                'name' => 'Infrastructure distance',
                'type' => 'number',
                'description' => 'Distance to infrastructure (km)',
                'is_required' => false,
            ],
            [
                'group_id' => 2,
                'name' => 'Airport distance',
                'type' => 'number',
                'description' => 'Distance from the airport (km)',
                'is_required' => false,
            ],
            [
                'group_id' => 2,
                'name' => 'Supermarket distance',
                'type' => 'number',
                'description' => 'Distance to the supermarket (km)',
                'is_required' => false,
            ],
            [
                'group_id' => 2,
                'name' => 'Restaurant distance',
                'type' => 'number',
                'description' => 'Distance to restaurants (km)',
                'is_required' => false,
            ],
            [
                'group_id' => 2,
                'name' => 'Marina distance',
                'type' => 'number',
                'description' => 'Distance to the marina (km)',
                'is_required' => false,
            ],
            [
                'group_id' => 2,
                'name' => 'Police Office distance',
                'type' => 'number',
                'description' => 'Distance to the Police Office (km)',
                'is_required' => false,
            ],
            [
                'group_id' => 2,
                'name' => 'Medical Office distance',
                'type' => 'number',
                'description' => 'Distance to the Medical Office (km)',
                'is_required' => false,
            ],
	    [
                'group_id' => 2,
                'name' => 'School distance',
                'type' => 'number',
                'description' => 'Distance to the school (km)',
                'is_required' => false,
            ],
	    [
                'group_id' => 2,
                'name' => 'Entertainment facility distance',
                'type' => 'number',
                'description' => 'Distance to entertainment facility (km)',
                'is_required' => false,
            ],           
        ];

        foreach ($attributes as $attributeData) {
            Attribute::insert($attributeData);
        }
    }
}
