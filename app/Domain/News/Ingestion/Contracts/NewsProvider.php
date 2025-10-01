<?php 

namespace App\Domain\News\Ingestion\Contracts;

interface NewsProvider{
    public function fetch();
    public function map($data);
    public function getSourceName();
}