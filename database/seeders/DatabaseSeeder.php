<?php

namespace Database\Seeders;

use App\Domain\News\Ingestion\seeders\ArticleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        (new ArticleSeeder)->run();
    }
}
