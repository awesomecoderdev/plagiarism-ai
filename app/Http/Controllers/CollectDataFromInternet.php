<?php

namespace App\Http\Controllers;

use Throwable;
use DOMDocument;
use Illuminate\Http\Request;
use App\Jobs\ProcessDataScraperAI;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class CollectDataFromInternet extends Controller
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $host = "https://www.google.com/search";

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    public $lang = "en-US";

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    public $source;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string, mixed>
     */
    public $links = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function __construct(public $search)
    {
        $this->source = "$this->host?hl=$this->lang&q=" . str_replace(" ", "+", strtolower($search));
    }

    /**
     * Get the validation rules that apply to the request.
     *    * &start=20 // per page
     * &hl=de // country
     *
     * @return array<string, mixed>
     */
    public function get(int $start = 0)
    {
        $page = $start * 10;
        $url = $page != 0 ? "$this->source&start=$page" : $this->source;
        $url = str_replace(" ", "+", $url);
        $output = [];
        $response = Http::withHeaders([
            "Host" => "google.com",
        ])->get($url);

        echo  $response->status();
        echo "<br>";

        if ($response->successful() && $response->status() == 200) {
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
        }



        // try {
        //     $response = Http::withHeaders([
        //         "Host" => "google.com",
        //     ])->get($url);
        //     if ($response->successful() && $response->status() == 200) {
        //         $htmlDom = new DOMDocument();
        //         @$htmlDom->loadHTML($response->body());
        //         $links =  $htmlDom->getElementsByTagName('a');
        //         foreach ($links as $key => $link) {
        //             if ($link->getAttribute("href")) {
        //                 $link_ = $link->getAttribute("href");
        //                 $search = substr($link_, 0, 7); // /url?q=
        //                 if ($search == "/url?q=") {
        //                     $url = str_replace("/url?q=", "", $link->getAttribute("href"));
        //                     $domain = parse_url($url, PHP_URL_HOST);
        //                     if (strpos($domain, ".google") == false) { // remove google links
        //                         $slug = explode("&sa=", $url, 2);
        //                         $url = is_array($slug) ? current($slug) : $url;
        //                         $verify = substr($url, 0, 7);
        //                         if ($verify != "/search") {
        //                             $output[] = $url;
        //                         }
        //                         echo "<a href='$url' >" . $link->textContent . "</a>";
        //                         echo "<br>";
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // } catch (Throwable $th) {
        //     throw $th;
        // }

        return $output;
    }

    /**
     * Get the validation rules that apply to the request.
     *    * &start=20 // per page
     * &hl=de // country
     *
     * @return array<string, mixed>
     */
    public function run(int $end = 5)
    {
        foreach (range(0, $end) as $key => $page) {
            $links = $this->get($page);
            if (!empty($links)) {
                echo '<pre>';
                print_r($links);
                echo '</pre>';
                // ProcessDataScraperAI::dispatch($links);
            } else {
                break;
            }
        }
    }
}
