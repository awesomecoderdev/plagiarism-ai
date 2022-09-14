<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AIRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class PlagiarismAIController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AIRequest $request)
    {
        $inputs = $request->only(["search", "lang"]);
        $ai = new PlagiarismAI($request->input("search"));
        $ai->lang = $request->input("lang");
        // $ai->run();
        // $ai->process();

        return Response::json([
            "success" => true,
            "status" => $ai->status,
            // "url" => $ai->url,
            "message" => $inputs,
            // "links" => $ai->links,
        ], HttpResponse::HTTP_OK);
    }
}
