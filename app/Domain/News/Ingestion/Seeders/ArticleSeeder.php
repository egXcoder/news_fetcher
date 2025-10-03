<?php

namespace App\Domain\News\Ingestion\Seeders;

use App\Domain\News\Ingestion\LaravelFactories\ArticleFactory;
use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new ArticleFactory)
        ->count(50)
        ->create([],new Article);
    }
}
