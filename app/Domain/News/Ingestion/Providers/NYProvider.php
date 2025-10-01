<?php

namespace App\Domain\News\Ingestion\Providers;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use Illuminate\Support\Facades\Http;

class NYProvider implements NewsProvider{
    public function fetch()
    {
        $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json',[
            'api-key'=>env('NY_TIMES_API_TOKEN'),
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

        $this->set_debug(json_encode($response));
        
        return self::$ERROR_WHILE_FETCHING;
    }

    public function map($data)
    {
        $result = [];

        foreach($data as $single){
            if($single['document_type'] != 'article'){
                continue;
            }

            $result[] = [
                'src_id'=> $single['_id'],
                'src_api'=>$this->getSourceName(),
                'author'=> $single['source'],
                'title'=> $single['headline']['main'],
                'description'=> $single['lead_paragraph'],
                // 'content'=>'', //the api don't provide content of their articles, i can scrap it by it may trigger legal consequences
                'src_url'=>$single['web_url'],
                'src_name'=>$single['source'],
                'src_published_at'=>$single['pub_date'],
            ];
        }
    }

    public function getSourceName()
    {
        return 'ny';
    }
}