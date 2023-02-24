<?php

namespace App\Providers;

use App\Events\AuthenticatedDraftUserEvent;
use App\Events\CreatingBlockAdminEvent;
use App\Events\CreatingBlockManagerEvent;
use App\Listeners\AuthenticatedDraftUserListener;
use App\Listeners\CreatingBlockAdminListener;
use App\Listeners\CreatingBlockManagerListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        CreatingBlockManagerEvent::class => [
            CreatingBlockManagerListener::class,
        ],

        CreatingBlockAdminEvent::class => [
            CreatingBlockAdminListener::class,
        ],

        AuthenticatedDraftUserEvent::class => [
            AuthenticatedDraftUserListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
