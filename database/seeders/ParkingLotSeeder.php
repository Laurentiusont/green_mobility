<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParkingLotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Hotel Laras Asri',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.341196690946701,
                'longitude' => 110.50953047341137,
                'phone_number' => '0298312222',
                'available_spots' => 35,
            ],
            [
                'name' => 'Superindo',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.3352980026888615,
                'longitude' => 110.50605019637884,
                'phone_number' => null,
                'available_spots' => 15,
            ],
            [
                'name' => 'Ramayana',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.3249592379502655,
                'longitude' => 110.50523451738383,
                'phone_number' => null,
                'available_spots' => 20,
            ],
            [
                'name' => 'Hotel Grand Wahid',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.326020386946638,
                'longitude' => 110.50464473538149,
                'phone_number' => '0298328500',
                'available_spots' => 25,
            ],
            [
                'name' => 'Satlantas Polres',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.3169391758115,
                'longitude' => 110.4956417066247,
                'phone_number' => null,
                'available_spots' => 65,
            ],
        ];

        foreach ($locations as $location) {
            DB::table('parking_lots')->insert([
                'guid' => Str::uuid()->toString(),
                'name' => $location['name'],
                'country' => $location['country'],
                'city' => $location['city'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'phone_number' => $location['phone_number'],
                'available_spots' => $location['available_spots'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    }






