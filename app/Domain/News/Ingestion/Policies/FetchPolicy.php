<?php

namespace App\Domain\News\Ingestion\Policies;

class FetchPolicy{
    protected $source_name;

    public function __construct($provider_source_name)
    {
        $this->source_name = $provider_source_name;    
    }

    public function canFetch(){

    }   

    public function recordLastRunTime(){

    }
}