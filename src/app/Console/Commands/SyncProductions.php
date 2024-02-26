<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // Add this line
use App\Models\Production;

class SyncProductions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-productions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch productions from external database
        $externalProductions = DB::connection('external_db')->table('productions')->get();

        // Fetch only IDs from external productions for comparison
        $externalProductionIds = $externalProductions->pluck('id')->all();

        // Iterate over each external production and update or create in the local database
        foreach ($externalProductions as $externalProduction) {
            Production::updateOrCreate(
                ['id' => $externalProduction->id], // Unique identifier for each production
                ['title' => $externalProduction->title] // Data to update or create
            );
        }

        // Find local productions that are not in the external productions list and delete them
        Production::whereNotIn('id', $externalProductionIds)->delete();
    }

}
