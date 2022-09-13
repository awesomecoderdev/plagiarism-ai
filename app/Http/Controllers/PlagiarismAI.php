<?php

namespace App\Http\Controllers;

use DOMDocument;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\ProcessDataScraperAI;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PlagiarismAI extends Controller
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
    public $lang = "en";

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
        $this->source = "$this->host?hl=$this->lang&num=100&q=" . str_replace(" ", "+", strtolower($search));
    }

    /**
     * Get the validation rules that apply to the request.
     *    * &start=20 // per page
     * &hl=de // country
     *
     * @return array<string, mixed>
     */
    public function run(int $start = 0)
    {
        $page = $start * 100;
        $url = $page != 0 ? "$this->source&start=$page" : $this->source;
        $url = str_replace(" ", "+", $url);
        $response = Http::withHeaders([
            "Host" => "google.com",
        ])->get($url);
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
                                $this->links[] = $url;
                            }
                            echo "<a href='$url' >" . $link->textContent . "</a>";
                            echo "<br>";
                        }
                    }
                }
            }
        }

        for ($i = 0; $i < 20; $i++) {
            $this->links[] = Str::random(50);
        }
        return $this->links;
    }

    /**
     * Get the validation rules that apply to the request.
     *    * &start=20 // per page
     * &hl=de // country
     *
     * @return array<string, mixed>
     */
    public function process(array $links = [])
    {
        echo '<pre>';
        print_r($this->links);
        echo '</pre>';
        // ProcessDataScraperAI::dispatch($links);
    }
}
