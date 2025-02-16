<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    /**
     * i didnt do auth around it, because most likely the articles website will be available for public
     * 
     * i have used laravel pagination to paginate using page argument
     * 
     * also, i tried to be compaitable with odata standard for arguments, i think i have exposed what is needed
     * i can though add more rules to it if frontend request more filtering power on the data
     * 
     * i didnt want to use odata package, because i don't want to expose too much power on this api
     * 
     * arguments are
     * - $top numerical
     * - $skip numerical
     * - $orderby updated_at desc
     * - page numerical
     */
    public function getArticles(){
        $top = request('$top') ?: 10;
        $skip = request('$skip') ?: 10;
        $orderby = request('$orderby') ?: 'src_published_at desc';

        if(!is_numeric($top)){
            return [
                'error'=>'Please Make sure passed top is numerical'
            ];
        }

        if(!is_numeric($skip)){
            return [
                'error'=>'Please Make sure passed skip is numerical'
            ];
        }

        $orderby = explode(' ',$orderby);
        if(!in_array($orderby[0],['src_published_at','created_at','updated_at'])){
            return [
                'error'=>'Please Make you are ordering by correct date column'
            ];
        }

        if(!in_array($orderby[1],['asc','desc'])){
            return [
                'error'=>'only allowed directions are asc or desc'
            ];
        }

        return [
            'success'=>Article::orderBy($orderby[0],$orderby[1])->skip($skip)->paginate($top)
        ];
    }
}
