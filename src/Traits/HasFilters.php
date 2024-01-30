<?php

namespace GrantHolle\ModelFilters\Traits;

use GrantHolle\ModelFilters\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Pipeline;

trait HasFilters
{
    public function scopeFilter(Builder $builder, Collection|array $data)
    {
        $filters = $this->filtersByKey();
        $pipes = collect($data)
            ->filter(function ($set, $key) use ($filters) {
                if (is_array($set)) {
                    return $filters->has($set['key'])
                        && isset($set['key'])
                        && isset($set['value']);
                }

                return $filters->has($key);
            })
            ->map(function ($set, $key) use ($filters) {
                if (! is_array($set)) {
                    $set = ['key' => $key, 'value' => $set];
                }

                /** @var BaseFilter $base */
                $base = $filters->get($set['key']);
                $filter = clone $base;
                $filter
                    ->when(
                        isset($set['operator']),
                        fn (BaseFilter $filter) => $filter->withOperator($set['operator'])
                    )
                    ->withValue($set['value']);

                return $filter;
            })
            ->toArray();

        return Pipeline::send($builder)
            ->through($pipes)
            ->thenReturn();
    }

    public function filters(): array
    {
        return [];
    }

    public function availableFiltersToArray(): array
    {
        return collect($this->filters())
            ->filter(fn (BaseFilter $filter) => $filter->showAsAvailable)
            ->map(fn (BaseFilter $filter) => $filter->toArray())
            ->values()
            ->toArray();
    }

    public function filtersByKey(): Collection
    {
        return collect($this->filters())
            ->keyBy(fn (BaseFilter $filter) => $filter->key);
    }
}
