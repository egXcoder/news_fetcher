<?php

namespace App\Domain\News\Ingestion\LaravelFactories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'author'=> $this->faker->name,
            'title'=> $this->faker->words(5),
            'description'=> $this->faker->text(500),
            'content'=> $this->faker->text(10000),
            'src_api'=> array_rand(['ny','guardian','newsapi']),
            'src_id'=> $this->faker->randomNumber(10),
        ];
    }
}
