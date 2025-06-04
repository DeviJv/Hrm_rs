<x-filament-widgets::widget>
       <x-filament::section>
         <div
            id="custom-map"
            style="width: 100%; height: 500px;"
            data-markers='@json($markers ?? [])'
        ></div>
    </x-filament::section>
    <script>
    console.log('kontol')
     </script>
    <script src="/js/custom-map.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initCustomMap"
        async defer>
    </script>
</x-filament-widgets::widget>
