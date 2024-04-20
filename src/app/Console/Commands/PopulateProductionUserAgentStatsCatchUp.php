<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateProductionUserAgentStatsCatchUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-production-user-agent-stats-catch-up{days=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Catches up to the user agent data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToCatchUp = (int) $this->argument('days');
        $this->info("Starting catch-up for the past $daysToCatchUp days...");

        $query = "
        SELECT
            DATE(eve.created) AS event_day,
            eve.fragment_id,
            SUM(CASE WHEN vie.user_agent LIKE '%iPhone%' THEN 1 ELSE 0 END) AS iPhone_views,
            SUM(CASE WHEN vie.user_agent LIKE '%iPad%' THEN 1 ELSE 0 END) AS iPad_views,
            SUM(CASE WHEN vie.user_agent LIKE '%iPod%' THEN 1 ELSE 0 END) AS iPod_views,
            SUM(CASE WHEN vie.user_agent LIKE '%Mac%OS%' OR vie.user_agent LIKE '%iMac%' THEN 1 ELSE 0 END) AS Mac_views,
            SUM(CASE WHEN vie.user_agent LIKE '%android%' AND vie.user_agent NOT LIKE '%mobile%' THEN 1 ELSE 0 END) AS Android_tablet_views,
            SUM(CASE WHEN vie.user_agent LIKE '%android%' THEN 1 ELSE 0 END) AS Android_views,
            SUM(CASE WHEN vie.user_agent LIKE '%CrOS%' OR vie.user_agent LIKE '%Chrome OS%' THEN 1 ELSE 0 END) AS Chrome_OS_views,
            SUM(CASE WHEN vie.user_agent LIKE '%NT 10.0%' THEN 1 ELSE 0 END) AS windows_10_11_views,
            SUM(CASE WHEN vie.user_agent LIKE '%NT 6.3%' THEN 1 ELSE 0 END) AS windows_8_1_views,
            SUM(CASE WHEN vie.user_agent LIKE '%NT 6.2%' THEN 1 ELSE 0 END) AS windows_8_views,
            SUM(CASE WHEN vie.user_agent LIKE '%NT 6.1%' THEN 1 ELSE 0 END) AS windows_7_views,
            SUM(CASE WHEN vie.user_agent LIKE '%NT 6.0%' THEN 1 ELSE 0 END) AS windows_vista_views,
            SUM(CASE WHEN vie.user_agent LIKE '%NT 5.1%' THEN 1 ELSE 0 END) AS windows_xp_views,
            SUM(CASE WHEN vie.user_agent LIKE '%NT 5.0%' THEN 1 ELSE 0 END) AS windows_2000_views,
            SUM(CASE WHEN vie.user_agent LIKE '%linux%' THEN 1 ELSE 0 END) AS Linux_views,
            SUM(CASE WHEN vie.user_agent LIKE '%FreeBSD%' THEN 1 ELSE 0 END) AS FreeBSD_views,
            SUM(CASE WHEN vie.user_agent LIKE '%Edge%' THEN 1 ELSE 0 END) AS Edge_views,
            SUM(CASE WHEN vie.user_agent LIKE '%OPR%' OR vie.user_agent LIKE '%Opera%' THEN 1 ELSE 0 END) AS Opera_views,
            SUM(CASE WHEN vie.user_agent LIKE '%Chrome%' AND vie.user_agent NOT LIKE '%Edge%' AND vie.user_agent NOT LIKE '%OPR%' AND vie.user_agent NOT LIKE '%SamsungBrowser%' THEN 1 ELSE 0 END) AS Google_Chrome_views,
            SUM(CASE WHEN vie.user_agent LIKE '%Safari%' AND vie.user_agent NOT LIKE '%Chrome%' AND vie.user_agent NOT LIKE '%OPR%' THEN 1 ELSE 0 END) AS Apple_Safari_views,
            SUM(CASE WHEN vie.user_agent LIKE '%Firefox%' THEN 1 ELSE 0 END) AS Mozilla_Firefox_views,
            SUM(CASE WHEN vie.user_agent LIKE '%SamsungBrowser%' THEN 1 ELSE 0 END) AS Samsung_Internet_views,
            SUM(CASE WHEN vie.user_agent LIKE '%MSIE%' THEN 1 ELSE 0 END) AS Internet_Explorer_views,
            SUM(CASE WHEN vie.user_agent LIKE '%Brave%' THEN 1 ELSE 0 END) AS Brave_views,
            SUM(CASE WHEN vie.user_agent LIKE '%Vivaldi%' THEN 1 ELSE 0 END) AS Vivaldi_views,
            SUM(CASE WHEN vie.user_agent LIKE '%DuckDuckGo%' THEN 1 ELSE 0 END) AS DuckDuckGo_views,
            SUM(CASE WHEN vie.user_agent LIKE '%Outlook%' THEN 1 ELSE 0 END) AS Outlook_views
        FROM
            `pvp_events_2023_10` eve
        LEFT JOIN
            `pvp_viewers_2023_10` vie ON eve.viewer_id = vie.id
        WHERE
            eve.created BETWEEN ? AND ?
        GROUP BY
            event_day, eve.fragment_id
    ";

        for ($i = 1; $i <= $daysToCatchUp; $i++) {
            $date = now()->subDays($i)->toDateString();
            $this->info("Processing $date...");

            // Execute the query and fetch the results
            $stats = DB::connection('external_db')->select($query, [$date . ' 00:00:00', $date . ' 23:59:59']);

            foreach ($stats as $stat) {
                // Prepare data for insert/update according to your table's column names
                $dataToUpdate = [
                    'day' => $stat->event_day,
                    'fragment_id' => $stat->fragment_id,
                    'iPhone_views' => $stat->iPhone_views,
                    'iPad_views' => $stat->iPad_views,
                    'iPod_views' => $stat->iPod_views,
                    'Mac_views' => $stat->Mac_views,
                    'Android_tablet_views' => $stat->Android_tablet_views,
                    'Android_views' => $stat->Android_views,
                    'Chrome_OS_views' => $stat->Chrome_OS_views,
                    'windows_10_11_views' => $stat->windows_10_11_views,
                    'windows_8_1_views' => $stat->windows_8_1_views,
                    'windows_8_views' => $stat->windows_8_views,
                    'windows_7_views' => $stat->windows_7_views,
                    'windows_vista_views' => $stat->windows_vista_views,
                    'windows_xp_views' => $stat->windows_xp_views,
                    'windows_2000_views' => $stat->windows_2000_views,
                    'Linux_views' => $stat->Linux_views,
                    'FreeBSD_views' => $stat->FreeBSD_views,
                    'Edge_views' => $stat->Edge_views,
                    'Opera_views' => $stat->Opera_views,
                    'Google_Chrome_views' => $stat->Google_Chrome_views,
                    'Apple_Safari_views' => $stat->Apple_Safari_views,
                    'Mozilla_Firefox_views' => $stat->Mozilla_Firefox_views,
                    'Samsung_Internet_views' => $stat->Samsung_Internet_views,
                    'Internet_Explorer_views' => $stat->Internet_Explorer_views,
                    'Brave_views' => $stat->Brave_views,
                    'Vivaldi_views' => $stat->Vivaldi_views,
                    'DuckDuckGo_views' => $stat->DuckDuckGo_views,
                    'Outlook_views' => $stat->Outlook_views,
                    'updated_at' => now(),
                ];

                // Insert or Update the local database
                DB::table('production_user_agent_stats')->updateOrInsert(
                    [
                        'fragment_id' => $stat->fragment_id,
                        'day' => $stat->event_day,
                    ],
                    $dataToUpdate
                );
            }

            $this->info('Daily stats for productions have been updated.');
        }
    }
}
