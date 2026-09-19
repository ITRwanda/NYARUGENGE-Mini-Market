<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendorUsers = User::where('role', 'vendor')
            ->orderBy('id')
            ->get();

        $categories = [
            'Vegetables & Fruits',
            'Meat & Poultry',
            'Dairy Products',
            'Fish & Seafood',
            'Grains & Cereals',
            'Spices & Condiments',
            'Bakery & Pastry',
            'General Goods',
            'Beverages',
            'Vegetables & Fruits',
            'Meat & Poultry',
            'Fish & Seafood',
        ];

        $businessNames = [
            'Nyirabeza Fresh Produce',
            'Ndayishimiye Meat Shop',
            'Uwase Dairy Corner',
            'Hakizimana Fish Market',
            'Mukansanga Grain Store',
            'Nkurunziza Spice Hub',
            'Ingabire Bakery',
            'Niyomugabo General Store',
            'Mukamurenzi Beverages',
            'Uwimana Vegetable Stand',
            'Nyiramana Poultry Corner',
            'Bizumuremyi Seafood Fresh',
        ];

        $marketIds = [1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3];

        foreach ($vendorUsers as $index => $user) {
            Vendor::create([
                'user_id'       => $user->id,
                'market_id'     => $marketIds[$index] ?? 1,
                'vendor_code'   => 'VND-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'business_name' => $businessNames[$index] ?? 'Vendor ' . ($index + 1),
                'phone'         => $user->phone,
                'food_category' => $categories[$index] ?? 'General Goods',
                'is_active'     => true,
            ]);
        }
    }
}
