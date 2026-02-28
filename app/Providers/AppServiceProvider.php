<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(UrlGenerator $url): void
    {
        Paginator::defaultView('vendor.pagination.custom');
        if (config('app.env') === 'production') {
            $url->forceScheme('https');
        }
    }
}
