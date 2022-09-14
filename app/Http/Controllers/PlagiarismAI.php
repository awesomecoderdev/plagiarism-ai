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
        $this->source = "$this->host?q=" . str_replace(" ", "+", strtolower($search)) . '&hl=' . $this->lang . '&num=100';
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
        $response = Http::withHeaders([
            "Host" => "google.com",
            "User-Agent" => 'Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.111 Mobile Safari/537.36',
        ])->get($url);

        echo $response->status();
        echo "<br>";

        if ($response->successful() && $response->status() == 200) {
            $htmlDom = new DOMDocument();
            @$htmlDom->loadHTML($response->body());
            $links =  $htmlDom->getElementsByTagName('a');
            foreach ($links as $key => $link) {
                if ($link->getAttribute("href")) {
                    $href = $link->getAttribute("href");
                    $href = explode("http", $href, 2);
                    $href = isset($href[1]) ? strtok(strtok("http$href[1]", '&'), '?') : false;
                    if ($href) {
                        if (strpos($href, "google.") == false) {
                            $this->links[] = $href;
                            echo "<a href='$href' >" . $link->textContent . "</a>";
                            echo "<br>";
                        }
                    }

                    // $link_ = $link->getAttribute("href");
                    // $search = substr($link_, 0, 7);
                    // if ($search == "/url?q=") {
                    //     $url = str_replace("/url?q=", "", $link->getAttribute("href"));
                    //     $domain = parse_url($url, PHP_URL_HOST);
                    //     if (strpos($domain, ".google") == false) { // remove google links
                    //         $slug = explode("&sa=", $url, 2);
                    //         $url = is_array($slug) ? current($slug) : $url;
                    //         $verify = substr($url, 0, 7);
                    //         if ($verify != "/search") {
                    //             $this->links[] = $url;
                    //         }
                    //         echo "<a href='$url' >" . $link->textContent . "</a>";
                    //         echo "<br>";
                    //     }
                    // }
                }
            }
        } else {
            // for ($i = 0; $i < 20; $i++) {
            //     $this->links[] = Str::random(50);
            // }
            $this->links[] = "/url?sa=t&source=web&rct=j&url=https://www.crosbymarketing.com/about-us/news-and-views/writing-errors-ruin-your-credibility/&ved=2ahUKEwi67f6X25T6AhXkQ3wKHfGkA3AQFnoECAoQAQ";
            $this->links[] = "https://policies.google.com/";
            $this->links[] = "https://awesomecoder.org/";
            $this->links[] = "https://awesomecoder.dev/";
            $this->links[] = "https://fiverr.com/";
            $this->links[] = "https://youtube.com/";
            $this->links[] = "https://github.com/";
            $this->links[] = "www.facebook.com/";
            $this->links[] = "https://en.wikipedia.org/wiki/Sibir";
            $this->links[] = "http://www.tiktok.com/@mdlabibhasan239/video/7141242123994549531?is_copy_url=1&is_from_webapp=v1";
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
    public function process()
    {
        // foreach ($this->links as $key => $link) {
        //     $href = $link;
        //     $href = explode("http", $href, 2);
        //     $href = isset($href[1]) ? strtok(strtok("http$href[1]", '&'), '?') : false;
        //     if ($href) {
        //         if (strpos($href, "google.") == false) {
        //             echo  $href;
        //             echo "<br>";
        //         }
        //     }
        // }
        echo '<pre>';
        print_r($this->links);
        echo '</pre>';
        ProcessDataScraperAI::dispatch($this->links);
    }
}
