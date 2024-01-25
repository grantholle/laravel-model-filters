<?php

namespace GrantHolle\ModelFilters\Filters;

use GrantHolle\ModelFilters\Enums\Component;
use GrantHolle\ModelFilters\Enums\Operator;

class MultipleSelectFilter extends BaseFilter
{
    public array $operators = [
        Operator::in,
        Operator::not_in,
    ];

    public Component $component = Component::checkbox_group;

    public function defaultValue(): array
    {
        return [];
    }
}
