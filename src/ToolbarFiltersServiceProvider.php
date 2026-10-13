<?php

declare(strict_types=1);

namespace Petecoop\FilamentToolbarFilters;

use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class ToolbarFiltersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-toolbar-filters');

        // Macroable binds this closure to the Table instance at call time.
        Table::macro('toolbarFilters', fn (array $names): Table => ToolbarFilters::configure($this, $names));
    }
}
