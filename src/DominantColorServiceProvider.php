<?php

namespace CapsulesCodes\DominantColor;

use Illuminate\Support\ServiceProvider;
use CapsulesCodes\DominantColor\DominantColor;

class DominantColorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/dominant-color.php' => config_path('dominant-color.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('dominant-color', function () {
            return new DominantColor();
        });

        $this->mergeConfigFrom(
            __DIR__.'/config/dominant-color.php',
            'dominant-color'
        );
    }
}
