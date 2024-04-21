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

        // Fetch data from the database
        $productionDailyUserAgents = self::whereIn('fragment_id', $fragmentIds)
            ->whereBetween('day', [$startDate, $endDate])
            ->groupBy('day')
            ->selectRaw('day,
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
                         SUM(Other_OS_views) as total_Other_OS_views,
                         SUM(Edge_views) as total_Edge_views,
                         SUM(Opera_views) as total_Opera_views,
                         SUM(Google_Chrome_views) as total_Google_Chrome_views,
                         SUM(Apple_Safari_views) as total_Apple_Safari_views,
                         SUM(Mozilla_Firefox_views) as total_Mozilla_Firefox_views,
                         SUM(Samsung_Internet_views) as total_Samsung_Internet_views,
                         SUM(Internet_Explorer_views) as total_Internet_Explorer_views,
                         SUM(Brave_views) as total_Brave_views,
                         SUM(Vivaldi_views) as total_Vivaldi_views,
                         SUM(DuckDuckGo_views) as total_DuckDuckGo_views,
                         SUM(Outlook_views) as total_Outlook_views,
                         SUM(Unknown_browser_views) as total_Unknown_browser_views,
                          SUM(Unknown_device_views) as total_Unknown_device_views')
            ->get()
            ->keyBy('day');

        $osStats = [];
        $browserStats = [];



        foreach ($allDates as $date) {
            $date = $date . " 00:00:00";
            if ($productionDailyUserAgents->has($date)) {
                $item = $productionDailyUserAgents->get($date);
                $osStats[$date] = [
                    'iPhone' => $item->total_iPhone_views,
                    'iPad' => $item->total_iPad_views,
                    'iPod' => $item->total_iPod_views,
                    'Mac' => $item->total_Mac_views,
                    'Android Tablet' => $item->total_Android_tablet_views,
                    'Android' => $item->total_Android_views,
                    'Mac OS' => $item->total_Mac_OS_views,
                    'Chrome OS' => $item->total_Chrome_OS_views,
                    'Windows 10/11' => $item->total_Windows_10_11_views,
                    'Windows 8' => $item->total_Windows_8_views,
                    'Windows 8.1' => $item->total_Windows_8_1_views,
                    'Windows 7' => $item->total_Windows_7_views,
                    'Windows Vista' => $item->total_Windows_Vista_views,
                    'Windows XP' => $item->total_Windows_XP_views,
                    'Windows 2000' => $item->total_Windows_2000_views,
                    'Linux' => $item->total_Linux_views,
                    'FreeBSD' => $item->total_FreeBSD_views,
                    'Other OS' => $item->total_Other_OS_views,
                ];
                $browserStats[$date] = [
                    'Edge' => $item->total_Edge_views,
                    'Opera' => $item->total_Opera_views,
                    'Google Chrome' => $item->total_Google_Chrome_views,
                    'Apple Safari' => $item->total_Apple_Safari_views,
                    'Mozilla Firefox' => $item->total_Mozilla_Firefox_views,
                    'Samsung Internet' => $item->total_Samsung_Internet_views,
                    'Internet Explorer' => $item->total_Internet_Explorer_views,
                    'Brave' => $item->total_Brave_views,
                    'Vivaldi' => $item->total_Vivaldi_views,
                    'DuckDuckGo' => $item->total_DuckDuckGo_views,
                    'Outlook' => $item->total_Outlook_views,
                    'Unknown Browser' => $item->total_Unknown_browser_views,
                    'Unknown Device' => $item->total_Unknown_device_views
                ];
            } else {
                // Populate with zeroes for missing days
                $osStats[$date] = [
                    'iPhone' => 0,
                    'iPad' => 0,
                    'iPod' => 0,
                    'Mac' => 0,
                    'Android Tablet' => 0,
                    'Android' => 0,
                    'Mac OS' => 0,
                    'Chrome OS' => 0,
                    'Windows 10/11' => 0,
                    'Windows 8' => 0,
                    'Windows 8.1' => 0,
                    'Windows 7' => 0,
                    'Windows Vista' => 0,
                    'Windows XP' => 0,
                    'Windows 2000' => 0,
                    'Linux' => 0,
                    'FreeBSD' => 0,
                    'Other OS' => 0,
                ];
                $browserStats[$date] = [
                    'Edge' => 0,
                    'Opera' => 0,
                    'Google Chrome' => 0,
                    'Apple Safari' => 0,
                    'Mozilla Firefox' => 0,
                    'Samsung Internet' => 0,
                    'Internet Explorer' => 0,
                    'Brave' => 0,
                    'Vivaldi' => 0,
                    'DuckDuckGo' => 0,
                    'Outlook' => 0,
                    'Unknown Browser' => 0,
                    'Unknown Device' => 0
                ];
            }
        }
        return ['osStats' => $osStats, 'browserStats' => $browserStats];
    }
}
