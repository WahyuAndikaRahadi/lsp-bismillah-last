<?php

namespace App\Providers;

use App\Models\SurveillancePlan;
use App\Models\SurveillanceReport;
use App\Policies\SurveillancePolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(SurveillancePlan::class, SurveillancePolicy::class);
        Gate::policy(SurveillanceReport::class, SurveillancePolicy::class);
    }
}
