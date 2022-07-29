<?php

namespace Huztw\ModelSupport;

use Illuminate\Support\ServiceProvider;

class ModelSupportServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        // Register the service the package provides.
        $this->app->singleton('model-support', function ($app) {
            return new ModelSupport;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['model-support'];
    }
}
