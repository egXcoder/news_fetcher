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

Route::get('/articles', [ArticlesController::class,'getArticles'])
->middleware('throttle:20,1') //throttle the api, then no one can do dos attack, we can change the limit per our agreement on the usage with the frontend 
->name('articles.get');
