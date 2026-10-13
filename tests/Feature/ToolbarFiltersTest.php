<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Livewire\Livewire;
use Petecoop\FilamentToolbarFilters\Tests\Fixtures\User;
use Petecoop\FilamentToolbarFilters\Tests\Fixtures\UsersTable;
use Petecoop\FilamentToolbarFilters\ToolbarFiltersPlugin;

it('auto-registers the table macro and boots as a panel plugin', function () {
    expect(Table::hasMacro('toolbarFilters'))->toBeTrue();
    expect(Filament::getPanel('testing')->getPlugin('filament-toolbar-filters'))
        ->toBeInstanceOf(ToolbarFiltersPlugin::class);
});

it('renders the native field once in the toolbar with the grouping selector', function () {
    $component = Livewire::test(UsersTable::class)->assertSee('data-toolbar-filters', escape: false);
    $html = $component->html();
    expect(substr_count($html, needle: 'role="switch"'))->toBe(1);
    expect($html)->toContain('Group by');
    expect($component->instance()->getTableFiltersForm()->toHtml())
        ->toContain('Menu match')
        ->not->toContain('Verified');
});

it('keeps ordinary tables unchanged when they do not opt in', function () {
    Livewire::test(UsersTable::class, ['useToolbar' => false])
        ->assertDontSee('data-toolbar-filters', escape: false);
});

it('combines querying searching and menu filters without a toolbar indicator', function () {
    $match = User::create(['name' => 'Match', 'email_verified_at' => now()]);
    $other = User::create(['name' => 'Other', 'email_verified_at' => now()]);
    $unverified = User::create(['name' => 'Match']);

    $component = Livewire::test(UsersTable::class)
        ->filterTable('verified')
        ->assertCanSeeTableRecords([$match, $other])
        ->assertCanNotSeeTableRecords([$unverified])
        ->assertDontSee('Active filters')
        ->searchTable('Other')
        ->assertCanSeeTableRecords([$other])
        ->assertCanNotSeeTableRecords([$match])
        ->searchTable('')
        ->filterTable('match')
        ->assertCanSeeTableRecords([$match])
        ->assertCanNotSeeTableRecords([$other, $unverified])
        ->assertSee('Active filters');

    expect($component->instance()->getTable()->getFilterIndicators())->toHaveCount(1);
    expect($component->instance()->getTable()->getFiltersTriggerAction()->getExtraAttributes())
        ->not
        ->toHaveKey('style');

    $component->call('removeTableFilters')->assertCanSeeTableRecords([$match, $other, $unverified]);
});

it('loads the native URL state and resets the lifted field', function () {
    $verified = User::create(['name' => 'Verified', 'email_verified_at' => now()]);
    $unverified = User::create(['name' => 'Unverified']);

    Livewire::withQueryParams(['filters' => ['verified' => ['isActive' => true]]])
        ->test(UsersTable::class)
        ->assertSet('tableFilters.verified.isActive', true)
        ->assertCanSeeTableRecords([$verified])
        ->assertCanNotSeeTableRecords([$unverified])
        ->call('resetTableFiltersForm')
        ->assertSet('tableFilters.verified.isActive', false)
        ->assertCanSeeTableRecords([$verified, $unverified]);
});

it('allows the panel to choose another toolbar hook', function () {
    $panel = Panel::make()->id('custom')->plugin(
        ToolbarFiltersPlugin::make()->renderHook(TablesRenderHook::TOOLBAR_SEARCH_BEFORE),
    );

    $panel->boot();
    expect(FilamentView::hasRenderHook(TablesRenderHook::TOOLBAR_SEARCH_BEFORE))->toBeTrue();
});

it('hides the empty menu when all filters are lifted', function () {
    $component = Livewire::test(new class extends UsersTable {
        public function table(Table $table): Table
        {
            return parent::table($table)->toolbarFilters(['verified', 'match']);
        }
    });

    $table = $component->instance()->getTable();
    expect($table->getFiltersTriggerAction()->getExtraAttributes()['style'])->toContain('display: none');
    expect($component->instance()->getTableFiltersForm()->toHtml())->not->toContain('Verified', 'Menu match');
    $component->assertSee('data-toolbar-filters', escape: false);
});

it('reports unknown filter names instead of silently ignoring them', function () {
    $table = Livewire::test(UsersTable::class)->instance()->getTable();

    expect(fn () => $table->toolbarFilters(['missing']))
        ->toThrow(InvalidArgumentException::class, 'Unknown toolbar filter [missing]');
});

it('leaves filter configuration alone for an empty toolbar filter list', function () {
    $table = Livewire::test(UsersTable::class, ['useToolbar' => false])->instance()->getTable();

    expect($table->toolbarFilters([]))->toBe($table);
    expect($table->hasDeferredFilters())->toBeTrue();
});
