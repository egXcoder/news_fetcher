<?php

namespace App\Domain\News\Ingestion\Providers;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use App\Domain\News\Ingestion\DTO\FetchContext;
use App\Domain\News\Ingestion\DTO\FetchResult;
use Exception;
use Illuminate\Support\Facades\Http;

class NYProvider implements NewsProvider{
    private const API_URL = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
    private const SORT_ORDER = 'oldest';

    public function fetch(FetchContext $fetchContext):FetchResult
    {
        $now = date('Y-m-d H:i:s');

        $response = Http::get(self::API_URL,[
            'api-key'=>config('services.nytimes.token', env('NY_TIMES_API_TOKEN')),
            'page'=>$fetchContext->getPageNo(),
            'sort'=>self::SORT_ORDER,
            'begin_date'=>date('Ymd',strtotime($fetchContext->getLastUpdatedAt())),
            // 'order-date'=>'last-modified',
            // 'use-date'=>'last-modified',
            // 'show-fields'=>'body,headline',
        ]);

        if (! $response->successful()) {
            throw new Exception("NY API error: " . $response->body());
        }
            
        $responseData = $response->json();

        $docs = $responseData['response']['docs'] ?? [];

        if(count($docs) == 0 ){
            return new FetchResult([],new FetchContext($now,1));
        }

        return new FetchResult(
            $response['response']['docs'],
            new FetchContext($fetchContext->getLastUpdatedAt(),$fetchContext->getPageNo()+1
        ));
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
                // 'src_url'=>$single['web_url'],
                // 'src_name'=>$single['source'],
                // 'src_published_at'=>$single['pub_date'],
            ];
        }
    }

    public function getSourceName()
    {
        return 'ny';
    }
}