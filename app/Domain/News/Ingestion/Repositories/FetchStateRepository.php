<?php

namespace App\Domain\News\Ingestion\Repositories;

use App\Domain\News\Ingestion\DTO\FetchContext;

class FetchStateRepository{
    public function getContext($providerSourceName):FetchContext{

    }

    public function saveContext($providerSourceName, FetchContext $fetchContext){

    }
}