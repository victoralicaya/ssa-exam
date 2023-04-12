<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Route::macro('softDeletes', function ($uri, $controller) {
            Route::get("{$uri}/trashed", "{$controller}@trashed")->name("{$uri}.trashed");
            Route::patch("{$uri}/{userId}/restore", "{$controller}@restore")->name("{$uri}.restore");
            Route::delete("{$uri}/{user}/delete", "{$controller}@destroy")->name("{$uri}.destroy");
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->register(RouteMacroServiceProvider::class);
    }
}
