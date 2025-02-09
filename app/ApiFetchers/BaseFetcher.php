<?php

namespace App\ApiFetchers;

abstract class BaseFetcher{
    /**
     * @param string $last_updated_at Y-m-d H:i:s
     * @param int $page_no
     */
    public abstract function fetch($last_updated_at,$page_no);


    /**
     * @param array $data
     */
    public abstract function save($data);
}