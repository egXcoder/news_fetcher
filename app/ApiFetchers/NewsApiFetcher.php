<?php

namespace App\ApiFetchers;

use Illuminate\Support\Facades\Http;

class NewsApiFetcher extends BaseFetcher{
    public function fetch($last_updated_at, $page_no)
    {
        Http::withHeaders([
            'X-Api-Key'=>'72ef32ebef61439386cfcbd1f605370e'
        ])
        ->get('https://newsapi.org/v2/everything?q=keyword&apiKey=72ef32ebef61439386cfcbd1f605370e')
    }

    public function save($data)
    {
        
    }
}