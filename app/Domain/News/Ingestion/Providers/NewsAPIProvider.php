<?php

namespace App\Domain\News\Ingestion\Providers;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use Illuminate\Support\Facades\Http;

class NewsAPIProvider implements NewsProvider{
    public function fetch()
    {
        $response = Http::withHeaders([
            'X-Api-Key'=>env('NEWS_API_TOKEN')
        ])
        ->get('https://newsapi.org/v2/everything',[
            'q'=>'*',
            'page'=>$page_no,
            'pageSize'=>100,
            'from'=>date('c',strtotime($last_updated_at)),
            'sortBy'=>'publishedAt',
            'language'=>'en' //i think we will be only interested in english sources for now
        ]);
            
        $response = $response->json();

        if($response['status'] == 'ok' && $response['totalResults'] === 0){
            return self::$PROCESSING_COMPLETE;
        }

        if($response['status'] == 'ok' && $response['totalResults']>0){
            return $response['articles'];
        }

        $this->set_debug(json_encode($response));
        
        return self::$ERROR_WHILE_FETCHING;
    }

    public function map($data)
    {
        $result = [];

        foreach($data as $single){
            $result[] = [
                'src_id'=> sha1($single['author'] . $single['title'] . $single['description']),
                'src_api'=>'newsapi',
                'author'=> $single['author'],
                'title'=> $single['title'],
                'description'=> $single['description'],
                'content'=>$single['content'],
                'src_url'=>$single['url'],
                'src_name'=>$single['source']['name'],
                'src_published_at'=>$single['publishedAt'],
            ];
        }

        return $result;
    }
}