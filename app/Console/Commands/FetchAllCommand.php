<?php

namespace App\Console\Commands;

use App\ApiFetchers\NewsApiFetcher;
use App\ApiFetchers\NewYorkTimesFetcher;
use App\ApiFetchers\TheGuardianApiFetcher;
use App\ApiFetchers\TheGuardianFetcher;
use App\Models\FetcherNextStatus;
use Illuminate\Console\Command;

class FetchAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {--source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch News From Apis';

    protected $newsapi_minutes = '20';
    protected $theguardian_minutes = '1';
    protected $ny_minutes = '2';

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
        //100 request per day
        //i can run this 1 request per 20 minutes so in day 3*24 = 72 and leave rest of 100 for testing..
        $last_run = FetcherNextStatus::where('key',class_basename(NewsApiFetcher::class))->value('updated_at');
        $last_run_from_seconds = time() - strtotime($last_run);
        if($last_run_from_seconds>$this->newsapi_minutes*60){
            $this->call('newsapi:fetch',[
                '--fetch-only-one-page'=>true
            ]);
        }


        //5000 request per day
        //i can run this 1 request per 1 minutes so in day 60*24 = 1,440 and even i left a lot of quota 
        $last_run = FetcherNextStatus::where('key',class_basename(TheGuardianFetcher::class))->value('updated_at');
        $last_run_from_seconds = time() - strtotime($last_run);
        if($last_run_from_seconds>$this->theguardian_minutes*60){
            $this->call('theguardian:fetch',[
                '--fetch-only-one-page'=>true
            ]);
        }


        //1000 request per day
        //i can run this 1 request per 2 minutes so in day 30*24 = 720
        $last_run = FetcherNextStatus::where('key',class_basename(NewYorkTimesFetcher::class))->value('updated_at');
        $last_run_from_seconds = time() - strtotime($last_run);
        if($last_run_from_seconds>$this->ny_minutes*60){
            $this->call('newyorktimes:fetch',[
                '--fetch-only-one-page'=>true
            ]);
        }
    }
}
