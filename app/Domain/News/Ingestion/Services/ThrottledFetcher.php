<?php 

namespace App\Domain\News\Ingestion\Services;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use App\Domain\News\Ingestion\Repositories\FetchContextRepository;
use Carbon\Carbon;

class ThrottledFetcher extends Fetcher{
    public function fetch(NewsProvider $provider, DataSaver $saver, FetchContextRepository $fetchContextRepository): void
    {
        $context = $fetchContextRepository->getContext($provider->getSourceName());

        $datetime = $context->getLastUpdatedAt();

        $d = Carbon::parse($datetime);
        $now = Carbon::now();

        if($d->diffInSeconds($now) < 60 * 60){
            return;
        }

        parent::fetch($provider,$saver,$fetchContextRepository);
    }
}