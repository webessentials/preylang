<?php

namespace App\Console\Commands;

use App\Models\Impact;
use Illuminate\Console\Command;

class DeleteNullDateImpactCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'we:delete-null-date-impacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all impacts having no dates';

    /**
     * Create a new command instance.
     *
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
        $impacts = Impact::whereReportDate(null);
        $impactNumbers = $impacts->count();
        if ($impactNumbers > 0) {
            if ($this->confirm('You are about to delete ' . $impactNumbers . ' impact(s) having no report date. Do you wish to continue?', true)) {
                $impacts->unsearchable();
                $impacts->delete();
                $this->info('------------ ' . $impactNumbers . ' impact(s) deleted ------------');
            } else {
                $this->info('INFO: process aborted');
            }
        } else {
            $this->info('INFO: nothing to delete');
        }
    }
}
