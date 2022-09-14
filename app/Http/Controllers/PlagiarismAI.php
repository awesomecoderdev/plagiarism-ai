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
     * @var string
     */
    public $status;

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    public $url;

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
        $this->source = "$this->host?q=" . str_replace(" ", "+", strtolower($search));
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
        $this->url = $url . '&hl=' . $this->lang . '&num=100';
        $response = Http::withHeaders([
            "Host" => "google.com",
            "User-Agent" => 'Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.111 Mobile Safari/537.36',
        ])->get($this->url);
        $this->status =  $response->status();

        // echo "<br>";
        // echo $url;
        // echo "<br>";


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
                            // echo "<a href='$href' >" . $link->textContent . "</a>";
                            // echo "<br>";
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
            // $this->links[] = "/url?sa=t&source=web&rct=j&url=https://www.crosbymarketing.com/about-us/news-and-views/writing-errors-ruin-your-credibility/&ved=2ahUKEwi67f6X25T6AhXkQ3wKHfGkA3AQFnoECAoQAQ";
            // $this->links[] = "https://policies.google.com/";
            // $this->links[] = "https://awesomecoder.org/";
            // $this->links[] = "https://awesomecoder.dev/";
            // $this->links[] = "https://fiverr.com/";
            // $this->links[] = "https://youtube.com/";
            // $this->links[] = "https://github.com/";
            // $this->links[] = "https://www.upwork.com/";
            // $this->links[] = "https://en.wikipedia.org/wiki/Sibir";
            // $this->links[] = "http://www.tiktok.com/@mdlabibhasan239/video/7141242123994549531?is_copy_url=1&is_from_webapp=v1";
            $this->links =    [
                "https://www.icc-cricket.com/news/2790115",
                "https://www.cricketworld.com/cricket-betting-tips-and-match-predictions-zimbabwe-t10-2022-harare-kings-cricket-club-vs-takashinga-patriots-ii-20th-match-14th-sept/81534.htm",
                "https://www.espncricinfo.com/",
                "https://www.espncricinfo.com/story/t20-world-cup-2022-west-indies-recall-evin-lewis-pick-yannic-cariah-ahead-of-hayden-walsh-1334702",
                "https://www.espncricinfo.com/story/robin-uthappa-announces-retirement-from-international-and-indian-cricket-1334660",
                "https://m.cricbuzz.com/cricket-commentary",
                "https://www.cricbuzz.com/cricket-match/live-scores/recent-matches",
                "https://www.cricbuzz.com/cricket-match/live-scores/upcoming-matches",
                "https://m.cricbuzz.com/cricket-commentary/51236/ind-vs-pak-super-four-match-2-a1-v-a2-asia-cup-2022",
                "https://www.cricbuzz.com/live-cricket-scorecard/51460/ken-vs-nep-1st-t20i-nepal-tour-of-kenya-2022",
                "https://m.cricbuzz.com/cricket-commentary/47673/oman-vs-nep-2nd-match-icc-cricket-world-cup-league-two-2019-23",
                "https://www.cricbuzz.com/live-cricket-scorecard/51462/ken-vs-nep-2nd-t20i-nepal-tour-of-kenya-2022",
                "https://m.cricbuzz.com/live-cricket-scorecard/43496/group-a-royal-london-one-day-cup-2022",
                "https://www.cricket.com.au/",
                "https://www.cricket.com.au/news/australia-squad-india-t20-tour-starc-marsh-stoinis-injury-ellis-sams-abbot-included-t20-world-cup/2022-09-14",
                "https://www.cricket.com.au/news/will-pucovski-century-ashley-chandrasinghe-victoria-gilkes-heazlett-nsw-queensland-tasmania/2022-09-14",
                "https://www.cricket.com/",
                "https://www.icc-cricket.com/",
                "https://www.icc-cricket.com/news/2790414",
                "https://www.icc-cricket.com/news/2790380",
                "https://en.m.wikipedia.org/wiki/Cricket",
                "http://en.m.wikipedia.org/wiki/Commonwealth_of_Nations",
                "http://en.m.wikipedia.org/wiki/Cricket_ball",
                "http://en.m.wikipedia.org/wiki/Cricket_bat",
                "http://en.m.wikipedia.org/wiki/Wicket",
                "http://en.m.wikipedia.org/wiki/Stump_(cricket)",
                "http://en.m.wikipedia.org/wiki/Bail_(cricket)",
                "http://en.m.wikipedia.org/wiki/Cricket_clothing_and_equipment",
                "https://www.cricketwireless.com/",
                "https://www.mykhel.com/cricket/",
                "https://www.news18.com/cricketnext/amp/",
                "https://www.bbc.com/sport/cricket",
                "https://m.timesofindia.com/sports/amp_cricket",
                "https://m.timesofindia.com/sports/cricket/news/robin-uthappa-announces-retirement-from-all-forms-of-cricket/amp_articleshow/94205998.cms",
                "https://www.cricketworldcup.com/",
                "https://www.skysports.com/cricket",
                "https://www.theguardian.com/sport/cricket",
                "https://www.hindustantimes.com/cricket/amp",
                "https://www.sportskeeda.com/cricket",
                "https://indianexpress.com/section/sports/cricket/lite/",
                "https://www.britannica.com/sports/cricket-sport",
                "https://www.britannica.com/animal/cricket-insect",
                "https://www.cricketworld.com/cricket/live",
                "https://www.firstpost.com/firstcricket/amp",
                "https://zeenews.india.com/cricket",
                "https://m.youtube.com/watch",
                "https://www.flashscore.com/cricket/",
                "https://www.cricketaustralia.com.au/",
                "https://www.t20worldcup.com/",
                "https://cricketexchange.in/",
                "https://sports.ndtv.com/cricket/amp/1",
                "https://www.hotstar.com/in/sports/cricket/live-cricket-score",
                "https://www.indiatvnews.com/cricket/live-scores",
                "https://www.espn.in/cricket/scores",
                "https://www.livescore.com/en/cricket/",
                "https://sportstar.thehindu.com/cricket/",
                "https://www.telegraph.co.uk/cricket/",
                "https://www.sportsadda.com/cricket/scores-fixtures",
                "https://www.sportsadda.com/cricket/scores-fixtures/european-cricket",
                "https://www.nzc.nz/",
                "https://m.rediff.com/amp/cricket",
                "https://www.ecb.co.uk/",
                "https://www.lords.org/",
                "https://www.ecn.cricket/",
                "https://wwos.nine.com.au/cricket",
                "https://cricket.one/",
                "https://betway.com/en/sports/cat/cricket",
                "https://betway.com/en/sports/evt/10208899",
                "https://betway.com/en/sports/evt/10204883",
                "https://betway.com/en/sports/evt/10215003",
                "https://www.smh.com.au/sport/cricket",
                "https://cricketaddictor.com/fantasy-cricket/rub-vs-amb-dream11-prediction-fantasy-cricket-tips-dream11-team-playing-xi-pitch-report-injury-update-kca-womens-t20-challengers/",
                "https://www.insidesport.in/cricket/",
                "https://cricketcanada.org/",
                "https://www.bcci.tv/",
                "https://shop.cricketmedia.com/",
                "https://www.dailymail.co.uk/sport/cricket/index.html",
                "https://cricketco.com/",
                "https://cricketpakistan.com.pk/",
                "https://twitter.com/cricketcomau",
                "https://supersport.com/cricket",
                "https://www.cricket24.com/",
                "https://www.cricketireland.ie/",
                "https://www.windiescricket.com/news/west-indies-name-squad-for-icc-mens-t20-world-cup/",
                "http://www.devoncricket.co.uk/",
                "https://www.fancode.com/cricket/schedule/today",
                "https://www.foxsports.com.au/cricket/australia/cricket-australia-2022-next-odi-captain-pat-cummins-david-warner-steve-smith-contenders-to-replace-aaron-finch/news-story/7380ca1db8bf743c2bb1d2f7fd5331cf",
                "https://www.crickethealth.com/",
                "https://www.pcb.com.pk/",
                "https://www.mirror.co.uk/sport/cricket/",
                "https://www.usacricket.org/what-is-cricket/",
                "https://m.facebook.com/IndianCricketTeam/",
                "https://bdcrictime.com/",
                "https://cricheroes.in/live-matches",
                "https://cricketmazza.com/",
                "https://www.reddit.com/r/Cricket/",
                "https://www.play-cricket.com/",
                "https://emergingcricket.com/",
                "https://www.kncb.nl/",
                "http://www.cricketscotland.com/",
                "https://www.thesun.co.uk/sport/cricket/",
                "https://www.outlookindia.com/topic/cricket",
                "https://www.sportstiger.com/",
                "https://www.news24.com/sport/cricket",
                "https://www.freepik.com/free-photos-vectors/cricket",
                "https://www.indiatoday.in/live-score/",
                "https://taxaoutdoors.com/habitats/cricket/",
                "https://parimatch.in/en/cricket/live",
                "https://parimatch.in/en/events/delhi-capitals-srl-kolkata-knight-riders-srl-9005047",
                "https://parimatch.in/en/events/jamaica-tallawahs-st-kitts-and-nevis-patriots-8934426",
                "https://parimatch.in/en/events/bangalore-legends-v-mumbai-legends-v-9030099",
                "https://www.independent.co.uk/sport/cricket",
                "https://cricket.lancashirecricket.co.uk/",
                "https://www.1news.co.nz/2022/09/13/nz-over-60s-cricket-team-raring-to-go-for-world-cup-final/",
                "https://www.mcg.org.au/",
                "https://www.cricketbuddies.com/",
                "https://www.newsnow.co.uk/h/Sport/Cricket",
                "https://www.bt.com/sport/cricket",
                "https://www.middlesexccc.com/",
                "https://uk.sports.yahoo.com/cricket/",
                "https://www.kiaoval.com/",
                "https://www.cricketnsw.com.au/",
                "https://www.cricketvictoria.com.au/",
                "https://cherrycricket.com/",
                "https://www.thecricketer.com/"
            ];
        }
        // https://api.allorigins.win/get?url=https://google.com
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
        // echo '<pre>';
        // print_r($this->links);
        // echo '</pre>';
        ProcessDataScraperAI::dispatch($this->links);
    }
}
