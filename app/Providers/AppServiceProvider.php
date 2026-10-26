<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\Ticket;
use App\Policies\SitePolicy;
use App\Policies\TicketPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Site::class, SitePolicy::class);

        RateLimiter::for('site-api', function (Request $request) {
            /** @var Site|null $site */
            $site = $request->attributes->get('site');

            $key = $site instanceof Site
                ? 'site:'.$site->id
                : 'ip:'.$request->ip();

            return Limit::perMinute(30)->by($key);
        });
    }
}
