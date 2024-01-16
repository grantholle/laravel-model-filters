<?php

namespace GrantHolle\LaravelModelFilters\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \GrantHolle\LaravelModelFilters\LaravelModelFilters
 */
class LaravelModelFilters extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \GrantHolle\LaravelModelFilters\LaravelModelFilters::class;
    }
}
