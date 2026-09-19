<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force Scramble to document every single route belonging to your API Controller group
        Scramble::routes(function (Route $route) {
            return str_contains($route->getActionName(), 'App\Http\Controllers\Api');
        });
    }
}
