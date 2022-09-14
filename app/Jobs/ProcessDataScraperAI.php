<?php

namespace App\Jobs;

use Exception;
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
                    if ($domain) {
                        echo "\n Extracting data from $domain";
                        $path  = resource_path("plagiarism/data/$domain");
                        if (!File::isDirectory($path)) {
                            File::makeDirectory($path, 0777, true, true);
                        }
                        try {
                            $response = Http::get($link);
                            if ($response->successful() && $response->status() == 200) {
                                $name = md5($link);
                                $htmlDom = new DOMDocument();
                                @$htmlDom->loadHTML($response->body());
                                $contents = $htmlDom->getElementsByTagName('body')->item(0)->textContent ? $htmlDom->getElementsByTagName('body')->item(0)->textContent : false;
                                // $contents = strip_tags($response->body());
                                if ($contents) {
                                    $textContents = trim(preg_replace('/\s\s+/', ' ', $contents));
                                    File::put("$path/$name.txt", $textContents);
                                    File::put("$path/$name.html", $textContents);
                                    // $json =  explode(".", $textContents);
                                    // $json = preg_split('~(?:Mrs?|Miss|Ms|Prof|Rev|Col|Dr)[.?!:](*SKIP)(*F)|[.?!:]+\K\s+~', $textContents, 0, PREG_SPLIT_NO_EMPTY);
                                    // File::put("$path/new_$name.json", json_encode($json, JSON_PRETTY_PRINT));
                                }
                            }
                        } catch (Throwable $e) {
                            File::put("$path/throwable.txt", $e->getMessage());
                            continue;
                        } catch (Exception $exception) {
                            File::put("$path/exception.txt", $exception->getMessage());
                            continue;
                        }
                    }
                }
            }
        }
    }
}
