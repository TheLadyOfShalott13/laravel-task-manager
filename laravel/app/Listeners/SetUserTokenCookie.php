<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class SetUserTokenCookie
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param Authenticated $event
     * @return void
     * Function to fire after the Authentication of a user
     * Sets the token for site wide user API usage
     */
    public function handle(Authenticated $event): void
    {
        $token_name             = config('constants.USER_API_TOKEN_COOKIE');
        $logged_in_user         = $event->user;
        $authorization_token    = $logged_in_user->createToken($token_name)->accessToken;
        $cookie                 = Cookie::make(
                                    $token_name,
                                    $authorization_token,
                                    60 * 24 * 365,  //put a large value ie 1 year just to keep flexibility in the session of the user
                                    '/',
                                    null,
                                    true,
                                    false,
                                    'strict'
                                );

        Cookie::queue($cookie);
        Log::info($token_name.' token cookie has been set for user: ' . $event->user->email);
    }
}
