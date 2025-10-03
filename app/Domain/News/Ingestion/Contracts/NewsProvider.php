<?php 

namespace App\Domain\News\Ingestion\Contracts;

use App\Domain\News\Ingestion\DTO\FetchContext;
use App\Domain\News\Ingestion\DTO\FetchResult;

interface NewsProvider{
    public function fetch(FetchContext $fetchContext):FetchResult;
    public function map($data);
    public function getSourceName();
}