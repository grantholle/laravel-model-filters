<?php

namespace GrantHolle\LaravelModelFilters;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use GrantHolle\LaravelModelFilters\Commands\LaravelModelFiltersCommand;

class LaravelModelFiltersServiceProvider extends PackageServiceProvider
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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-model-filters_table')
            ->hasCommand(LaravelModelFiltersCommand::class);
    }
}
