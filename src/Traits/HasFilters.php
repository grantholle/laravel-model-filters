<?php

namespace GrantHolle\ModelFilters\Traits;

use GrantHolle\ModelFilters\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Pipeline;

trait HasFilters
{
    public function scopeFilter(Builder $builder, array $data)
    {
        $filters = $this->filtersByKey();
        $pipes = collect(Arr::isAssoc($data) ? [$data] : $data)
            ->filter(fn (array $set) => isset($set['key'])
                && isset($set['value'])
                && $filters->has($set['key']))
            ->map(function (array $set) use ($filters) {
                /** @var BaseFilter $base */
                $base = $filters->get($set['key']);
                $filter = clone $base;
                $filter->withOperator($set['operator'] ?? null)
                    ->withValue($set['value']);

                return $filter;
            })
            ->toArray();

        return Pipeline::send($builder)
            ->through($pipes)
            ->thenReturn();
        return $builder->where(
            fn (Builder $builder) => Pipeline::send($builder)
                ->through($pipes)
                ->thenReturn()
        );
    }

    public function filters(): array
    {
        return [];
    }

    public function filtersToArray(): array
    {
        return collect($this->filters())
            ->map(fn (BaseFilter $filter) => $filter->toArray())
            ->toArray();
    }

    public function filtersByKey(): Collection
    {
        return collect($this->filters())
            ->keyBy(fn (BaseFilter $filter) => $filter->key);
    }
}
