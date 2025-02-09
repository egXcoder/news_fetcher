<?php

namespace App\ApiFetchers;

use App\Models\Article;
use Illuminate\Support\Facades\Http;

class NewsApiFetcher extends BaseFetcher{
    protected function fetch($last_updated_at, $page_no)
    {
        $response = Http::withHeaders([
            'X-Api-Key'=>'72ef32ebef61439386cfcbd1f605370e'
        ])
        ->get('https://newsapi.org/v2/everything',[
            'q'=>'*',
            'page'=>$page_no,
            'from'=>date('c',strtotime($last_updated_at)),
        ]);
            
        $response = $response->json();

        if($response['status'] == 'ok' && $response['totalResults'] === 0){
            return self::$PROCESSING_COMPLETE;
        }

        if($response['status'] == 'ok' && $response['totalResults']>0){
            return $response['articles'];
        }

        return self::$ERROR_WHILE_FETCHING;
    }


    protected function save($data)
    {
        foreach($data as $single){
            Article::updateOrCreate([

            ],[

            ]);
        }
    }
}