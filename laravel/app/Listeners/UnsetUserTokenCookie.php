<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class UnsetUserTokenCookie
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param Logout $event
     * @return void
     * Function to unset the user api access token cookie
     * After the user successfully logs out
     */
    public function handle(Logout $event): void
    {
        $token_name = config('constants.USER_API_TOKEN_COOKIE');
        Cookie::queue(Cookie::forget($token_name));
        Log::info($token_name.' token cookie has been unset for user: ' . $event->user->email);
    }
}
