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

        // Custom route model binding for Lead to handle soft-deleted models
        // This allows accessing leads that are soft-deleted (for conversion operations)
        \Illuminate\Support\Facades\Route::bind('lead', function ($value) {
            // For conversion and update-stage routes, we need to access the lead even if it might be soft-deleted
            // But for other routes, we should only get non-deleted leads
            $routeName = request()->route()?->getName();
            $conversionRoutes = [
                'admin.leads.convert',
                'admin.leads.update-stage',
                'superadmin.leads.convert',
                'superadmin.leads.update-stage'
            ];

            if (in_array($routeName, $conversionRoutes)) {
                // For conversion routes, include soft-deleted leads (in case conversion happens during the request)
                $lead = Lead::withTrashed()->find($value);
                if (!$lead) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                        "No query results for model [App\Models\Lead] {$value}"
                    );
                }
                return $lead;
            }

            // For other routes, only get non-deleted leads
            return Lead::findOrFail($value);
        });
    }
}
