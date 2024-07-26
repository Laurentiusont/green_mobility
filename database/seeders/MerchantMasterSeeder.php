<?php

namespace Database\Seeders;

use App\Models\MerchantMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MerchantMaster::create([
            "name" => "Alfamart",
            "guid" => "9c9e0522-6e07-4ea3-b832-40b66edc5986"
        ]);
    }
}
