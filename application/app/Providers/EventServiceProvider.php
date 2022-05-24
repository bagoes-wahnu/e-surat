<?php

namespace App\Providers;

use App\Events\SuratKeluarUploadPdf;
use App\Listeners\SuratKeluarOpenPdf;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SuratKeluarUploadPdf::class => [
            SuratKeluarOpenPdf::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
