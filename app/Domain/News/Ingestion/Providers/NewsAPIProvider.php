<?php

namespace App\Domain\News\Ingestion\Providers;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use App\Domain\News\Ingestion\DTO\FetchContext;
use App\Domain\News\Ingestion\DTO\FetchResult;
use Illuminate\Support\Facades\Http;

class NewsAPIProvider implements NewsProvider{
    private const API_URL = 'https://newsapi.org/v2/everything';
    private const SORT_BY = 'publishedAt';
    private const LANGUAGE = 'en';
    private const PAGE_SIZE = 100;

    public function fetch(FetchContext $fetchContext):FetchResult
    {
        $now = date('Y-m-d H:i:s');
        
        $response = Http::withHeaders([
            'X-Api-Key'=>config('services.newsapi.token', env('NEWS_API_TOKEN'))
        ])
        ->get(self::API_URL,[
            'q'=>'*',
            'page'=>$fetchContext->getPageNo(),
            'pageSize'=>self::PAGE_SIZE,
            'from'=>date('c',strtotime($fetchContext->getLastUpdatedAt())),
            'sortBy'=>self::SORT_BY,
            'language'=>self::LANGUAGE //i think we will be only interested in english sources for now
        ]);
            
        if(!$response->successful()){
            throw new \Exception("NewsAPI API error: " . $response->body());
        }

        $responseData = $response->json();

        $status = $responseData['status'] ?? null;
        $articles = $responseData['articles'] ?? [];

        if ($status !== 'ok') {
            throw new \Exception("Unexpected status from NewsAPI: " . $status);
        }

        if (count($articles) === 0) {
            // No more results → restart from now
            return new FetchResult([], new FetchContext($now, 1));
        }

        return new FetchResult(
            $articles,
            new FetchContext($fetchContext->getLastUpdatedAt(), $fetchContext->getPageNo() + 1)
        );
    }

    public function map($data)
    {
        $result = [];

        foreach($data as $single){
            $result[] = [
                'src_id'=> sha1($single['author'] . $single['title'] . $single['description']),
                'src_api'=>$this->getSourceName(),
                'author'=> $single['author'],
                'title'=> $single['title'],
                'description'=> $single['description'],
                'content'=>$single['content'],
                // 'src_url'=>$single['url'],
                // 'src_name'=>$single['source']['name'],
                // 'src_published_at'=>$single['publishedAt'],
            ];
        }

        return $result;
    }

    public function getSourceName()
    {
        return 'newsapi';
    }
}