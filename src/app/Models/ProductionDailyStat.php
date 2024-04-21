<?php

namespace App\Models;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class ProductionDailyStat extends Model
{
    protected $fillable = ['day'];

    protected $casts = [
        'day' => 'date',  // Ensuring 'day' is treated as a date
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);  // Call the parent constructor

        // Immediately fill attributes if provided
        $this->fill($attributes);
    }

    /**
     * Fetches and processes video statistics for a given range and set of fragment IDs,
     * and returns both the complete and raw statistics.
     *
     * @param string $startDate Start date of the period to analyze.
     * @param string $endDate End date of the period to analyze.
     * @param array $fragmentIds IDs of fragments to include in the stats.
     * @return array An array containing both processed and raw statistics.
     * @throws \Exception
     */
    public static function fetchAndProcessStats(string $startDate, string $endDate, array $fragmentIds): array
    {
        $allDates = collect(new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day')
        ))->map(function ($date) {
            return $date->format('Y-m-d');
        });

        $productionDailyStats = self::whereIn('fragment_id', $fragmentIds)
            ->whereDate('day', '>=', $startDate)
            ->whereDate('day', '<=', $endDate)
            ->groupBy('day')
            ->selectRaw('day, SUM(views) as total_views, SUM(`load`) as total_load,
                                    SUM(watched_till_percentage_0) as avg_watched_0,
                                    SUM(watched_till_percentage_10) as avg_watched_10,
                                    SUM(watched_till_percentage_20) as avg_watched_20,
                                    SUM(watched_till_percentage_30) as avg_watched_30,
                                    SUM(watched_till_percentage_40) as avg_watched_40,
                                    SUM(watched_till_percentage_50) as avg_watched_50,
                                    SUM(watched_till_percentage_60) as avg_watched_60,
                                    SUM(watched_till_percentage_70) as avg_watched_70,
                                    SUM(watched_till_percentage_80) as avg_watched_80,
                                    SUM(watched_till_percentage_90) as avg_watched_90,
                                    SUM(watched_till_percentage_100) as avg_watched_100')
            ->get()
            ->keyBy('day');

        $productionDailyStatsWithEmptyDays = $allDates->mapWithKeys(function ($date) use ($productionDailyStats) {
            $date = $date . " 00:00:00";
            if ($productionDailyStats->has($date)) {
                return [$date => $productionDailyStats->get($date)];
            } else {
                // Create a new instance and manually set attributes
                $stat = new ProductionDailyStat;
                $stat->day = $date;
                $stat->total_views = 0;
                $stat->total_load = 0;
                $stat->avg_watched_0 = 0;
                $stat->avg_watched_10 = 0;
                $stat->avg_watched_20 = 0;
                $stat->avg_watched_30 = 0;
                $stat->avg_watched_40 = 0;
                $stat->avg_watched_50 = 0;
                $stat->avg_watched_60 = 0;
                $stat->avg_watched_70 = 0;
                $stat->avg_watched_80 = 0;
                $stat->avg_watched_90 = 0;
                $stat->avg_watched_100 = 0;
                $stat->average_viewing_percentage = 0;
                $stat->exists = false;  // Ensure the model is treated as not persisted
                return [$date => $stat];
            }
        });


        $productionDailyStatsProcessedAverages  = $productionDailyStatsWithEmptyDays->map(function ($stat) {
            $total_viewers = $stat->avg_watched_0; // Start with the total number of viewers at 0%
            $weighted_sum = 0;
            $last_count = $total_viewers;

            // Iterate through each 10% interval up to 100%
            for ($i = 10; $i <= 100; $i += 10) {
                $current_key = "avg_watched_$i";
                $current_viewers = isset($stat->$current_key) ? $stat->$current_key : 0;

                // Calculate the number of viewers who stopped at this specific interval
                $viewers_stopped = $last_count - $current_viewers;
                $weighted_sum += $viewers_stopped * ($i - 10); // Apply the midpoint of the previous range

                // Update the last_count for the next iteration
                $last_count = $current_viewers;

                // Break early if no viewers reach further milestones
                if ($current_viewers == 0) {
                    break;
                }
            }

            // Include the last set of viewers who reached the final milestone
            if ($last_count > 0) {
                $weighted_sum += $last_count * ($i - 10); // Use the last valid milestone
            }

            // Calculate the average viewing percentage
            $stat->average_viewing_percentage = $total_viewers > 0 ? $weighted_sum / $total_viewers : 0;

            return $stat;
        });

        // Calculate the total for each watched percentage
        $productionDailyStatsWatchedTillPercentageTotals = [];
        for ($i = 0; $i <= 100; $i += 10) {
            $key = "avg_watched_$i";
            $productionDailyStatsWatchedTillPercentageTotals[$key] = $productionDailyStats->sum($key);
        }

        return [
            'productionDailyStatsProcessedAverages' => $productionDailyStatsProcessedAverages,
            'productionDailyStatsWithEmptyDays' => $productionDailyStatsWithEmptyDays,
            'productionDailyStatsWatchedTillPercentageTotals'=>$productionDailyStatsWatchedTillPercentageTotals
        ];
    }

    public static function fetchDailyStats($days): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $data = self::whereBetween('day', [$startDate, $endDate])
            ->groupBy('fragment_id')
            ->selectRaw('fragment_id, SUM(views) as total_views, SUM(`load`) as total_load,
                        SUM(watched_till_percentage_0) as total_watched_0,
                        SUM(watched_till_percentage_10) as total_watched_10,
                        SUM(watched_till_percentage_20) as total_watched_20,
                        SUM(watched_till_percentage_30) as total_watched_30,
                        SUM(watched_till_percentage_40) as total_watched_40,
                        SUM(watched_till_percentage_50) as total_watched_50,
                        SUM(watched_till_percentage_60) as total_watched_60,
                        SUM(watched_till_percentage_70) as total_watched_70,
                        SUM(watched_till_percentage_80) as total_watched_80,
                        SUM(watched_till_percentage_90) as total_watched_90,
                        SUM(watched_till_percentage_100) as total_watched_100')
            ->get();

        // Convert the collection to an associative array with `fragment_id` as the key
        $results = [];
        foreach ($data as $item) {
            $results[$item->fragment_id] = [
                'total_views' => $item->total_views,
                'total_load' => $item->total_load,
                'total_watched_0' => $item->total_watched_0,
                'total_watched_10' => $item->total_watched_10,
                'total_watched_20' => $item->total_watched_20,
                'total_watched_30' => $item->total_watched_30,
                'total_watched_40' => $item->total_watched_40,
                'total_watched_50' => $item->total_watched_50,
                'total_watched_60' => $item->total_watched_60,
                'total_watched_70' => $item->total_watched_70,
                'total_watched_80' => $item->total_watched_80,
                'total_watched_90' => $item->total_watched_90,
                'total_watched_100' => $item->total_watched_100
            ];
        }

        return $results;
    }


}
