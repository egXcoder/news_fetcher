<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('author')->collation('utf8mb4_general_ci')->nullable();
            $table->string('title')->collation('utf8mb4_general_ci')->nullable();
            $table->text('description')->collation('utf8mb4_general_ci')->nullable();
            $table->text('content')->collation('utf8mb4_general_ci')->nullable();
            $table->string('src_api')->nullable();
            $table->string('src_id')->nullable();
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
