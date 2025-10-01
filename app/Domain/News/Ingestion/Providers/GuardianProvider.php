<?php

namespace App\Domain\News\Ingestion\Providers;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use App\Domain\News\Ingestion\DTO\FetchContext;
use App\Domain\News\Ingestion\DTO\FetchResult;
use Illuminate\Support\Facades\Http;

class GuardianProvider implements NewsProvider{
    private const API_URL = 'https://content.guardianapis.com/search';
    private const PAGE_SIZE = 10;
    private const SORT_BY = 'oldest';
    private const ORDER_DATE = 'last-modified';
    private const SHOW_FIELDS = 'body,headline';

    public function fetch(FetchContext $fetchContext):FetchResult
    {
        $now = date('Y-m-d H:i:s');

        $response = Http::get(self::API_URL,[
            'api-key'=> config('services.guardian.token',env('GUARDIAN_API_TOKEN')),
            'page'=>$fetchContext->getPageNo(),
            'page-size'=>self::PAGE_SIZE,
            'order-by'=>self::SORT_BY,
            'order-date'=>self::ORDER_DATE,
            'from-date'=>date('c',strtotime($fetchContext->getLastUpdatedAt())),
            'use-date'=>self::ORDER_DATE,
            'show-fields'=>self::SHOW_FIELDS,
        ]);

        if(!$response->successful()){
            throw new \Exception("guadian api error " . $response->body());
        }
            
        $response = $response->json();

        $responseData = $response['response'];

        if($responseData['status']!='ok'){
            throw new \Exception("Unexpected status from NewsAPI: " . $responseData['status']);
        }

        if($response['total'] === 0){
            return new FetchResult([] , new FetchContext($now,1));
        }

        return new FetchResult(
            $response['total'],
            new FetchContext($fetchContext->getLastUpdatedAt(),$fetchContext->getPageNo()+1)
        );
    }

    public function map($data)
    {
        $result = [];

        foreach($data as $single){
            if($single['type'] != 'article'){
                continue;
            }

            $result[] = [
                'src_id'=> $single['id'],
                'src_api'=>$this->getSourceName(),
                'author'=> 'the gurdian',
                'title'=> $single['webTitle'],
                'description'=> $single['fields']['headline'],
                'content'=>$single['fields']['body'],
                'src_name'=>'the guardian',
                'src_url'=>$single['webUrl'],
                'src_published_at'=>$single['webPublicationDate'],
            ];
        }

        return $result;
    }

    public function getSourceName()
    {
        return 'guardian';
    }
}