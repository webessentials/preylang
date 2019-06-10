<?php

namespace App\Console\Commands;

use App\Models\Impact;
use Illuminate\Console\Command;

class DeleteExcludedImpactsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'we:delete-excluded-impacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all impacts marked as excluded';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $impacts = Impact::whereExcluded(true);
        $excludedImpactNumbers = $impacts->count();
        if ($excludedImpactNumbers > 0) {
            if ($this->confirm('You are about to delete ' . $excludedImpactNumbers . ' excluded impact(s). Do you wish to continue?', true)) {
                $impacts->unsearchable();
                $impacts->delete();
                $this->info('------------ ' . $excludedImpactNumbers . ' impact(s) deleted ------------');
            } else {
                $this->info('INFO: process aborted');
            }
        } else {
            $this->info('INFO: no excluded impacts to delete');
        }
    }
}
