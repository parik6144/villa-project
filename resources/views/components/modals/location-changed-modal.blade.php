<x-filament::modal id="{{ $getName() }}" :width="'xl'">
    <div class="text-xl font-bold text-center">
        {{ $getHeading() }}
    </div>

    <div class="text-lg text-center">
        {!! $getDescription() !!}
    </div>

    <div class="filament-modal__body">
        <div class="flex flex-col gap-4" x-data="{ state: @entangle($getStatePath()) }" x-init="console.log('Initial state:', state)">
            <label class="flex items-start space-x-2 gap-2">
                <input class="mt-1" type="radio" wire:model="{{ $getStatePath() }}" value="incorrect" />
                <span>My address is correct and the selected point on the map is wrong</span>
            </label>

            <label class="flex items-start space-x-2 gap-2">
                <input class="mt-1" type="radio" wire:model="{{ $getStatePath() }}" value="correct" />
                <span>The selected point on the map is correct</span>
            </label>

            <label class="flex items-start space-x-2 gap-2">
                <input class="mt-1" type="radio" wire:model="{{ $getStatePath() }}" value="save_both" />
                <span>Both my address and the point on the map are correct</span>
            </label>
        </div>
    </div>

    <div class="w-full flex justify-center mt-3">
        @if ($actions = $getActions())
            <x-filament::actions :actions="$actions" />
        @endif
    </div>

</x-filament::modal>
