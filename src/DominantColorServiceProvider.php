<?php

namespace CapsulesCodes\DominantColor;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use CapsulesCodes\DominantColor\DominantColor;

class DominantColorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('dominant-color')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(DominantColor::class);
        $this->app->alias(DominantColor::class, 'dominant-color');
    }
}
