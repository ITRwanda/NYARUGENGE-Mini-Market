<?php

namespace Database\Seeders;

use App\Models\Market;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $market = Market::where('name', 'like', '%Nyarugenge%')->firstOrFail();

        $vendorUsers = User::where('role', 'vendor')->orderBy('id')->get();

        $definitions = [
            [
                'user_index'    => 0,
                'business_name' => 'Nyirabeza Fresh Produce',
                'food_category' => 'Vegetables & Fruits',
            ],
            [
                'user_index'    => 1,
                'business_name' => 'Ndayishimiye Meat Shop',
                'food_category' => 'Meat & Poultry',
            ],
            [
                'user_index'    => 2,
                'business_name' => 'Uwase Dairy Corner',
                'food_category' => 'Dairy Products',
            ],
            [
                'user_index'    => 3,
                'business_name' => 'Hakizimana Fish Market',
                'food_category' => 'Fish & Seafood',
            ],
            [
                'user_index'    => 4,
                'business_name' => 'Mukansanga Grain Store',
                'food_category' => 'Grains & Cereals',
            ],
            [
                'user_index'    => 5,
                'business_name' => 'Nkurunziza Spice Hub',
                'food_category' => 'Spices & Condiments',
            ],
            [
                'user_index'    => 6,
                'business_name' => 'Ingabire Bakery',
                'food_category' => 'Bakery & Pastry',
            ],
            [
                'user_index'    => 7,
                'business_name' => 'Niyomugabo General Store',
                'food_category' => 'General Goods',
            ],
        ];

        foreach ($definitions as $i => $def) {
            $user = $vendorUsers[$def['user_index']] ?? null;

            Vendor::create([
                'user_id'       => $user?->id,
                'market_id'     => $market->id,
                'vendor_code'   => 'VND-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'business_name' => $def['business_name'],
                'phone'         => $user?->phone,
                'food_category' => $def['food_category'],
                'is_active'     => true,
            ]);
        }
    }
}
