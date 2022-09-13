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
    // $res = file_get_contents(resource_path("plagiarism/data/awesomecoder.dev/d1de586156896cd6cd13fe3c8dd6835a.html"));
    // preg_match_all('%(<p[^>]*>.*?</p>)%i', $res, $matches);
    // preg_match_all('/<p>(.*?)<\/p>/s', $res, $matches);
    // $htmlDom = new DOMDocument();
    // @$htmlDom->loadHTML($res);
    // $ratings =  $htmlDom->getElementsByTagName('p');
    // foreach ($ratings as $key => $r) {
    //     echo trim($r->textContent);
    // }

    $array1 = array('item1', 'item2', 'item3', 'item4', 'item5');
    $array2 = array('item1', 'item4', 'item6', 'item7', 'item8', 'item9', 'item10');
    // returns array containing only items that appear in both arrays
    $matches = array_intersect($array1, $array2);
    // calculate 'similarity' of array 2 to array 1
    // if you want to calculate the inverse, the 'similarity' of array 1
    // to array 2, replace $array1 with $array2 below
    $a = round(count($matches));
    $b = count($array1);
    $similarity = $a / $b * 100;
    echo 'SIMILARITY: ' . $similarity . '%';

    // /<h2(.*?)<\/h2>/si
});
