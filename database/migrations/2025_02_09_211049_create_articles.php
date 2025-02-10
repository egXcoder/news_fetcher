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
            $table->string('author')->collation('utf8mb4_general_ci')->nullable();
            $table->text('title')->collation('utf8mb4_general_ci')->nullable();
            $table->text('description')->collation('utf8mb4_general_ci')->nullable();
            $table->text('content')->collation('utf8mb4_general_ci')->nullable();
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
