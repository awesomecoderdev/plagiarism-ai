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
    $ai = new PlagiarismAI("Mispellings and grammatical errors can effect your credibility.");
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

    // $word1 = "Mispellings and grammatical errors can effect your credibility. The same goes for misused commas, and other types of punctuation . Not only will Grammarly underline these issues in red, it will also showed you how to correctly write the sentence.";
    // $word2 = "and grammatical same goes errors Mispellings grammatical errors can goes";
    // $match = similar_text($word1, $word2, $percent);
    // $percent = round($percent, 2);
    // echo "$match letters are the same between '$word1' and '$word2': a $percent% match.\n";

    // /<h2(.*?)<\/h2>/si
});
