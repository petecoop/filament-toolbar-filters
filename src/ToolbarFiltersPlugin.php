<?php

declare(strict_types=1);

namespace Petecoop\FilamentToolbarFilters;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Tables\View\TablesRenderHook;

class ToolbarFiltersPlugin implements Plugin
{
    protected string $renderHook = TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER;

    public static function make(): static
    {
        return new static;
    }

    public function getId(): string
    {
        return 'filament-toolbar-filters';
    }

    public function renderHook(string $hook): static
    {
        $this->renderHook = $hook;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            $this->renderHook,
            static fn () => view('filament-toolbar-filters::toolbar-filters'),
        );
    }

    public function boot(Panel $panel): void {}
}
