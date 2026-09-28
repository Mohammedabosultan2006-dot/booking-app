<?php

namespace App\Providers;

use App\Models\{Booking, Service};
use App\Policies\{BookingPolicy, ServicePolicy};
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
    }
}
