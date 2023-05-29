<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\CodeCheckController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('password/email', [ForgotPasswordController::class, '__invoke']);
Route::post('password/code/check', [CodeCheckController::class, '__invoke']);
Route::post('password/reset', [ResetPasswordController::class, '__invoke']);


Route::group(['middleware' => 'jwt.verify'], function ($router) {
    // User Feature
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [UserController::class, 'show']);
    Route::put('profile', [UserController::class, 'update']);
    Route::get('users/{username}', [UserController::class, 'getUserByUsername']);
    Route::post('follow', [FollowController::class, 'follow']);
    Route::delete('unfollow', [FollowController::class, 'unfollow']);

    // Post 
    Route::post('post/create', [PostController::class, 'create']);
    Route::post('post/save/{id}', [PostController::class, 'savePost']);
    Route::post('post/like/{id}', [PostController::class, 'likePost']);
    Route::post('post/comment/{id}', [PostController::class, 'commentPost']);
    Route::post('post/like/comment/{id}', [PostController::class, 'likeComment']);
    Route::get('post/{slug}/{id}', [PostController::class, 'detail']);
    Route::get('post/', [PostController::class, 'getAllPosts']);
    Route::put('post/{id}', [PostController::class, 'update']);
    Route::delete('post/{id}', [PostController::class, 'delete']);
    Route::get('post/{title}', [PostController::class, 'getPostByTitle']);
    Route::get('search/{search}', [PostController::class, 'searchPostOrUser']);

});