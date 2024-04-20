<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;

class ProductionUserAgentStats extends Model
{
    protected $casts = [
        'day' => 'date',  // Ensuring 'day' is treated as a date
    ];

    /**
     * Get stats with all dates filled between a start and end date.
     *
     * @param array $fragmentIds
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getFilledStats(array $fragmentIds, string $startDate, string $endDate)
    {
        // Fetch data from the database
        $stats = self::whereIn('fragment_id', $fragmentIds)
            ->whereBetween('day', [$startDate, $endDate])
            ->groupBy('day')
            ->selectRaw('day as raw_day,
                         SUM(iPhone_views) as total_iPhone_views,
                         SUM(iPad_views) as total_iPad_views,
                         SUM(iPod_views) as total_iPod_views,
                         SUM(Mac_OS_views) as total_Mac_OS_views,
                         SUM(Mac_views) as total_Mac_views,
                         SUM(Android_tablet_views) as total_Android_tablet_views,
                         SUM(Android_views) as total_Android_views,
                         SUM(Chrome_OS_views) as total_Chrome_OS_views,
                         SUM(Windows_10_11_views) as total_Windows_10_11_views,
                         SUM(Windows_8_views) as total_Windows_8_views,
                         SUM(Windows_8_1_views) as total_Windows_8_1_views,
                         SUM(Windows_7_views) as total_Windows_7_views,
                         SUM(Windows_Vista_views) as total_Windows_Vista_views,
                         SUM(Windows_XP_views) as total_Windows_XP_views,
                         SUM(Windows_2000_views) as total_Windows_2000_views,
                         SUM(Linux_views) as total_Linux_views,
                         SUM(FreeBSD_views) as total_FreeBSD_views,
                         SUM(Other_OS_views) as total_Other_OS_views')
            ->get()
            ->mapWithKeys(function ($item) {
                // Normalize the date to ensure no time is included
                $date = (new DateTime($item->raw_day))->format('Y-m-d');
                $item->day = $date;  // Update day attribute to normalized date
                return [$date => $item];
            });

        // Generate all dates in the range
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day')
        );

        // Fill missing dates with zeros
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if (!isset($stats[$dateStr])) {
                $stats[$dateStr] = (object) [
                    'day' => $dateStr,
                    'total_iPhone_views' => 0,
                    'total_iPad_views' => 0,
                    'total_iPod_views' => 0,
                    'total_Mac_OS_views' => 0,
                    'total_Mac_views' => 0,
                    'total_Android_tablet_views' => 0,
                    'total_Android_views' => 0,
                    'total_Chrome_OS_views' => 0,
                    'total_Windows_10_11_views' => 0,
                    'total_Windows_8_views' => 0,
                    'total_Windows_8_1_views' => 0,
                    'total_Windows_7_views' => 0,
                    'total_Windows_Vista_views' => 0,
                    'total_Windows_XP_views' => 0,
                    'total_Windows_2000_views' => 0,
                    'total_Linux_views' => 0,
                    'total_FreeBSD_views' => 0,
                    'total_Other_OS_views' => 0
                ];
            }
        }

        return $stats;
    }
}
