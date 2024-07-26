<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MerchantLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alfamarts = [
            [
                'name' => 'Alfamart Diponegoro 3',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.298801945983172,
                'longitude' => 110.4836632052919,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Diponegoro 2',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.30612011454293,
                'longitude' => 110.48819810500801,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.311210466987295,
                'longitude' => 110.4908570545763,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Diponegoro 4',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.315418931774008,
                'longitude' => 110.49346688025906,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart lingkar selatan',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.295110869285845,
                'longitude' => 110.47532617840226,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart kemiri',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.316410839570922,
                'longitude' => 110.50202442526951,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Pattimura 2',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.314255556834777,
                'longitude' => 110.50640928727077,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Dr. Moewardi',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.332066342506442,
                'longitude' => 110.50957464708769,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.32033449220736,
                'longitude' => 110.49341594432165,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.329381614896727,
                'longitude' => 110.50068728234048,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.330894866048561,
                'longitude' => 110.49487667183448,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.320688473958223,
                'longitude' => 110.47520520670905,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Bright SPBU Sidomukti',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.327127856132072,
                'longitude' => 110.4791978657981,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.338808530017466,
                'longitude' => 110.48663796132216,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Argoboga',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.349386222110722,
                'longitude' => 110.50854176678793,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Marditomo',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.337105701729936,
                'longitude' => 110.51754203448755,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Tingkir Tengah',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.362019959301136,
                'longitude' => 110.52009693029052,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart Joko Tingkir',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.3580398871242245,
                'longitude' => 110.51618303166818,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
            [
                'name' => 'Alfamart',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.361364088968178,
                'longitude' => 110.51344850366435,
                'phone_number' => null,
                'merchant_master_guid' => '9c9e0522-6e07-4ea3-b832-40b66edc5986',
            ],
        ];

        foreach ($alfamarts as $alfamart) {
            DB::table('merchant_locations')->insert([
                'guid' => Str::uuid()->toString(),
                'name' => $alfamart['name'],
                'country' => $alfamart['country'],
                'city' => $alfamart['city'],
                'latitude' => $alfamart['latitude'],
                'longitude' => $alfamart['longitude'],
                'phone_number' => $alfamart['phone_number'],
                'merchant_master_guid' => $alfamart['merchant_master_guid'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    }






