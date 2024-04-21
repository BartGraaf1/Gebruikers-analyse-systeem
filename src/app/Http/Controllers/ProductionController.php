<?php

namespace App\Http\Controllers;

use App\Models\ProductionUserAgentStats;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Production;
use App\Models\PvpProduction;
use App\Models\PvpEvent;
use App\Models\PvpViewer;
use App\Models\ProductionDailyStat;
use App\Models\PvpFragment;
use DateTime;
use DateInterval;
use DatePeriod;

class ProductionController extends Controller
{
    public function overview(Request $request)
    {
        // Fetch IDs of active productions
        $activeProductionIds = DB::table('productions')
            ->where('is_active', 1)
            ->pluck('id');

        // Fetch fragments associated with active productions
        $fragmentsCsv = DB::connection('external_db')->table('productions')
            ->whereIn('id', $activeProductionIds)
            ->pluck('fragment_start');

        // Flatten CSV strings into an array of unique fragment IDs
        $fragmentIds = $fragmentsCsv->flatMap(function ($csv) {
            return explode(',', $csv);
        })->unique()->all();


        // Define time periods
        $now = Carbon::now();
        $sevenDaysAgo = $now->copy()->subDays(6)->startOfDay();
        $thirtyDaysAgo = $now->copy()->subDays(29)->startOfDay();
        $ninetyDaysAgo = $now->copy()->subDays(89)->startOfDay();

        // Initialize an array to hold your results
        $results = [
            'views' => [
                'last_7_days' => 0,
                'last_30_days' => 0,
                'last_90_days' => 0,
            ],
            'loads' => [
                'last_7_days' => 0,
                'last_30_days' => 0,
                'last_90_days' => 0,
            ],
            'percentage_watched' => [
                '40_percent' => 0,
                '60_percent' => 0,
                '80_percent' => 0,
            ]
        ];

        // Fetch views and loads for the specified periods
        $stats = DB::table('production_daily_stats')
            ->selectRaw('
        SUM(CASE WHEN day >= ? THEN views ELSE 0 END) as views_last_7_days,
        SUM(CASE WHEN day >= ? THEN views ELSE 0 END) as views_last_30_days,
        SUM(CASE WHEN day >= ? THEN views ELSE 0 END) as views_last_90_days,
        SUM(CASE WHEN day >= ? THEN `load` ELSE 0 END) as loads_last_7_days,
        SUM(CASE WHEN day >= ? THEN `load` ELSE 0 END) as loads_last_30_days,
        SUM(CASE WHEN day >= ? THEN `load` ELSE 0 END) as loads_last_90_days',
                [$sevenDaysAgo, $thirtyDaysAgo, $ninetyDaysAgo, $sevenDaysAgo, $thirtyDaysAgo, $ninetyDaysAgo]
            )
            ->whereIn('fragment_id', $fragmentIds)
            ->first();

        // Assign the fetched values to the results array
        $results['views']['last_7_days'] = $stats->views_last_7_days;
        $results['views']['last_30_days'] = $stats->views_last_30_days;
        $results['views']['last_90_days'] = $stats->views_last_90_days;
        $results['loads']['last_7_days'] = $stats->loads_last_7_days;
        $results['loads']['last_30_days'] = $stats->loads_last_30_days;
        $results['loads']['last_90_days'] = $stats->loads_last_90_days;

        // For percentage watched till 40%, 60%, and 80% of the last 30 days
        $stats = DB::table('production_daily_stats')
            ->selectRaw('
        SUM(views) as total_views_last_30_days,
        SUM(watched_till_percentage_40) as watched_40,
        SUM(watched_till_percentage_60) as watched_60,
        SUM(watched_till_percentage_80) as watched_80
        ')
            ->whereIn('fragment_id', $fragmentIds)
            ->where('day', '>=', $thirtyDaysAgo)
            ->first();


        // Assign the fetched values to the results array
        $totalViews = $stats->total_views_last_30_days;
        $results['percentage_watched']['40_percent'] = $totalViews ? round(($stats->watched_40 / $totalViews) * 100,2) : 0;
        $results['percentage_watched']['60_percent'] = $totalViews ? round(($stats->watched_60 / $totalViews) * 100,2) : 0;
        $results['percentage_watched']['80_percent'] = $totalViews ? round(($stats->watched_80 / $totalViews) * 100,2) : 0;


        $search = $request->query('search');

        if ($search) {
            // If there is a search term, perform the search on title and description
            $productions = Production::query()
                ->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->orderBy('id', 'desc') // Order by id in ascending order
                ->paginate(10)
                ->appends(request()->except('page')); // This will append all query parameters except 'page' to the pagination links
            } else {
                // Otherwise, just paginate all productions
            $productions = \App\Models\Production::query()
                ->orderBy('id', 'desc') // Order by id in ascending order
                ->paginate(10);
        }

        // Return the index view with the results
        return view('production/productions-overview', compact('productions', 'results'));
    }


    public function productionStatistics(Request $request, $productionId)
    {

        // Check if Date isset else take the last 14 days
        $dateRange = $request->input('statistics_date');
        if ($dateRange) {
            [$startDate, $endDate] = explode(' to ', $dateRange);
            // Convert the string dates to Carbon instances
            $startDate = Carbon::createFromFormat('Y-m-d', trim($startDate))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', trim($endDate))->endOfDay();
        } else {
            $endDate = now();
            $startDate = now()->subDays(14);
        }

        // Ensure dates are in 'Y-m-d' format for the database query
        $startDate = $startDate->format('Y-m-d');
        $endDate = $endDate->format('Y-m-d');

        // Fetch the production details from the local database
        $production = Production::findOrFail($productionId);

        // Fetch the corresponding `fragment_start` from the external database
        $pvpProduction = PvpProduction::find($productionId);
        if (!$pvpProduction) {
            abort(404, 'Production not found in external database.');
        }

        // Fetch all connected fragments to the production
        $allFragments = PvpFragment::where('production_id', $productionId)->get();

        // Check if specific fragments are selected in the request, otherwise use fragment_start
        $fragmentIds = $request->input('statistics_fragments', explode(',', $pvpProduction->fragment_start));

        // Ensure $fragmentIds is always an array
        if (!is_array($fragmentIds)) {
            $fragmentIds = [$fragmentIds];
        }

        // Normalize $fragmentIds to ensure it's an array even if only one fragment ID is provided
        $fragmentIds = is_array($fragmentIds) ? $fragmentIds : [$fragmentIds];

        $productionDailyStats = ProductionDailyStat::fetchAndProcessStats($startDate, $endDate, $fragmentIds);
        $productionDailyStatsProcessedAverages = $productionDailyStats['productionDailyStatsProcessedAverages'];
        $productionDailyStatsWithEmptyDays = $productionDailyStats['productionDailyStatsWithEmptyDays'];
        $productionDailyStatsWatchedTillPercentageTotals = $productionDailyStats['productionDailyStatsWatchedTillPercentageTotals'];


        $productionUserAgentStats = ProductionUserAgentStats::fetchAndProcessStats($startDate, $endDate, $fragmentIds);
        $browserStats = $productionUserAgentStats['browserStats'];
        $osStats = $productionUserAgentStats['osStats'];

        $labels = $productionDailyStatsWithEmptyDays->pluck('day')->map(function ($date) {
            // Convert string to Carbon instance firsts
            return Carbon::parse($date)->format('M d'); // Formatting date as 'Mon 01'
        });

        $totalViews = $productionDailyStatsWithEmptyDays->pluck('total_views');
        $totalLoad = $productionDailyStatsWithEmptyDays->pluck('total_load');

        // Pass these arrays to your view
        return view('production.production-statistics', compact('production','allFragments', 'fragmentIds', 'startDate', 'endDate', 'labels', 'totalViews', 'totalLoad', 'productionDailyStatsProcessedAverages', 'productionDailyStatsWatchedTillPercentageTotals', 'productionUserAgentStats', 'browserStats', 'osStats'));

    }
}
