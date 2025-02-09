<?php

namespace App\ApiFetchers;

use App\Models\Article;
use Illuminate\Support\Facades\Http;

class NewsApiFetcher extends BaseFetcher{
    /**
     * please notice, free plan only allows 100 request every day, so we need to be careful to throttle this
     * we can add a logic on this class to try to track the allowed number of requests per day, and try to distribute
     * this number per day.. this will be nice to have maybe i will work on it later
     */
    protected function fetch($last_updated_at, $page_no)
    {
        $response = Http::withHeaders([
            'X-Api-Key'=>'72ef32ebef61439386cfcbd1f605370e'
        ])
        ->get('https://newsapi.org/v2/everything',[
            'q'=>'*',
            'page'=>$page_no,
            'pageSize'=>100,
            'from'=>date('c',strtotime($last_updated_at)),
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


    protected function save($data)
    {
        foreach($data as $single){
            Article::updateOrCreate([
                'src_id'=> sha1($single['author'] . $single['title'] . $single['description']),
            ],[
                'author'=> $single['author'],
                'title'=> $single['title'],
                'description'=> $single['description'],
                'content'=>$single['content'],
                'fetched_from'=>class_basename($this),
                'src_url'=>$single['url'],
                'src_name'=>$single['source']['name'],
                'src_published_at'=>$single['publishedAt'],
            ]);
        }
    }
}