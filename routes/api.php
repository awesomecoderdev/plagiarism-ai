<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlagiarismAI;
use App\Http\Controllers\API\Auth\AuthController;
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
Route::group(['prefix' => '/v1', "controller" => AuthController::class,], function () {
    Route::get('/ai', function () {
        $url = "https://www.google.com/search?q=how+to+make+money";
        $url = str_replace(" ", "+", $url);
        $output = [];
        $response = Http::withHeaders([
            'Remote Address' => '142.250.97.4:443',
            'Referrer Policy' => 'origin'
        ])->get($url);
        return $response;
        // echo '<pre>';
        // print_r($AI->links);
        // echo '</pre>';

        // for ($i=0; $i < ; $i++) {
        //     # code...
        // }
    });
});
