<?php

use App\Http\Controllers\ArticlesController;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/articles', [ArticlesController::class,'getArticles'])->name('articles.get');
Route::get('/articles/search', [ArticlesController::class,'searchArticles'])->name('articles.search');
