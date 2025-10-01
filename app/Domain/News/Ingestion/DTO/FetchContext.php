<?php

namespace App\Domain\News\Ingestion\DTO;


class FetchContext{
    private $last_updated_at;
    private $page_no;

    public function __construct($last_updated_at,$page_no){
        $this->last_updated_at = $last_updated_at;
        $this->page_no = $page_no;
    }

    public function getLastUpdatedAt(){
        return $this->last_updated_at;
    }

    public function getPageNo(){
        return $this->page_no;
    }
}