<?php

namespace App\Console\Commands;

use App\ApiFetchers\NewsApiFetcher;
use Illuminate\Console\Command;

class FetchNewsApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsapi:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch From News API';

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
        $fetcher = new NewsApiFetcher;
        $fetcher->fetchRecentNews();
    }
}
