<?php

declare(strict_types=1);

namespace Petecoop\FilamentToolbarFilters;

use Filament\Schemas\Components\Group;

class ToolbarFilterGroup extends Group
{
    public function toSchemaHtml(?bool $isVisible = null): string
    {
        // Keep the group in the native schema for hydration, validation and resets,
        // but render it only through the toolbar hook using toHtml().
        return '';
    }
}
