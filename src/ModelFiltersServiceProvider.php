<?php

namespace GrantHolle\ModelFilters;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use GrantHolle\ModelFilters\Commands\ModelFiltersCommand;

class ModelFiltersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-model-filters')
            ->hasConfigFile();
    }
}
