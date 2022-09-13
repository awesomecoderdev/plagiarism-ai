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
    $ai->run(0);
    $ai->process();
    $res = file_get_contents(resource_path("plagiarism/data/awesomecoder.dev/d1de586156896cd6cd13fe3c8dd6835a.html"));
    // preg_match_all('%(<p[^>]*>.*?</p>)%i', $res, $matches);
    // preg_match_all('/<p>(.*?)<\/p>/s', $res, $matches);
    $htmlDom = new DOMDocument();
    @$htmlDom->loadHTML($res);
    $ratings =  $htmlDom->getElementsByTagName('p');
    foreach ($ratings as $key => $r) {
        echo trim($r->textContent);
    }

    // /<h2(.*?)<\/h2>/si
});
