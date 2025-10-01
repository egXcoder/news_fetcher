<?php

namespace App\Domain\News\Ingestion\Services;

use App\Domain\News\Ingestion\Contracts\NewsProvider;

class Fetcher{
    public function fetch(NewsProvider $provider,DataSaver $saver){
        $data = $provider->fetch();

        $data = $provider->map($data);

        $saver->save($data);
    }
}