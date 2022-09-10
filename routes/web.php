<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlagiarismAI;

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
    // return view('welcome');

    // $url = "https://www.google.com/search?q=cricket &hl=en";
    // /**
    //  * &start=20 // per page
    //  * &hl=de // country
    //  **/
    // $url = str_replace(" ", "+", $url);

    // $content = file_get_contents($url);
    // // file_put_contents("test.html", $content);
    // // die;
    // $output = [];
    // $htmlDom = new DOMDocument();
    // @$htmlDom->loadHTML($content);

    // $links =  $htmlDom->getElementsByTagName('a');
    // foreach ($links as $key => $link) {
    //     if ($link->getAttribute("href")) {
    //         $link_ = $link->getAttribute("href");
    //         $search = substr($link_, 0, 7); // /url?q=
    //         if ($search == "/url?q=") {
    //             $url = str_replace("/url?q=", "", $link->getAttribute("href"));
    //             $domain = parse_url($url, PHP_URL_HOST);
    //             if (strpos($domain, ".google") == false) { // remove google links
    //                 $slug = explode("&sa=", $url, 2);
    //                 $url = is_array($slug) ? current($slug) : $url;
    //                 echo "<a href='$url' >" . $link->textContent . "</a>";
    //                 echo "<br>";
    //             }
    //         }
    //     }
    // }

    // $AI = new PlagiarismAI("Cricket");
    // $AI->lang = "en-UK";
    // $AI->run();

    // echo '<pre>';
    // print_r($AI->links);
    // echo '</pre>';

    $url = "https://www.google.com/search?q=how+to+make+money";
    $url = str_replace(" ", "+", $url);
    $output = [];
    // $response = Http::withHeaders([
    //     'Remote-Address' => '142.250.97.4:443',
    //     'Referrer-Policy' => 'www.google.com',
    //     'Host' => 'www.google.com',
    // ])->get($url);
    // $response = Http::get($url);
    // $response = file_get_contents($url);

    // return $response;

    $variableee = readfile($url);
    echo $variableee;

    // for ($i=0; $i < ; $i++) {
    //     # code...
    // }
});
