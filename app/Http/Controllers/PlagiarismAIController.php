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
        $input = $request->only(["search", "lang"]);

        return Response::json([
            "success" => true,
            "message" => $request->all(),
        ], HttpResponse::HTTP_OK);
    }
}
