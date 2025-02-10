<?php

namespace App\ApiFetchers;

use App\Models\Article;
use Illuminate\Support\Facades\Http;

class TheGuardianFetcher extends BaseFetcher{
    /**
     * please notice, free plan only allows 5000 calls per day and a maximum of 12 per second
     * so this one has more quota than the news api fetcher
     */
    protected function fetch($last_updated_at, $page_no)
    {
        $response = Http::get('https://content.guardianapis.com/search',[
            'api-key'=>'c28a75dd-c529-41e2-96d8-426de9c873b2',
            'page'=>$page_no,
            'page-size'=>10,
            'order-by'=>'oldest',
            'order-date'=>'last-modified',
            'from-date'=>date('c',strtotime($last_updated_at)),
            'use-date'=>'last-modified',
            'show-fields'=>'body,headline',
        ]);
            
        $response = json_decode($response->body(),true);

        $response = $response['response'];

        if($response['status'] == 'ok' && $response['total'] === 0){
            return self::$PROCESSING_COMPLETE;
        }

        if($response['status'] == 'ok' && $response['total']>0){
            return $response['results'];
        }

        $this->set_debug(json_encode($response));
        
        return self::$ERROR_WHILE_FETCHING;
    }


    protected function save($data)
    {
        foreach($data as $single){
            if($single['type'] != 'article'){
                continue;
            }

            Article::updateOrCreate([
                'src_id'=> $single['id'],
            ],[
                'author'=> 'the gurdian',
                'title'=> $single['webTitle'],
                'description'=> $single['fields']['headline'],
                'content'=>$single['fields']['body'],
                'fetched_from'=>class_basename($this),
                'src_url'=>$single['webUrl'],
                'src_name'=>'the gurdian',
                'src_published_at'=>$single['webPublicationDate'],
            ]);
        }
    }
}