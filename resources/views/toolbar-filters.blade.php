@if ($this instanceof \Filament\Tables\Contracts\HasTable)
    @php
        $toolbarGroups = array_filter(
            $this->getTableFiltersForm()->getComponents(),
            static fn($group) => $group instanceof \Petecoop\FilamentToolbarFilters\ToolbarFilterGroup &&
                $group->isVisible(),
        );
    @endphp

    @if ($toolbarGroups)
        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 1rem" data-toolbar-filters>
            @foreach ($toolbarGroups as $group)
                <div wire:key="{{ $this->getId() }}.toolbar-filter.{{ $group->getKey() }}">
                    {!! $group->toHtml() !!}
                </div>
            @endforeach
        </div>
    @endif
@endif
