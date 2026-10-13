# Filament Toolbar Filters

Move selected filters into the toolbar.

There have been a few attempts for more configurable filters in Filament e.g. [#20150](https://github.com/filamentphp/filament/pull/20150). And it looks like it will get an official implementation in [#20140](https://github.com/filamentphp/filament/issues/20140), until then this is my implementation to relocate a few filters.

## Installation

```bash
composer require petecoop/filament-toolbar-filters
```

Add the plugin to your Panel:

```php
use Petecoop\FilamentToolbarFilters\ToolbarFiltersPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(ToolbarFiltersPlugin::make());
}
```

## Usage

Define your filters normally and choose which ones to display in the toolbar using `toolbarFilters`.

```php
use Filament\Tables\Filters\Filter;

return $table
    ->columns([
        // ...
    ])
    ->filters([
        Filter::make('is_verified')
            ->label('Verified')
            ->toggle()
            ->query(static fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
        Filter::make('another_filter'),
    ])
    ->toolbarFilters(['is_verified']);
```

## Position

The default is `TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER`, inside the
start of the toolbar after the grouping selector. It works when search is absent
as well. You can choose another hook when registering the panel plugin:

```php
use Filament\Tables\View\TablesRenderHook;

ToolbarFiltersPlugin::make()
    ->renderHook(TablesRenderHook::TOOLBAR_SEARCH_BEFORE)
```

Use a hook inside the table toolbar.

## Current constraints

- The macro sets **all** table filters to apply immediately (`deferFilters(false)`).
  A deferred menu combined with immediate toolbar filters is not supported.
- The macro uses `filtersFormSchema()` and `filtersTriggerAction()` internally.
  Custom callbacks for these methods are not currently supported: callbacks set
  before `toolbarFilters()` are replaced, and callbacks set afterwards replace
  the plugin's callbacks. This can break toolbar rendering and indicator
  suppression, or leave an empty filter menu trigger visible.
