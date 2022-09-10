<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlagiarismAI;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\PlagiarismAIController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

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

Route::group(['prefix' => '/v1', "controller" => AuthController::class,], function () {
    Route::get('csrf', [CsrfCookieController::class, 'show'])->middleware('web')->name('api.csrf');
});

Route::group(['prefix' => '/v1/user', "controller" => AuthController::class,], function () {
    Route::post('register', 'store')->name('register');
    Route::post('login', 'auth')->name('login');

    // verify with Bearer Token
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/', 'user')->name('index');
        Route::get('notifications', 'notifications')->name('notifications');
        Route::post('notifications', 'markAsReadNotification')->name('markAsReadNotification');
    });
});


// AI
Route::group(['prefix' => '/v1',], function () {
    Route::post('ai', [PlagiarismAIController::class, 'index'])->name('ai');
});
