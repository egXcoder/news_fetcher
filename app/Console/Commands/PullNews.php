<?php

namespace App\Console\Commands;

use App\Domain\News\Ingestion\Factories\NewsProviderFactory;
use App\Domain\News\Ingestion\Repositories\FetchContextRepository;
use App\Domain\News\Ingestion\Services\DataSaver;
use App\Domain\News\Ingestion\Services\ThrottledFetcher;
use Illuminate\Console\Command;

class PullNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:pull {provider}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull News';

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
        $fetcher = new ThrottledFetcher(
            NewsProviderFactory::make($this->argument('provider')),
            new DataSaver,
            new FetchContextRepository
        );

        $fetcher->fetch();
    }
}
