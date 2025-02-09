<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('articles');

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('author')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('fetched_from')->nullable();
            $table->string('src_id')->nullable();
            $table->string('src_name')->nullable();
            $table->string('src_url')->nullable();
            $table->string('src_published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
