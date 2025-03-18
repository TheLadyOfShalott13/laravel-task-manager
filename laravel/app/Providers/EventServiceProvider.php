<?php

namespace App\Providers;

use App\Listeners\SetUserTokenCookie;
use App\Listeners\UnsetUserTokenCookie;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Authenticated::class    => [ SetUserTokenCookie::class ],       // set the user token for site wise API usage
        Logout::class           => [ UnsetUserTokenCookie::class ],     // unset the user token for site wise API usage
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Custom event registration can go here
    }
}
