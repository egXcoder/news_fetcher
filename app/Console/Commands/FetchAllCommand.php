<?php

namespace App\Console\Commands;

use App\ApiFetchers\NewsApiFetcher;
use App\ApiFetchers\TheGuardianApiFetcher;
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
        $this->call('newsapi:fetch');
        $this->call('theguardian:fetch');
    }
}
