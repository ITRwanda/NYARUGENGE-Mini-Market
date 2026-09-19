<?php

namespace Database\Seeders;

use App\Models\Inspection;
use App\Models\InspectionItem;
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
        'Clean and sanitized surfaces',
        'Proper waste disposal',
        'Vendor personal hygiene compliance',
        'Food separation (raw and cooked)',
        'Adequate ventilation in stall',
        'Fire safety equipment present',
        'Valid health permit displayed',
    ];

    public function run(): void
    {
        $inspectors = User::where('role', 'inspector')->get();
        $stalls     = Stall::all();
        $statuses   = ['completed', 'completed', 'completed', 'pending', 'follow_up'];

        foreach ($stalls as $sIndex => $stall) {
            $inspector = $inspectors[$sIndex % $inspectors->count()];

            // Create 2-3 inspections per stall over the last 30 days
            $numInspections = rand(2, 3);

            for ($i = 0; $i < $numInspections; $i++) {
                $daysAgo = rand(1, 30);
                $status  = $statuses[($sIndex + $i) % count($statuses)];

                $inspection = Inspection::create([
                    'market_id'       => $stall->market_id,
                    'stall_id'        => $stall->id,
                    'inspector_id'    => $inspector->id,
                    'inspection_date' => Carbon::now()->subDays($daysAgo),
                    'status'          => $status,
                    'general_notes'   => $this->getGeneralNotes($status),
                ]);

                // Add checklist items
                foreach ($this->checklistItems as $itemText) {
                    $itemStatus = $this->getItemStatus($status);
                    InspectionItem::create([
                        'inspection_id' => $inspection->id,
                        'item'          => $itemText,
                        'status'        => $itemStatus,
                        'notes'         => $itemStatus === 'fail' ? 'Requires immediate attention and correction.' : null,
                    ]);
                }
            }
        }
    }

    private function getGeneralNotes(string $status): string
    {
        return match ($status) {
            'completed'  => 'Inspection completed successfully. All critical items passed.',
            'pending'    => 'Scheduled inspection pending vendor availability.',
            'follow_up'  => 'Follow-up required for items flagged in previous inspection.',
            default      => 'Routine inspection conducted.',
        };
    }

    private function getItemStatus(string $inspectionStatus): string
    {
        if ($inspectionStatus === 'completed') {
            $rand = rand(1, 10);
            if ($rand <= 7) return 'pass';
            if ($rand <= 9) return 'fail';
            return 'not_applicable';
        }

        if ($inspectionStatus === 'follow_up') {
            $rand = rand(1, 10);
            if ($rand <= 5) return 'pass';
            if ($rand <= 8) return 'fail';
            return 'not_applicable';
        }

        // pending
        return 'not_applicable';
    }
}
