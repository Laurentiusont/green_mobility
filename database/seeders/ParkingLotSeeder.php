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
                'name' => 'Maranatha',
                'country' => 'Indonesia',
                'city' => 'Bandung',
                'latitude' => -6.886658715860169,
                'longitude' => 107.5799600138004,
                'phone_number' => '0298312222',
                'available_spots' => 250,
            ],
            [
                'name' => 'Bakjer',
                'country' => 'Indonesia',
                'city' => 'Bandung',
                'latitude' => -6.884747601121666,
                'longitude' => 107.58254072608966,
                'phone_number' => null,
                'available_spots' => 100,
            ],
            [
                'name' => 'Sari Ater Kamboti',
                'country' => 'Indonesia',
                'city' => 'Bandung',
                'latitude' => -6.883851525699362,
                'longitude' => 107.58007905970545,
                'phone_number' => null,
                'available_spots' => 50,
            ],
            [
                'name' => 'WU Tower',
                'country' => 'Indonesia',
                'city' => 'Bandung',
                'latitude' => -6.890831370940659,
                'longitude' => 107.5785740095686,
                'phone_number' => '0298328500',
                'available_spots' => 125,
            ],
            [
                'name' => 'Satlantas Polres',
                'country' => 'Indonesia',
                'city' => 'Bandung',
                'latitude' => -6.895723772745682,
                'longitude' => 107.57657690022646,
                'phone_number' => null,
                'available_spots' => 1050,
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






