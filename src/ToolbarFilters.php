<?php

declare(strict_types=1);

namespace Petecoop\FilamentToolbarFilters;

use Filament\Actions\Action;
use Filament\Tables\Table;
use InvalidArgumentException;

class ToolbarFilters
{
    /** @param array<string> $names */
    public static function configure(Table $table, array $names): Table
    {
        if ($names === []) {
            return $table;
        }

        $names = array_values(array_unique($names));

        foreach ($names as $name) {
            if (! $table->getFilter($name, withHidden: true)) {
                throw new InvalidArgumentException(
                    "Unknown toolbar filter [{$name}]. Define filters before calling toolbarFilters().",
                );
            }
        }

        foreach ($names as $name) {
            $table->getFilter($name)?->indicateUsing(static fn (): array => []);
        }

        return $table
            ->deferFilters(false)
            ->filtersFormSchema(static function (array $filters) use ($table, $names): array {
                foreach ($names as $name) {
                    $filter = $table->getFilter($name);

                    if (! $filter) {
                        continue;
                    }

                    $filters[$name] = ToolbarFilterGroup::make($filter->getSchemaComponents())
                        ->statePath($name)
                        ->key($name)
                        ->columnSpan($filter->getColumnSpan())
                        ->columnStart($filter->getColumnStart())
                        ->columns($filter->getColumns());
                }

                return array_values($filters);
            })
            ->filtersTriggerAction(static fn (Action $action): Action => $action->extraAttributes(
                static fn (): array => (
                    array_diff(array_keys($table->getFilters()), $names) === []
                        ? ['style' => 'display: none']
                        : []
                ),
                merge: true,
            ));
    }
}
