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
        $externalProductions = DB::connection('external_db')->table('productions')->get();

        foreach ($externalProductions as $externalProduction) {
            Production::updateOrCreate(
                ['id' => $externalProduction->id], // Assuming each production has a unique ID
                ['title' => $externalProduction->title]
            );
        }
    }
}
