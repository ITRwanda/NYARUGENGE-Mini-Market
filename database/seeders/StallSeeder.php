<?php

namespace Database\Seeders;

use App\Models\Market;
use App\Models\Stall;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class StallSeeder extends Seeder
{
    public function run(): void
    {
        $market = Market::where('name', 'like', '%Nyarugenge%')->firstOrFail();

        $vendors = Vendor::where('market_id', $market->id)
            ->orderBy('id')
            ->get()
            ->keyBy('vendor_code');

        /*
         * Stall layout — vendors can occupy multiple stalls
         *
         * VND-0001  Nyirabeza Fresh Produce     → 3 stalls (A, B, C)
         * VND-0002  Ndayishimiye Meat Shop       → 2 stalls (A, B)
         * VND-0003  Uwase Dairy Corner           → 1 stall  (B)
         * VND-0004  Hakizimana Fish Market       → 2 stalls (C, D)
         * VND-0005  Mukansanga Grain Store       → 1 stall  (D)
         * VND-0006  Nkurunziza Spice Hub         → 1 stall  (A)
         * VND-0007  Ingabire Bakery              → 2 stalls (C, D)
         * VND-0008  Niyomugabo General Store     → 1 stall  (B)
         *
         * Total: 13 stalls
         */
        $stallDefs = [
            ['number' => 'S-001', 'section' => 'Section A', 'vendor' => 'VND-0001', 'description' => 'Main vegetable display — fresh produce'],
            ['number' => 'S-002', 'section' => 'Section A', 'vendor' => 'VND-0001', 'description' => 'Fruit stand — seasonal fruits'],
            ['number' => 'S-003', 'section' => 'Section B', 'vendor' => 'VND-0001', 'description' => 'Overflow produce storage'],
            ['number' => 'S-004', 'section' => 'Section A', 'vendor' => 'VND-0002', 'description' => 'Primary butchery counter'],
            ['number' => 'S-005', 'section' => 'Section B', 'vendor' => 'VND-0002', 'description' => 'Poultry display and cold storage'],
            ['number' => 'S-006', 'section' => 'Section B', 'vendor' => 'VND-0003', 'description' => 'Dairy products — milk, cheese, yoghurt'],
            ['number' => 'S-007', 'section' => 'Section C', 'vendor' => 'VND-0004', 'description' => 'Fresh fish display'],
            ['number' => 'S-008', 'section' => 'Section D', 'vendor' => 'VND-0004', 'description' => 'Smoked and dried fish'],
            ['number' => 'S-009', 'section' => 'Section D', 'vendor' => 'VND-0005', 'description' => 'Grains, rice, beans and cereals'],
            ['number' => 'S-010', 'section' => 'Section A', 'vendor' => 'VND-0006', 'description' => 'Spices, herbs and condiments'],
            ['number' => 'S-011', 'section' => 'Section C', 'vendor' => 'VND-0007', 'description' => 'Bakery — bread and rolls'],
            ['number' => 'S-012', 'section' => 'Section D', 'vendor' => 'VND-0007', 'description' => 'Pastry and confectionery'],
            ['number' => 'S-013', 'section' => 'Section B', 'vendor' => 'VND-0008', 'description' => 'General goods and household items'],
        ];

        foreach ($stallDefs as $def) {
            $vendor = $vendors[$def['vendor']] ?? null;

            Stall::create([
                'market_id'    => $market->id,
                'vendor_id'    => $vendor?->id,
                'stall_number' => $def['number'],
                'section'      => $def['section'],
                'description'  => $def['description'],
                'is_active'    => true,
            ]);
        }
    }
}
