<?php

namespace Database\Seeders;

use App\Models\Market;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        $markets = [
            [
                'name'        => 'Nyarugenge Main Market',
                'district'    => 'Nyarugenge',
                'city'        => 'Kigali',
                'country'     => 'Rwanda',
                'description' => 'The central food market of Nyarugenge district, serving thousands of customers daily with fresh produce, meat, dairy, and general goods.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Kimironko Market',
                'district'    => 'Gasabo',
                'city'        => 'Kigali',
                'country'     => 'Rwanda',
                'description' => 'A vibrant market known for fresh vegetables, fruits, and household items, popular with local families.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Nyabugogo Market',
                'district'    => 'Nyarugenge',
                'city'        => 'Kigali',
                'country'     => 'Rwanda',
                'description' => 'Major transport and trade hub market near Nyabugogo bus terminal.',
                'is_active'   => true,
            ],
        ];

        foreach ($markets as $market) {
            Market::create($market);
        }
    }
}
