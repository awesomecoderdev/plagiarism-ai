<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

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
                    $path  = resource_path("plagiarism/data/$domain");
                    if (!File::isDirectory($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }
                    $response = Http::get($link);
                    if ($response->successful() && !$response->clientError()) {
                        $name = md5($link);
                        File::put("$path/$name.html", $response->body());
                    }
                }
            }
        }
    }
}
