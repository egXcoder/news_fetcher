<?php

namespace App\Domain\News\Ingestion\Factories;

use App\Domain\News\Ingestion\Providers\GuardianProvider;
use App\Domain\News\Ingestion\Providers\NewsAPIProvider;
use App\Domain\News\Ingestion\Providers\NYProvider;
use InvalidArgumentException;

class NewsProviderFactory{
    public static function make($sourceName){
        switch($sourceName){
            case 'guardian':
                return new GuardianProvider();
            case 'ny':
                return new NYProvider();
            case 'newsapi':
                return new NewsAPIProvider();
            default:
                throw new InvalidArgumentException("Unknown Provider " . $sourceName);
        }
    }
}