<?php

namespace App\Domain\News\Ingestion\Services;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use App\Domain\News\Ingestion\Repositories\FetchContextRepository;
use Illuminate\Support\Facades\Log;

class Fetcher{
    public function fetch(NewsProvider $provider,DataSaver $saver, FetchContextRepository $fetchContextRepository):void{
        $sourceName = $provider->getSourceName();

        try{
            $fetchContext = $fetchContextRepository->getContext($sourceName);
    
            $fetchResult = $provider->fetch($fetchContext);
    
            $data = $provider->map($fetchResult->getData());
            
            if(!empty($data)){
                $saver->save($data);
            }

            $fetchContextRepository->saveContext($sourceName, $fetchResult->getNextContext());

        }catch(\Exception $ex){
            //log and notify
            Log::error("Fetcher failed for provider [$sourceName]: {$ex->getMessage()}", [
                'provider' => $sourceName,
                'context'  => isset($fetchContext) ? $fetchContext : null,
                'exception' => $ex,
            ]);
        }
    }
}