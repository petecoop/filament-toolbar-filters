<?php

declare(strict_types=1);

namespace Petecoop\FilamentToolbarFilters\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider;
use Petecoop\FilamentToolbarFilters\ToolbarFiltersPlugin;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel->id('testing')->path('testing')->plugin(ToolbarFiltersPlugin::make());
    }
}
