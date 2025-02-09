<?php

namespace App\Console\Commands;

use App\ApiFetchers\NewsApiFetcher;
use Illuminate\Console\Command;

class fetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch News From Apis';

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
        $newsApiFetcher = new NewsApiFetcher;
        $newsApiFetcher->fetchRecentNews();
    }
}
