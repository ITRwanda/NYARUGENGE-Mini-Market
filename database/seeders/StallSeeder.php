<?php

namespace Database\Seeders;

use App\Models\Stall;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class StallSeeder extends Seeder
{
    public function run(): void
    {
        // Nyarugenge Main Market - 8 stalls
        $this->createStallsForMarket(1, 8, ['A', 'B']);
        // Kimironko Market - 6 stalls
        $this->createStallsForMarket(2, 6, ['C', 'D']);
        // Nyabugogo Market - 4 stalls
        $this->createStallsForMarket(3, 4, ['E']);

        // Assign vendors to stalls
        $vendors = Vendor::all();
        $stalls  = Stall::all();

        foreach ($vendors as $index => $vendor) {
            $stall = $stalls->where('market_id', $vendor->market_id)
                ->whereNull('vendor_id')
                ->first();

            if ($stall) {
                $stall->update(['vendor_id' => $vendor->id]);
            }
        }
    }

    private function createStallsForMarket(
        int $marketId,
        int $count,
        array $sections
    ): void {
        for ($i = 1; $i <= $count; $i++) {
            $section = $sections[($i - 1) % count($sections)];
            Stall::create([
                'market_id'    => $marketId,
                'vendor_id'    => null,
                'stall_number' => 'S-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'section'      => 'Section ' . $section,
                'description'  => 'Stall ' . $i . ' in section ' . $section,
                'is_active'    => true,
            ]);
        }
    }
}
