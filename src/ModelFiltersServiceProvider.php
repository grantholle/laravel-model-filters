<?php

namespace GrantHolle\ModelFilters;

use Illuminate\Http\Request;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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

    public function packageBooted()
    {
        Request::macro('currentFilters', function () {
            /** @var Request $this */
            return $this->collect(config('model-filters.filter_key'))
                ->mapWithKeys(function (array $filter, $key) {
                    return [$key => [
                        'key' => $filter['key'],
                        'operator' => $filter['operator'] ?? null,
                        'value' => $filter['value'] ?? null,
                    ]];
                });
        });

        Request::macro('addFilter', function (string|array $key, mixed $value = null) {
            $data = $this->all();
            $filters = $data[config('model-filters.filter_key')] ?? [];
            $filters[$key] = [
                'key' => $key,
                'value' => $value,
            ];
            $data[config('model-filters.filter_key')] = $filters;
            $this->merge($data);

            return $this;
        });
    }
}
