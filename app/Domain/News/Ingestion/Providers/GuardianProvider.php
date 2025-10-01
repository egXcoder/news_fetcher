<?php

namespace App\Domain\News\Ingestion\Providers;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use Illuminate\Support\Facades\Http;

class GuardianProvider implements NewsProvider{
    public function fetch()
    {
        $response = Http::get('https://content.guardianapis.com/search',[
            'api-key'=>env('GUARDIAN_API_TOKEN'),
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

    public function map($data)
    {
        $result = [];

        foreach($data as $single){
            if($single['type'] != 'article'){
                continue;
            }

            $result[] = [
                'src_id'=> $single['id'],
                'src_api'=>'guadian',
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
}