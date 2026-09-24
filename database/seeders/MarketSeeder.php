<?php

namespace Database\Seeders;

use App\Models\Market;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        // ONE market only — Nyarugenge Mini Market
        Market::create([
            'name'        => 'Nyarugenge Mini Market',
            'district'    => 'Nyarugenge',
            'city'        => 'Kigali',
            'country'     => 'Rwanda',
            'description' => 'The official mini market of Nyarugenge district, Kigali. '
                           . 'Serving the local community with fresh produce, meat, dairy, '
                           . 'fish, cereals, and general goods. '
                           . 'Equipped with IoT sensors for real-time hygiene monitoring.',
            'is_active'   => true,
        ]);
    }
}
