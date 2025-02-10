<?php

namespace App\ApiFetchers;

use App\Models\Article;
use Illuminate\Support\Facades\Http;

class NewYorkTimesFetcher extends BaseFetcher{
    /**
     * please notice, free plan only allows Limit: 1,000 requests per day. Rate: 10 requests per minute.
     */
    protected function fetch($last_updated_at, $page_no)
    {
        $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json',[
            'api-key'=>'J7lGGG7wG98bOOgaB3G3zbHj4iA3JCc3 ',
            'page'=>$page_no,
            'sort'=>'oldest',
            'begin_date'=>date('Ymd',strtotime($last_updated_at)),
            // 'order-date'=>'last-modified',
            // 'use-date'=>'last-modified',
            // 'show-fields'=>'body,headline',
        ]);
            
        $response = json_decode($response->body(),true);

        if($response['status'] == 'OK' && count($response['response']['docs']) === 0){
            return self::$PROCESSING_COMPLETE;
        }

        if($response['status'] == 'OK' && (count($response['response']['docs'])>0)){
            return $response['response']['docs'];
        }

        dd('called');

        $this->set_debug(json_encode($response));
        
        return self::$ERROR_WHILE_FETCHING;
    }


    protected function save($data)
    {
        foreach($data as $single){
            if($single['document_type'] != 'article'){
                continue;
            }

            Article::updateOrCreate([
                'src_id'=> $single['_id'],
            ],[
                'author'=> $single['source'],
                'title'=> $single['headline']['main'],
                'description'=> $single['lead_paragraph'],
                // 'content'=>'', //the api don't provide content of their articles, i can scrap it by it may trigger legal consequences
                'fetched_from'=>class_basename($this),
                'src_url'=>$single['web_url'],
                'src_name'=>$single['source'],
                'src_published_at'=>$single['pub_date'],
            ]);
        }
    }
}