<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Destination;
use App\Models\Event;
use App\Policies\ArticlePolicy;
use App\Policies\DestinationPolicy;
use App\Policies\EventPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(Destination::class, DestinationPolicy::class);
        Gate::policy(Event::class, EventPolicy::class);
        Paginator::useBootstrapFive();
    }
}
