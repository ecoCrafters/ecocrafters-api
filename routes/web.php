<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/create-content', [PostController::class, 'createContent'])->name('content.create');
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'guest']], function () {
     \UniSharp\LaravelFilemanager\Lfm::routes();
 });