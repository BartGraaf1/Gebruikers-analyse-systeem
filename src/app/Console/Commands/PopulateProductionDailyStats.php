<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateProductionDailyStats extends Command
{
    protected $signature = 'production:populate-daily-stats';
    protected $description = 'Populate the production daily stats table with data from the external database for the previous day.';

    public function handle()
    {
        $this->info('Starting to fetch and populate daily stats for productions...');

        // Define the date for which stats should be aggregated
        $yesterday = now()->subDay()->toDateString(); // For actual runs

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

        // Fetching stats from external database
        $dailyStats = DB::connection('external_db')->table('daily')
            ->whereIn('action_id', array_values($actionIds))
            ->whereDate('date', '=', $yesterday)
            ->get();

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
                        $counts[$actionName] += $innerStat->count_unique; // Assuming count_views is what you want to aggregate
                    }
                }
            }

            // Since 'views' and 'watched_till_percentage_0' are the same, assign the same value to both
            $counts['watched_till_percentage_0'] = $counts['views'];

            // Prepare data for insert/update
            $dataToUpdate = [
                'day' => $yesterday,
                'load' => $counts['load'],
                'views' => $counts['views'],
                'watched_till_percentage_0' => $counts['watched_till_percentage_0'],
                'watched_till_percentage_10' => $counts['watched_till_percentage_10'],
                'watched_till_percentage_20' => $counts['watched_till_percentage_20'],
                'watched_till_percentage_30' => $counts['watched_till_percentage_30'],
                'watched_till_percentage_40' => $counts['watched_till_percentage_40'],
                'watched_till_percentage_50' => $counts['watched_till_percentage_50'],
                'watched_till_percentage_60' => $counts['watched_till_percentage_60'],
                'watched_till_percentage_70' => $counts['watched_till_percentage_70'],
                'watched_till_percentage_80' => $counts['watched_till_percentage_80'],
                'watched_till_percentage_90' => $counts['watched_till_percentage_90'],
                'watched_till_percentage_100' => $counts['watched_till_percentage_100'],
                'updated_at' => now(),
            ];

            // Insert or Update the local database
            DB::table('production_daily_stats')->updateOrInsert(
                [
                    'fragment_id' => $stat->fragment_id,
                    'day' => $yesterday,
                ],
                $dataToUpdate, // Data for update
            );
        }

        $this->info('Daily stats for productions have been updated.');
    }
}
