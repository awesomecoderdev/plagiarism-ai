<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlagiarismAI;
use Illuminate\Support\Facades\File;

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


Route::get('/', function () {
    $ai = new PlagiarismAI("awesomecoder");
    $ai->run(5);
    $links = $ai->links;
    echo '<pre>';
    print_r($links);
    echo '</pre>';
    $ai->process($links);
});
