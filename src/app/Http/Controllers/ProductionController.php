<?php

namespace App\Http\Controllers;

use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            $productions = \App\Models\Production::query()
                ->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->paginate(10)
                ->appends(request()->except('page')); // This will append all query parameters except 'page' to the pagination links
            } else {
                // Otherwise, just paginate all productions
            $productions = \App\Models\Production::paginate(10);
        }

        // Return the index view with the results
        return view('production/productions-overview', compact('productions', 'results'));
    }


    public function productionStatistics(Request $request, $production){

        // Fetch the production details
        $production = Production::findOrFail($production);

        // Fetch fragments associated with this production
        // This assumes you have a method to directly query the external database or a local representation of it
        $fragments = DB::connection('external_db') // 'external' is a placeholder for your actual external database connection name
        ->table('fragments')
            ->where('production_id', $production)
            ->get();
        
        // Return the fragments and their statistics to the view
        return view('production/production-statistics', compact('fragments'));
    }
}
