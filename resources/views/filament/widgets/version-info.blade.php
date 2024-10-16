<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">
                <a href="#" rel="noopener noreferrer" target="_blank">
                    <img src="{{ asset('images/logo 1.svg') }}" class="h-20 object-fill" />
                </a>

                {{-- <p class=" mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Cash-Craft
                </p> --}}
            </div>

            <div class="flex flex-col items-end gap-y-1">
                <x-filament::link color="gray" icon="heroicon-m-book-open"
                    icon-alias="panels::widgets.filament-info.open-documentation-button" rel="noopener noreferrer"
                    target="_blank">
                    Version V1.0
                </x-filament::link>

                <x-filament::link color="gray" icon-alias="panels::widgets.filament-info.open-github-button"
                    rel="noopener noreferrer" target="_blank">


                    Stable Channel
                </x-filament::link>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
