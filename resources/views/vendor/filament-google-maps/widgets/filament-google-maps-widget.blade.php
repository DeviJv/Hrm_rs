@php
    $heading     = $this->getHeading();
    $filters     = $this->getFilters();
    $icon        = $this->getIcon();
    $collapsible = $this->getCollapsible();
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        class="filament-google-maps-widget"
        :icon="$icon"
        :collapsible="$collapsible"
    >
        <x-slot name="heading">
            {{ $heading }}
        </x-slot>

        @if ($filters)
            <x-slot name="headerEnd">
                <x-filament::input.wrapper
                    inline-prefix
                    wire:target="filter"
                    class="-my-2"
                >
                    <x-filament::input.select
                        inline-prefix
                        wire:model.live="filter"
                    >
                        @foreach ($filters as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-slot>
        @endif

        <div
            {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}=\"updateMapData\"" : '' !!}
        >
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-google-maps-widget', 'cheesegrits/filament-google-maps') }}"
                x-data="filamentGoogleMapsWidget({
                            cachedData: {{ json_encode($this->getCachedData()) }},
                            config: {{ $this->getMapConfig() }},
                            mapEl: $refs.map,
                        })"
                wire:ignore
                @if ($maxHeight = $this->getMaxHeight())
                    style=" max-height: {{ $maxHeight }}"
                @endif
            >
                <div
                    x-ref="map"
                    class="w-full"
                    style="
                        min-height: {{ $this->getMinHeight() }};
                        z-index: 1 !important;
                    "
                ></div>
            </div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
   @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('🧠 DOM Loaded for map');

        let tries = 0;

        const waitForMap = setInterval(() => {
            const el = [...document.querySelectorAll('[x-data]')]
                .find(el => el.getAttribute('x-data')?.startsWith('filamentGoogleMapsWidget'));

            if (!el || !el.__x?.$data) {
                tries++;
                if (tries > 20) {
                    clearInterval(waitForMap);
                    console.warn('❌ Gagal akses Alpine map instance setelah 20x cek');
                }
                return;
            }

            clearInterval(waitForMap);

            const instance = el.__x.$data;
            if (!instance.markers?.length) {
                console.warn('❌ Markers masih kosong');
                return;
            }

            console.log(`✅ Marker instance loaded: ${instance.markers.length} marker(s)`);

            instance.markers.forEach((marker, i) => {
                marker.addListener('click', () => {
                    alert(`🟢 Marker ${i + 1} clicked!`);
                });
            });

            console.log('🟢 Marker click listeners attached');
        }, 500); // cek setiap 500ms
    });
</script>
@endpush
</x-filament-widgets::widget>
