<?php

namespace App\ApiFetchers;

use App\Models\FetcherNextStatus;
use DateTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class BaseFetcher{
    public static $ERROR_WHILE_FETCHING = -1;
    public static $PROCESSING_COMPLETE = -2;

    public function fetchRecentNews(){
        $this->blockIfAlreadyFetching();

        $now = date('Y-m-d H:i:s');
        $next_successful_fetch = $this->getNextSuccessfulFetch();
        $last_updated_at = $next_successful_fetch[0];
        $page_no = $next_successful_fetch[1];
        
        $this->set_debug('Starting fetching for -- class= ' . class_basename($this));
        $this->set_debug('from lastUpdatedDate= ' . $last_updated_at);

        if(!$this->validateDate($last_updated_at)){
            throw new \Exception("Please make sure last updated date is valid date");
        }

        while(true){
            $this->set_debug('fetching page no = ' . $page_no);

            $data = $this->fetch($last_updated_at,$page_no);

            if($data === self::$ERROR_WHILE_FETCHING){
                return false;
            }

            if($data === self::$PROCESSING_COMPLETE){
                break;
            }

            if(!count($data)){
                return false;
            }

            $this->set_debug('fetched data with length = ' . count($data));
            $this->set_debug('fetched: ' . json_encode($data));

            DB::beginTransaction();

            $this->save($data);

            $page_no++;
            
            $this->setNextSuccessfulFetch($last_updated_at,$page_no);
            
            DB::commit();

            $this->set_debug('data saved in database');
            $this->set_debug('sleeping...');

            sleep(10);
        }

        $this->setNextSuccessfulFetch($now,1);
    }

    /**
     * @return array [string of last updated_at in format Y-m-d H:i:s, int of page no]
     */
    private function getNextSuccessfulFetch(){
        $key = class_basename($this);

        return [
            FetcherNextStatus::where('key',$key)->value('next_datetime') ?: date('Y-m-d H:i:s',strtotime('-2 day')),
            FetcherNextStatus::where('key',$key)->value('next_page_no') ?: 1,
        ];
    }

    /**
     * @param string $updated_at Y-m-d H:i:s
     * @param int $page_no
     */
    private function setNextSuccessfulFetch($updated_at,$page_no){
        $key = class_basename($this);

        FetcherNextStatus::updateOrCreate([
            'key'=>$key,
        ],[
            'next_datetime'=>$updated_at,
            'next_page_no'=>$page_no
        ]);
    }

    protected function set_debug($debug) {
        dump($debug);
	}

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function blockIfAlreadyFetching(){
        $fp = fopen('/tmp/fetch_news_' . class_basename($this), 'w');

        if(!flock($fp, LOCK_EX | LOCK_NB)) {
            throw new \Exception('There is a request that is still processing at the moment for' . class_basename($this));
        }
    }

    /**
     * @param string $last_updated_at Y-m-d H:i:s
     * @param int $page_no
     */
    protected abstract function fetch($last_updated_at,$page_no);


    /**
     * @param array $data
     */
    protected abstract function save($data);
}