<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\AuthUserRequest;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\StoreUserRequest;
use App\Notifications\UserNotification;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Response as HttpResponse;
use App\Notifications\UserRegisteredNotification;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Response::json([
            "success" => true,
            "message" => "Index ",
            "data" => User::all(),
        ], HttpResponse::HTTP_OK, [
            "X-AwesomeMailer" => true,
        ], 0);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {

        $input = $request->only(["name", "email"]);
        $input["password"] = bcrypt($request->input("password"));

        $user = User::create($input);

        return Response::json([
            "success" => true,
            "message" => "User successfully created!",
            "data" => $request->all(),
            "token" => $user->createToken($request->input("name"))->plainTextToken,
        ], HttpResponse::HTTP_CREATED);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function auth(AuthUserRequest $request)
    {
        if (Auth::attempt(["email" => $request->email, "password" => $request->password])) {
            $user = Auth::user();
            DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
            $token = $user->createToken($user->name)->plainTextToken;
            return Response::json(
                [
                    "success" => true,
                    "message" => "Successfully Authorized.",
                    "token" => $token,
                ],
                HttpResponse::HTTP_OK
            );
            // )->withCookie(Cookie::make('next_cors', $token, (60 * 24), "/", "localhost",));
        }
        return Response::json([
            'success'   => false,
            'message'   => 'Unauthorized access.',
        ], HttpResponse::HTTP_OK);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        // return $request->user()->notifications;
        // $request->user()->notify(new UserRegisteredNotification($request->user()));
        // Notification::send(User::all(), new UserNotification("Hello how are you."));

        // return DB::table('notifications')->select(["id", "created_at", "read_at", "data"])->where("notifiable_id", $request->user()->id)->get();

        return new UserCollection(
            Cache::remember("user_" . $request->user()->id, 60 * 60, function () use ($request) {
                return $request->user();
            })
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function notifications(Request $request)
    {
        // $request->user()->notify(new UserNotification("Hello how are you."));
        // Notification::send(User::all(), new UserNotification("Hello how are you."));

        $notifications = Cache::remember("notifications_" . $request->user()->id, 60 * 60, function () use ($request) {
            return $request->user()->notifications;
        });

        return Response::json([
            "success" => true,
            "message" => "Successfully Authorized.",
            "data" => $notifications,
        ], HttpResponse::HTTP_OK);

        // ->withHeaders(
        //     [
        //         // "Set-Cookie" =>  Cookie::make('NEXT-CSRF', $request->user()->currentAccessToken()->token, (60 * 24), "/", ".localhost", true, true, false, "strict")
        //         "X-AwesomeCoder" => PersonalAccessToken::findToken($request->user()->currentAccessToken()->token),
        //     ]
        // )->withCookie(Cookie::make('NEXT-CSRF', $request->user()->currentAccessToken()->token, (60 * 24), "/", ".localhost", true, true, false, "strict"));
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function markAsReadNotification(Request $request)
    {
        Cache::forget("notifications_" . $request->user()->id);
        $request->user()->unreadNotifications->when($request->input('id'), function ($query) use ($request) {
            return $query->where('id', $request->input("id"));
        })->markAsRead();

        $notifications = Cache::remember("notifications_" . $request->user()->id, 60 * 60, function () use ($request) {
            return $request->user()->notifications;
        });

        return Response::json([
            "success" => true,
            "message" => "Successfully Authorized.",
            "data" => $notifications,
        ], HttpResponse::HTTP_OK);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
