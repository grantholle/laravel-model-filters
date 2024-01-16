<?php

namespace GrantHolle\LaravelModelFilters\Commands;

use Illuminate\Console\Command;

class LaravelModelFiltersCommand extends Command
{
    public $signature = 'laravel-model-filters';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
