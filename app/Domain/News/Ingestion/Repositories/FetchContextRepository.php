<?php

namespace App\Domain\News\Ingestion\Repositories;

use App\Domain\News\Ingestion\DTO\FetchContext;
use Illuminate\Support\Facades\DB;

class FetchContextRepository{
    public function getContext($providerSourceName){
        $record = DB::table('fetch_contexts')->where('api',$providerSourceName)->first();
    
        $updated_at = $record['next_datetime'] ?: date('Y-m-d H:i:s',strtotime('-4 hours'));
        $page_no = $record['next_page_no'] ?: 1;

        return new FetchContext($updated_at,$page_no);
    }

    public function saveContext($providerSourceName, FetchContext $fetchContext){
        DB::table('fetch_contexts')->updateOrInsert([
            'api'=> $providerSourceName,
        ],[
            'next_datetime'=>$fetchContext->getLastUpdatedAt(),
            'next_page_no'=>$fetchContext->getPageNo()
        ]);
    }
}