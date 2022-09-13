<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlagiarismAI;
use App\Http\Controllers\CollectDataFromInternet;

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
    // $ai = new PlagiarismAI("awesomecoder");
    // $ai->run(5);
    // $links = $ai->links;
    // echo '<pre>';
    // print_r($links);
    // echo '</pre>';
    // $ai->process($links);

    $url = "https://www.google.com/search?hl=en&q=md+ibrahim+kholil";
    $response = Http::withHeaders([
        "Host" => "google.com",
        "authority" => "www.google.com",
        "sec-fetch-site" => "same-origin",
        "user-agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36",
    ])->get($url);
    if ($response->successful()) {
        $htmlDom = new DOMDocument();
        @$htmlDom->loadHTML($response->body());
        $links =  $htmlDom->getElementsByTagName('a');
        foreach ($links as $key => $link) {
            if ($link->getAttribute("href")) {
                $link_ = $link->getAttribute("href");
                $search = substr($link_, 0, 7); // /url?q=
                if ($search == "/url?q=") {
                    $url = str_replace("/url?q=", "", $link->getAttribute("href"));
                    $domain = parse_url($url, PHP_URL_HOST);
                    if (strpos($domain, ".google") == false) { // remove google links
                        $slug = explode("&sa=", $url, 2);
                        $url = is_array($slug) ? current($slug) : $url;
                        $verify = substr($url, 0, 7);
                        if ($verify != "/search") {
                            $output[] = $url;
                        }
                        echo "<a href='$url' >" . $link->textContent . "</a>";
                        echo "<br>";
                    }
                }
            }
        }
    } else {
        return $response;
    }
    // $req = new CollectDataFromInternet("cricket");
    // $req->get(1);
});
