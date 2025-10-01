<?php

namespace App\Domain\News\Ingestion\DTO;

class FetchResult{
    private Array $data;
    private FetchContext $nextContext;

    public function __construct($data,$nextContext)
    {
        $this->data = $data;
        $this->nextContext = $nextContext;
    }

    public function getData(){
        return $this->data;
    }

    public function getNextContext(){
        return $this->nextContext;
    }
}