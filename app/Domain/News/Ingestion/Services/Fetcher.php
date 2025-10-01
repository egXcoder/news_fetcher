<?php

namespace App\Domain\News\Ingestion\Services;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use App\Domain\News\Ingestion\Policies\FetchPolicy;

class Fetcher{
    public function fetch(NewsProvider $provider,DataSaver $saver,FetchPolicy $policy){
        if(!$policy->canFetch()){
            return;
        }

        $data = $provider->fetch();

        $data = $provider->map($data);

        $saver->save($data);
    
        $policy->recordLastRunTime();
    }
}