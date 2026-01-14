<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\RouteHelper;
use App\Models\Lead;
use App\Observers\LeadObserver;

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
        // Register Blade directive for dynamic routes
        Blade::directive('dynamicRoute', function ($expression) {
            return "<?php echo route(\App\Helpers\RouteHelper::route($expression)); ?>";
        });

        Blade::directive('dynamicRouteIs', function ($expression) {
            return "<?php if(\App\Helpers\RouteHelper::routeIsAny($expression)): ?>";
        });

        Blade::directive('endDynamicRouteIs', function () {
            return "<?php endif; ?>";
        });

        // Register Lead observer for automatic conversion
        Lead::observe(LeadObserver::class);
    }
}
