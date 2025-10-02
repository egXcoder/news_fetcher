<?php

namespace App\Domain\News\Ingestion\Services;

use App\Domain\News\Ingestion\Contracts\NewsProvider;
use App\Domain\News\Ingestion\Repositories\FetchContextRepository;
use Illuminate\Support\Facades\Log;

class Fetcher{
    protected $provider;
    protected $saver;
    protected $fetchContextRepository;

    public function __construct(NewsProvider $provider,DataSaver $saver, FetchContextRepository $fetchContextRepository)
    {
        $this->provider = $provider;
        $this->saver = $saver;
        $this->fetchContextRepository = $fetchContextRepository;
    }

    public function fetch():void{
        $sourceName = $this->provider->getSourceName();

        try{
            $fetchContext = $this->fetchContextRepository->getContext($sourceName);
    
            $fetchResult = $this->provider->fetch($fetchContext);
    
            $data = $this->provider->map($fetchResult->getData());
            
            if(!empty($data)){
                $this->saver->save($data);
            }

            $this->fetchContextRepository->saveContext($sourceName, $fetchResult->getNextContext());

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