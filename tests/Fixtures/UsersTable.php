<?php

declare(strict_types=1);

namespace Petecoop\FilamentToolbarFilters\Tests\Fixtures;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class UsersTable extends TableWidget
{
    protected static bool $isLazy = false;

    public bool $useToolbar = true;

    public bool $toolbarFirst = false;

    #[Url(as: 'filters')]
    public ?array $tableFilters = null;

    public function table(Table $table): Table
    {
        if ($this->useToolbar && $this->toolbarFirst) {
            $table->toolbarFilters(['verified']);
        }

        $table
            ->query(User::query())
            ->columns([TextColumn::make('name')->searchable()])
            ->groups([Group::make('name')])
            ->filters([
                Filter::make('verified')
                    ->label('Verified')
                    ->toggle()
                    ->query(static fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Filter::make('match')
                    ->label('Menu match')
                    ->query(static fn (Builder $query): Builder => $query->where('name', 'Match')),
            ]);

        return $this->useToolbar && ! $this->toolbarFirst ? $table->toolbarFilters(['verified']) : $table;
    }
}
