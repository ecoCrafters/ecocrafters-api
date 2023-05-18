<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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
// Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
// Route::post('reset-password', [NewPasswordController::class, 'reset']);
// Route::post('is-email-exist', [UserController::class, 'isEmailExist']);

Route::post('password/email', [ForgotPasswordController::class, '__invoke']);
Route::post('password/code/check', [CodeCheckController::class, '__invoke']);
Route::post('password/reset', [ResetPasswordController::class, '__invoke']);


Route::group(['middleware' => 'jwt.verify'], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
});