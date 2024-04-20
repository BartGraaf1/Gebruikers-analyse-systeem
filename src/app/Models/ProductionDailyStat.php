<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;

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

    public static function getProcessedStats(array $fragmentIds, string $startDate, string $endDate)
    {
        // Fetch stats from the local database
        $productionDailyStats = self::whereIn('fragment_id', $fragmentIds)
            ->whereBetween('day', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE_FORMAT(day, "%Y-%m-%d")'))
            ->selectRaw('DATE_FORMAT(day, "%Y-%m-%d") as day, SUM(views) as total_views, SUM(`load`) as total_load,
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

        // Generate all dates in the range
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day')
        );

        // Fill missing dates with zeros
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if (!isset($productionDailyStats[$dateStr])) {
                $productionDailyStats[$dateStr] = (object) [
                    'day' => $dateStr,
                    'total_views' => 0,
                    'total_load' => 0,
                    'avg_watched_0' => 0,
                    'avg_watched_10' => 0,
                    'avg_watched_20' => 0,
                    'avg_watched_30' => 0,
                    'avg_watched_40' => 0,
                    'avg_watched_50' => 0,
                    'avg_watched_60' => 0,
                    'avg_watched_70' => 0,
                    'avg_watched_80' => 0,
                    'avg_watched_90' => 0,
                    'avg_watched_100' => 0
                ];
            }
        }

        // Process stats to calculate average viewing percentage
        $processedStats = collect($productionDailyStats)->map(function ($stat) {
            $total_viewers = $stat->avg_watched_0; // Start with the total number of viewers at 0%
            $weighted_sum = 0;
            $last_count = $total_viewers;

            // Iterate through each 10% interval up to 100%
            for ($i = 10; $i <= 100; $i += 10) {
                $current_key = "avg_watched_$i";
                $current_viewers = $stat->$current_key ?? 0;

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

        return [$productionDailyStats, $processedStats];
    }
}
