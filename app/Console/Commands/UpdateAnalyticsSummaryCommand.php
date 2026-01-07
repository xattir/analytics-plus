<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateAnalyticsSummaryTables;
use Carbon\Carbon;

class UpdateAnalyticsSummaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:update-summary {--date=} {--site-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update analytics summary tables for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::yesterday();
        $siteId = $this->option('site-id');
        
        $this->info("Updating analytics summary for date: {$date->format('Y-m-d')}");
        
        if ($siteId) {
            $this->info("Site ID: {$siteId}");
        }
        
        $job = new UpdateAnalyticsSummaryTables($date->format('Y-m-d'), $siteId);
        $job->handle();
        
        $this->info('Summary update completed successfully!');
    }
}
