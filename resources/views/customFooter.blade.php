<script>
document.addEventListener('alpine:init', () => {
        
        const alpine = document.querySelector('#data\\.location-alpine')?.__x;
        const map = alpine?.getUntracked().map;
        const markers = map?.__markers; // ini tergantung dari implementasi plugin

        if (!map || !markers) return;
        
        // Pastikan hanya tambah listener sekali
        if (map._customClickHandlersAttached) return;
        map._customClickHandlersAttached = true;

        markers.forEach((marker) => {
            google.maps.event.clearListeners(marker, 'click');
            marker.addListener('click', () => {
                const bidanId = marker.__data?.id; // asumsi custom data disisipkan ke marker
                window.dispatchEvent(new CustomEvent('open-bidan-detail-modal', {
                    detail: { id: bidanId }
                }));
            });
        });
  
});
</script>


{{-- <script>
    
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
                    maxWidth: 1000,
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
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script> --}}