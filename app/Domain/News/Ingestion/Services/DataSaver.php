<?php

namespace App\Domain\News\Ingestion\Services;

use App\Models\Article;

class DataSaver{
    public function save($data){
        foreach($data as $single){
            Article::updateOrCreate([
                'src_id'=> $single['src_id'],
                'src_api'=> $single['src_api'],
            ],$single);
        }
    }
}