<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
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

        // Fetch views per month for active fragments, limited to the last 12 months
        $viewsPerMonth = DB::table('production_daily_stats')
            ->selectRaw('YEAR(day) as year, MONTH(day) as month, MONTHNAME(day) as month_name, SUM(views) as total_views')
            ->whereIn('fragment_id', $fragmentIds)
            ->where('day', '>=', now()->subMonths(12)->startOfMonth())
            ->groupBy(DB::raw('YEAR(day)'), DB::raw('MONTH(day)'), DB::raw('MONTHNAME(day)'))
            ->orderBy(DB::raw('YEAR(day)'), 'asc')
            ->orderBy(DB::raw('MONTH(day)'), 'asc')
            ->get();

        // Generate a collection of the last 12 months with zeros for missing data
        $months = collect(range(1, 12))->map(function ($item) {
            $date = now()->subMonths(12 - $item);
            return (object) [
                'year' => $date->year,
                'month' => $date->month,
                'month_name' => $date->format('F'),
                'total_views' => 0,
            ];
        });

        // Merge the database results with the generated months, overriding zeros where data exists
        $viewsPerMonth = $months->map(function ($month) use ($viewsPerMonth) {
            $data = $viewsPerMonth->firstWhere(function ($value) use ($month) {
                return $value->year == $month->year && $value->month == $month->month;
            });
            return $data ? $data : $month;
        });

        // Total Productions
        $totalProductions = DB::table('productions')->where('is_active', 1)->count();

        // Current Period Calculations
        $viewsLastDayCurrent = DB::table('production_daily_stats')
            ->whereIn('fragment_id', $fragmentIds)
            ->where('day', '=', now()->subDay()->startOfDay()->toDateString())
            ->sum('views');

        $viewsLastWeekCurrent = DB::table('production_daily_stats')
            ->whereIn('fragment_id', $fragmentIds)
            ->whereBetween('day', [now()->subWeek()->startOfWeek()->startOfDay()->toDateString(), now()->subWeek()->endOfWeek()->endOfDay()->toDateString()])
            ->sum('views');

        $viewsLastMonthCurrent = DB::table('production_daily_stats')
            ->whereIn('fragment_id', $fragmentIds)
            ->whereBetween('day', [now()->subMonth()->startOfMonth()->startOfDay()->toDateString(), now()->subMonth()->endOfMonth()->endOfDay()->toDateString()])
            ->sum('views');

        // Previous Period Calculations
        $viewsLastDayPrevious = DB::table('production_daily_stats')
            ->whereIn('fragment_id', $fragmentIds)
            ->where('day', '=', now()->subDays(2)->startOfDay()->toDateString())
            ->sum('views');

        $viewsLastWeekPrevious = DB::table('production_daily_stats')
            ->whereIn('fragment_id', $fragmentIds)
            ->whereBetween('day', [now()->subWeeks(2)->startOfWeek()->startOfDay()->toDateString(), now()->subWeeks(2)->endOfWeek()->endOfDay()->toDateString()])
            ->sum('views');

        $viewsLastMonthPrevious = DB::table('production_daily_stats')
            ->whereIn('fragment_id', $fragmentIds)
            ->whereBetween('day', [now()->subMonths(2)->startOfMonth()->startOfDay()->toDateString(), now()->subMonths(2)->endOfMonth()->endOfDay()->toDateString()])
            ->sum('views');

        // Calculate Percentage Change with rounding to two decimals
        $changeLastDay = $viewsLastDayPrevious > 0 ? round((($viewsLastDayCurrent - $viewsLastDayPrevious) / $viewsLastDayPrevious) * 100, 2) : 0;
        $changeLastWeek = $viewsLastWeekPrevious > 0 ? round((($viewsLastWeekCurrent - $viewsLastWeekPrevious) / $viewsLastWeekPrevious) * 100, 2) : 0;
        $changeLastMonth = $viewsLastMonthPrevious > 0 ? round((($viewsLastMonthCurrent - $viewsLastMonthPrevious) / $viewsLastMonthPrevious) * 100, 2) : 0;

        // Determine status for template color
        $statusLastDay = $changeLastDay >= 0 ? 'success' : 'danger';
        $statusLastWeek = $changeLastWeek >= 0 ? 'success' : 'danger';
        $statusLastMonth = $changeLastMonth >= 0 ? 'success' : 'danger';

        // Organizing data into an array named topRowStatistics
        $topRowStatistics = [
            'totalProductions'=> [
                'value'=>$totalProductions,
                'change' => '',
                'status' => ''
            ],
            'viewsLastDay' => [
                'value' => $viewsLastDayCurrent,
                'change' => $changeLastDay,
                'status' => $statusLastDay
            ],
            'viewsLastWeek' => [
                'value' => $viewsLastWeekCurrent,
                'change' => $changeLastWeek,
                'status' => $statusLastWeek
            ],
            'viewsLastMonth' => [
                'value' => $viewsLastMonthCurrent,
                'change' => $changeLastMonth,
                'status' => $statusLastMonth
            ],
        ];
        // Pass the data to the view
        return view('dashboard', compact('viewsPerMonth', 'topRowStatistics'));
    }
}
