<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\IngredientController;
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

// Get Data bu Username
Route::get('profile/{username}', [UserController::class, 'show']);
Route::get('profile/{username}/following', [UserController::class, 'showFollowing']);
Route::get('profile/{username}/followers', [UserController::class, 'showFollowers']);
Route::get('profile/{username}/posts', [UserController::class, 'showPosts']);
Route::get('profile/{username}/comments', [UserController::class, 'showComments']);
Route::get('profile/{username}/about', [UserController::class, 'about']);

Route::group(['middleware' => 'jwt.verify'], function ($router) {
    // User Feature
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('profile', [UserController::class, 'update']);
    Route::post('follow', [FollowController::class, 'follow']);
    Route::delete('unfollow', [FollowController::class, 'unfollow']);
    Route::get('users', [UserController::class, 'searchUser']);
    Route::get('check-follow/{id_target}', [UserController::class, 'checkFollow']);


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

    // Tags
    Route::get('tag/', [TagController::class, 'getAllTags']);
    Route::post('tag/create', [TagController::class, 'create']);
    Route::put('tag/{id}', [TagController::class, 'update']);
    Route::delete('tag/{id}', [TagController::class, 'delete']);

    // Ingredients
    Route::get('ingredient/', [IngredientController::class, 'getAllIngredients']);
    Route::post('ingredient/create', [IngredientController::class, 'create']);
    Route::put('ingredient/{id}', [IngredientController::class, 'update']);
    Route::delete('ingredient/{id}', [IngredientController::class, 'delete']);
});