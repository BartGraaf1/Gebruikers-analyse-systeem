<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class PopulateProductionStatsCatchUp extends Command
{
    protected $signature = 'app:populate-production-stats-catchup {days=30}';
    protected $description = 'Catch up on populating production stats for a given number of past days.';

    public function handle()
    {
        $daysToCatchUp = (int) $this->argument('days');
        $this->info("Starting catch-up for the past $daysToCatchUp days...");

        // Define action IDs for stats we are interested in
        $actionIds = [
            'load' => 1,
            'views' => 1748,
            'watched_till_percentage_0' => 1748,
            'watched_till_percentage_10' => 1751,
            'watched_till_percentage_20' => 1752,
            'watched_till_percentage_30' => 1753,
            'watched_till_percentage_40' => 1754,
            'watched_till_percentage_50' => 1755,
            'watched_till_percentage_60' => 1756,
            'watched_till_percentage_70' => 1757,
            'watched_till_percentage_80' => 1758,
            'watched_till_percentage_90' => 1759,
            'watched_till_percentage_100' => 1765,
        ];

        for ($i = 1; $i <= $daysToCatchUp; $i++) {
            $date = now()->subDays($i)->toDateString();
            $this->info("Processing $date...");

            // Fetch stats for the given day
            $dailyStats = DB::connection('external_db')->table('daily')
                ->whereIn('action_id', array_values($actionIds))
                ->whereDate('date', '=', $date)
                ->get();

            // If no stats found for the day, continue to the next day
            if ($dailyStats->isEmpty()) {
                $this->info("No stats found for $date.");
                continue;
            }

            // Initialize an array to keep track of processed fragments to avoid duplicate updates
            $processedFragments = [];

            foreach ($dailyStats as $stat) {
                // Skip if this fragment has already been processed
                if (in_array($stat->fragment_id, $processedFragments)) {
                    continue;
                }

                // Add this fragment to the processed list
                $processedFragments[] = $stat->fragment_id;

                // Initialize counts for all actions
                $counts = array_fill_keys(array_keys($actionIds), 0);

                // Loop through stats again to aggregate counts for the current fragment
                foreach ($dailyStats as $innerStat) {
                    if ($innerStat->fragment_id === $stat->fragment_id) {
                        $actionName = array_search($innerStat->action_id, $actionIds);
                        if ($actionName !== false) {
                            $counts[$actionName] += $innerStat->count_unique; // Assuming count_unique is what you want to aggregate
                        }
                    }
                }

                // Prepare data for insert/update
                $dataToUpdate = [
                    'day' => $date,
                    'load' => $counts['load'],
                    'views' => $counts['views'],
                    // Include other stats here...
                    'updated_at' => now(),
                ];

                // Insert or Update the local database
                DB::table('production_daily_stats')->updateOrInsert(
                    [
                        'fragment_id' => $stat->fragment_id,
                        'day' => $date,
                    ],
                    $dataToUpdate
                );
            }

            $this->info("Finished processing $date.");
        }

        $this->info('Catch-up complete.');
    }
}
