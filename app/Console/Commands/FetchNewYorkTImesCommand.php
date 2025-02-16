<?php

namespace App\Console\Commands;

use App\ApiFetchers\NewYorkTimesFetcher;
use App\ApiFetchers\TheGuardianApiFetcher;
use Illuminate\Console\Command;

class FetchNewYorkTImesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newyorktimes:fetch
        {--fetch-only-one-page}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch From New York Times';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fetcher = new NewYorkTimesFetcher;

        if($this->option('fetch-only-one-page')){
            $fetcher = $fetcher->fetchOnlyOnePage();
        }

        $fetcher->fetchRecentNews();
    }
}
