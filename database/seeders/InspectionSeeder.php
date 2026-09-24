<?php

namespace Database\Seeders;

use App\Models\Inspection;
use App\Models\InspectionItem;
use App\Models\Market;
use App\Models\Stall;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InspectionSeeder extends Seeder
{
    private array $checklistItems = [
        'Food storage temperature within safe range',
        'Proper food labeling and date markings',
        'Absence of pests or rodents',
        'Clean and sanitized work surfaces',
        'Proper waste disposal in place',
        'Vendor personal hygiene compliance',
        'Raw and cooked food properly separated',
        'Adequate ventilation in stall',
        'Fire safety equipment present and accessible',
        'Valid health permit visibly displayed',
    ];

    public function run(): void
    {
        $market     = Market::where('name', 'like', '%Nyarugenge%')->firstOrFail();
        $inspectors = User::where('role', 'inspector')->get();
        $stalls     = Stall::where('market_id', $market->id)->get();

        if ($inspectors->isEmpty()) {
            return;
        }

        foreach ($stalls as $sIdx => $stall) {
            $inspector = $inspectors[$sIdx % $inspectors->count()];

            // 2 inspections per stall, spread over last 30 days
            for ($round = 0; $round < 2; $round++) {
                $daysAgo = rand(1 + ($round * 10), 10 + ($round * 10));
                $status  = $round === 0 ? 'completed' : ['completed', 'follow_up', 'pending'][rand(0, 2)];

                $inspection = Inspection::create([
                    'market_id'       => $market->id,
                    'stall_id'        => $stall->id,
                    'inspector_id'    => $inspector->id,
                    'inspection_date' => Carbon::now()->subDays($daysAgo),
                    'status'          => $status,
                    'general_notes'   => $this->notes($status),
                ]);

                foreach ($this->checklistItems as $itemText) {
                    $itemStatus = $this->itemStatus($status);
                    InspectionItem::create([
                        'inspection_id' => $inspection->id,
                        'item'          => $itemText,
                        'status'        => $itemStatus,
                        'notes'         => $itemStatus === 'fail'
                            ? 'Requires immediate correction before next market day.'
                            : null,
                    ]);
                }
            }
        }
    }

    private function notes(string $status): string
    {
        return match ($status) {
            'completed'  => 'Routine inspection completed. Stall meets hygiene standards.',
            'follow_up'  => 'Follow-up required. Issues were identified and vendor was notified.',
            'pending'    => 'Inspection scheduled but not yet conducted.',
            default      => 'Inspection on record.',
        };
    }

    private function itemStatus(string $inspectionStatus): string
    {
        if ($inspectionStatus === 'pending') {
            return 'not_applicable';
        }
        $roll = rand(1, 10);
        if ($inspectionStatus === 'follow_up') {
            return $roll <= 5 ? 'pass' : ($roll <= 8 ? 'fail' : 'not_applicable');
        }
        return $roll <= 8 ? 'pass' : ($roll <= 9 ? 'fail' : 'not_applicable');
    }
}
