<x-filament-widgets::widget>
    <style>
    .gm-style .gm-style-iw {
        max-width: none !important;
        width: 800px !important;
    }
    .gm-style .gm-style-iw > div {
        width: 800px !important;
    }
</style>
    <x-filament::section>
   {{-- <div wire:poll.5s="updateMarkers"> --}}
    
        <div id="custom-map"
            data-markers='@json($markers ?? [])'
            style="width: 100%; height: 600px; min-height: 500px; background: #eee;"
            data-resource-name="bidan-mitras" 
            data-view-action="view" 
            data-edit-action="edit"
            wire:ignore
            >
        </div>
        
</x-filament::section>
    <script>
    function toggleBidanTab(id, tab) {
        const detailEl    = document.getElementById(`detail-${id}`);
        const kunjungEl   = document.getElementById(`kunjungan-${id}`);
        const pasienEl   = document.getElementById(`pasien-${id}`);
        // show / hide
        if (tab === 'detail') {
        detailEl.style.display  = 'block';
        kunjungEl.style.display = 'none';
        pasienEl.style.display = 'none';
        } else  if (tab === 'kunjungan') {
        detailEl.style.display  = 'none';
        kunjungEl.style.display = 'block';
        pasienEl.style.display = 'none';
        }else{
        detailEl.style.display  = 'none';
        kunjungEl.style.display = 'none';
        pasienEl.style.display = 'block';
        }
        
    }
    </script>
    <script>
    window.initCustomMap = function () {
        console.log('✅ initCustomMap dipanggil');

        let attempts = 0;
        const maxAttempts = 20;

        function tryInit() {
            const el = document.getElementById('custom-map');

            if (!el) {
                console.warn('🛑 #custom-map belum tersedia, coba lagi...');
                attempts++;

                if (attempts < maxAttempts) {
                    setTimeout(tryInit, 300);
                } else {
                    console.error('❌ Gagal menemukan #custom-map setelah 20x cek');
                }

                return;
            }

            console.log('🟢 #custom-map ditemukan, lanjut inisialisasi');

            const markers = JSON.parse(el.dataset.markers || '[]');

            const map = new google.maps.Map(el, {
                zoom: 14,
                zoomControl: true,
                mapTypeControl: true,
                scaleControl: true,
                streetViewControl: true,
                rotateControl: true,
                fullscreenControl: true,
            });

            const bounds = new google.maps.LatLngBounds();

            const markerList = [];
            markers.forEach((m) => {
                const iconUrl = {
                    sudah: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                    belum: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                }[m.status] || 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png'; 
                const marker = new google.maps.Marker({
                    position: { lat: m.lat, lng: m.lng },
                    map,
                    title: m.nama,
                    icon: {
                        url: iconUrl,
                        scaledSize: new google.maps.Size(35, 35), // opsional: atur ukuran
                    },
                });
                bounds.extend(marker.getPosition());
                const infoWindow = new google.maps.InfoWindow({
                    content: m.info,      // langsung pakai HTML Blade
                    // maxWidth: 1000,
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                    // google.maps.event.addListenerOnce(infoWindow, 'domready', () => {
                    // // find our unique container by ID
                    // const svEl = document.getElementById(`sv-${m.id}`);
                    // if (!svEl) return;
                    //     new google.maps.StreetViewPanorama(svEl, {
                    //         position: marker.getPosition(),
                    //         pov: { heading: 34, pitch: 10 },
                    //         zoom: 1,
                    //         enableCloseButton: false,
                    //     });
                    // });
                    google.maps.event.addListenerOnce(infoWindow, 'domready', () => {
                        const svEl = document.getElementById(`sv-${m.id}`);
                        if (!svEl) return;

                        // 1) Cari panorama terdekat dalam radius 50m
                        const svService = new google.maps.StreetViewService();
                        svService.getPanorama({
                        location: marker.getPosition(),
                        radius: 50,
                        }, (panoData, status) => {
                        if (status !== google.maps.StreetViewStatus.OK) return;

                        // 2) Hitung heading agar menghadap marker
                        const panoLatLng  = panoData.location.latLng;
                        const markerPos   = marker.getPosition();
                        const heading     = google.maps.geometry.spherical.computeHeading(
                            panoLatLng,
                            markerPos
                        );

                        // 3) Render StreetViewPanorama dengan pano ID dan heading yang dihitung
                        new google.maps.StreetViewPanorama(svEl, {
                            pano: panoData.location.pano,
                            pov:  { heading, pitch: 0 },
                            zoom:  1,
                            enableCloseButton: false,
                        });
                        });
                    });
                });
                markerList.push(marker);
            });

            // ⬇️ Aktifkan clustering
            new markerClusterer.MarkerClusterer({ map, markers: markerList });
            document
                .querySelectorAll('[id^="sv-"]')
                .forEach((el) => {
                const pid = el.id.replace('sv-', '');
                const lat = parseFloat(el.dataset.lat);
                const lng = parseFloat(el.dataset.lng);

                // store them if you need later
                svElements[pid] = new google.maps.StreetViewPanorama(el, {
                    position:         { lat, lng },
                    pov:              { heading: 0, pitch: 0 },
                    zoom:             1,
                    clickToGo:        false,
                    disableDefaultUI: true,
                    linksControl:     false,
                    panControl:       false,
                    addressControl:   false,
                    motionTracking:   false,
                });
                });
            if (!bounds.isEmpty()) {
                map.fitBounds(bounds);

                // Batasi zoom maksimal setelah fitBounds
                google.maps.event.addListenerOnce(map, 'bounds_changed', () => {
                    if (map.getZoom() > 16) {
                        map.setZoom(16);
                    }
                });
            }
        }

        tryInit();
    };
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initCustomMap"
    async defer>
</script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
</x-filament-widgets::widget>
