<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriberController;

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

$app_version = env('APP_VERSION', 'v1');

Route::prefix($app_version)->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('profile', 'profile');
            Route::patch('profile', 'updateUserProfile');
        });

        Route::prefix('auth')->group(function () {
            Route::post('login', 'login');
            Route::post('register', 'register');
            Route::post('logout', 'logout');
            Route::post('refresh', 'refresh');
            Route::post('verify-mail', 'verifyMail');
            Route::post('forgot-password', 'forgotPassword');
            Route::patch('reset-password', 'resetPassword');
            Route::post('send-verification-mail', 'sendVerificationMail');
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::apiResources([
            'websites' => WebsiteController::class,
            'posts' => PostController::class,
            'subscriptions' => SubscriberController::class,
        ]);
    });
});
