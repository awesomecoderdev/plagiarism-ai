<?php

namespace App\Jobs;

use Throwable;
use DOMDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessDataScraperAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private $links)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if (!empty($this->links)) {
            $links = "plagiarism/json/links.json";
            $links_path = resource_path($links);

            if (!File::isDirectory(resource_path("plagiarism/json"))) {
                File::makeDirectory((resource_path("plagiarism/json")), 0777, true, true);
            }
            if (!File::isDirectory(resource_path("plagiarism/data"))) {
                File::makeDirectory((resource_path("plagiarism/data")), 0777, true, true);
            }

            if (File::isDirectory(resource_path("plagiarism/data"))) {
                if (!File::exists($links_path)) {
                    File::put($links_path, json_encode($this->links, JSON_PRETTY_PRINT));
                }

                foreach ($this->links as $key => $link) {
                    $domain = parse_url($link, PHP_URL_HOST);
                    echo "\n Extracting data from $domain";
                    $path  = resource_path("plagiarism/data/$domain");
                    if (!File::isDirectory($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }
                    try {
                        $response = Http::get($link);
                        if ($response->successful() && $response->status() == 200) {
                            $name = md5($link);
                            $jsonObj = [];
                            $htmlDom = new DOMDocument();
                            @$htmlDom->loadHTML($response->body());
                            $paragraphs = $htmlDom->getElementsByTagName('p');
                            foreach ($paragraphs as $key => $p) {
                                $jsonObj["p"][] = trim(preg_replace('/\s\s+/', ' ', $p->textContent));
                            }
                            $contents =  $htmlDom->getElementsByTagName('h1');
                            foreach ($contents as $key => $c) {
                                $jsonObj["h1"][] = trim(preg_replace('/\s\s+/', ' ', $c->textContent));
                            }
                            $contents =  $htmlDom->getElementsByTagName('h2');
                            foreach ($contents as $key => $c) {
                                $jsonObj["h2"][] = trim(preg_replace('/\s\s+/', ' ', $c->textContent));
                            }
                            File::put("$path/$name.html", $response->body());
                            File::put("$path/$name.json", json_encode($jsonObj, JSON_PRETTY_PRINT));
                        }
                    } catch (Throwable $e) {
                        continue;
                    }
                }
            }
        }
    }
}
