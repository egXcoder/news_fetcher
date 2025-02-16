<?php

namespace App\Console\Commands;

use App\ApiFetchers\TheGuardianApiFetcher;
use App\ApiFetchers\TheGuardianFetcher;
use App\Models\Article;
use Illuminate\Console\Command;

class FetchTheGuardianCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theguardian:fetch
        {--fetch-only-one-page}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch From The Guadian';

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
        $fetcher = new TheGuardianFetcher;

        if($this->option('fetch-only-one-page')){
            $fetcher = $fetcher->fetchOnlyOnePage();
        }

        $fetcher->fetchRecentNews();
    }
}
