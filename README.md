 # Laravel Model Filters

[![Latest Version on Packagist](https://img.shields.io/packagist/v/grantholle/laravel-model-filters.svg?style=flat-square)](https://packagist.org/packages/grantholle/laravel-model-filters)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/grantholle/laravel-model-filters/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/grantholle/laravel-model-filters/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/grantholle/laravel-model-filters/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/grantholle/laravel-model-filters/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/grantholle/laravel-model-filters.svg?style=flat-square)](https://packagist.org/packages/grantholle/laravel-model-filters)

A composable way to filter Laravel models. This is not exhaustive, but it can add some basic filtering to your models.

## Installation

You can install the package via composer:

```bash
composer require grantholle/laravel-model-filters
```

By default, the package expects that filters are stored in the `f` key of the request. You can change this by adding the environment variable `MODEL_FILTERS_KEY` to your `.env` file.

```
MODEL_FILTERS_KEY=filter
```

## Usage

The first step is registering the filters for the desired model. In the model, add the `HasFilters` trait and define the filters in the `filters` method.

```php
use GrantHolle\ModelFilters\Enums\Component;
use GrantHolle\ModelFilters\Filters\MultipleSelectFilter;
use GrantHolle\ModelFilters\Filters\TextFilter;
use GrantHolle\ModelFilters\Traits\HasFilters;

class User extends Authenticatable implements ExistsInSis
{
    use HasFilters;
    
    public function filters(): array
    {
        return [
            TextFilter::make('search', __('Search'))
                // Exclude from the list of available filters (see below when not present on `availableFiltersToArray()`
                ->hide()
                // By default, the filter will try to construct the query based on the supplied operator and value.
                // If that doesn't meet your needs, you can define the query parameters yourself. It should
                // return an instance of `Illuminate\Database\Eloquent\Builder`.
                ->using(fn (Builder $builder, string $search) => $builder->search($search)),
            // The first argument is the key that will be used to filter the model. The second argument is the label    
            TextFilter::make('first_name', __('First name')),
            TextFilter::make('last_name', __('Last name')),
            MultipleSelectFilter::make('user_type', __('Checkbox group'))
                // For filters that can have multiple values/choices, you can
                // define the options. How it's constructed is up to you, since
                // the frontend is implemented independently.
                ->options(UserType::options()),
            MultipleSelectFilter::make('user_type', __('Combobox'))
                ->withComponent(Component::combobox)
                ->options(UserType::options()),
        ];
    }
}
```

Once your filters are defined, you can get the list of the available filters by calling the `availableFiltersToArray` on the model. This allows you to implement the frontend however you want.

```php
(new User())->availableFiltersToArray();

// This is the output
$filters = [
    [
        "key" => "first_name",
        "label" => "First name",
        "component" => "text",
        "operators" => [
            "contains" => "Contains",
            "not_contains" => "Doesn't contain",
            "starts" => "Starts with",
            "not_starts" => "Doesn't start with",
            "ends" => "Ends with",
            "not_ends" => "Doesn't end with",
        ],
        "props" => [],
        "defaultValue" => null,
    ],
    [
        "key" => "last_name",
        "label" => "Last name",
        "component" => "text",
        "operators" => [
            "contains" => "Contains",
            "not_contains" => "Doesn't contain",
            "starts" => "Starts with",
            "not_starts" => "Doesn't start with",
            "ends" => "Ends with",
            "not_ends" => "Doesn't end with",
        ],
        "props" => [],
        "defaultValue" => null,
    ],
    [
        "key" => "user_type",
        "label" => "Checkbox group",
        "component" => "checkbox_group",
        "operators" => [
            "in" => "In",
            "not_in" => "Not in",
        ],
        "props" => [
            "options" => [
                "staff" => "Staff",
                "guardian" => "Contact",
                "student" => "Student",
            ],
        ],
        "defaultValue" => [],
    ],
    [
        "key" => "user_type",
        "label" => "Combobox",
        "component" => "combobox",
        "operators" => [
            "in" => "In",
            "not_in" => "Not in",
        ],
        "props" => [
            "options" => [
                "staff" => "Staff",
                "guardian" => "Contact",
                "student" => "Student",
            ],
        ],
        "defaultValue" => [],
    ],
    [
        "key" => "user_type",
        "label" => "Select",
        "component" => "combobox",
        "operators" => [
            "in" => "In",
            "not_in" => "Not in",
        ],
        "props" => [
            "options" => [
                "staff" => "Staff",
                "guardian" => "Contact",
                "student" => "Student",
            ],
        ],
        "defaultValue" => [],
    ],
];
```

The request should use the key defined in the environment (by default `f`) along with the filter details. Take the following query string:

```
?f[0][key]=first_name&f[0][operator]=starts&f[0][value]=gr
```

This will be expanded in the request to the following:

```php
[
    [
        "key" => "first_name",
        "operator" => "starts",
        "value" => "gr"
    ]
]
```

In your controller, you can call `currentFilters` on the request to obtain the filters that should be applied to the model.

```php
public function index(Request $request)
{
    $filters = $request->currentFilters();
    $users = User::filter($filters)
        ->get();
    
    // ...
}
```

If you'd like to filter a model manually, the following structure should be used:

```php
use GrantHolle\ModelFilters\Enums\Operator;

$filters = [
    [
        "key" => "first_name", // The key should match the key in the filter definition
        "operator" => "contains", // You can also use the Operator::contains enum
        "value" => "an" // This is the value by which to filter
    ],
    [
        "key" => "first_name",
        "operator" => Operator::not_starts_with,
        "value" => "Gr"
    ]
];

User::filter($filters)->pluck("first_name");
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Grant Holle](https://github.com/grantholle)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
