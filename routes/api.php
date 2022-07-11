<?php

use Illuminate\Http\Request;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

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

    Route::middleware('auth:api')->prefix('/admin/account/manage')->group(function () {
        Route::apiResources([
            'users' => UserManagerController::class,
        ]);
    });
});
