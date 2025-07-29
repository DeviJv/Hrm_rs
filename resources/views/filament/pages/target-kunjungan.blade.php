<x-filament::page>
    {{-- Inject Markers Data --}}
    <style>
      /* .custom-label {
        background: white;
        position: absolute;
        padding: 4px 8px;
        border-radius: 4px;
        box-shadow: 0 5px 10px rgba(0,0,0,0.3);
        white-space: nowrap;
        transform: translate(-50%, -120%);
        text-transform: capitalize;
        border: 3px solid;
    }
    .custom-label::after {
        content: "";
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid white;
    }
    .border-green { border-color: green !important; }
    .border-red { border-color: red !important; } */
    .custom-label {
    background: white;
    position: absolute;
    padding: 4px 8px;
    border-radius: 4px;
    box-shadow: 0 5px 10px rgba(0,0,0,0.3);
    white-space: nowrap;
    transform: translate(-50%, -120%);
    text-transform: capitalize;
    border: 3px solid transparent;
}

.custom-label {
    background: white;
    position: absolute;
    padding: 4px 8px;
    border-radius: 4px;
    box-shadow: 0 5px 10px rgba(0,0,0,0.3);
    white-space: nowrap;
    transform: translate(-50%, -120%);
    text-transform: capitalize;
    border: 3px solid transparent;
    overflow: visible; /* penting */
}

/* Arrow default */
.custom-label::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid white;
}

/* Border hijau penuh */
.border-green {
    border-color: green !important;
}

/* Border merah penuh */
.border-red {
    border-color: red !important;
}

/* Border hijau-kuning */
.border-green-yellow::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 4px;
    padding: 2.5px;
    background: linear-gradient(to right, green 50%, gold 50%);
    -webkit-mask: 
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
    z-index: -1;
    box-sizing: border-box;
}

/* Arrow untuk split */
.border-green-yellow::after {
    background: linear-gradient(to right, green 50%, gold 50%);
    width: 12px;
    height: 6px;
    content: "";
    display: block;
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    clip-path: polygon(0 0, 100% 0, 50% 100%);
}
  </style>
    <form wire:submit.prevent="applyFilter" class="mb-4">
        {{ $this->form }}
    </form>
    <div wire:ignore id="map" class="w-full rounded-xl shadow" style="height: 80vh; min-height: 400px;"></div>
      <script>
            window.markersData = @js($this->markers);
        </script>
 <script>
   let map;
let markersArray = [];
let isMapReady = false;
let markerQueue = [];

// Render marker
function renderMarkers(data) {
    markersArray.forEach(marker => marker.setMap(null));
    markersArray = [];

    const bounds = new google.maps.LatLngBounds();

    data.forEach(m => {
        
        const marker = new google.maps.Marker({
         
            position: { lat: m.lat, lng: m.lng },
            map,
            title: m.nama,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(35, 35),
                labelOrigin: new google.maps.Point(17, -5),
            },
            label: {
                text: m.nama,
                color: "#000",
                fontSize: "12px",
                fontWeight: "bold",
                className: "custom-label " + (
                    m.has_patient && m.has_valid_visit ? 'border-green-yellow' :
                    m.has_patient ? 'border-green' :
                    'border-red'
                ),
            },
        });

        markersArray.push(marker);
        bounds.extend(marker.getPosition());

        const infoWindow = new google.maps.InfoWindow({ content: m.info });
        marker.addListener('click', () => infoWindow.open(map, marker));
    });

    if (data.length > 0) {
            map.fitBounds(bounds);
        }
    }

    // Google Maps siap
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: -6.2, lng: 106.8 },
            zoom: 10,
        });

        isMapReady = true;

        // Jika ada queue marker, render sekarang
        if (markerQueue.length > 0) {
            renderMarkers(markerQueue.pop());
            markerQueue = [];
        } else {
            renderMarkers(window.markersData);
        }
    }

    // Listener untuk Livewire event
    document.addEventListener('refreshMap', (event) => {
        const data = event.detail.markers;
        if (isMapReady) {
            renderMarkers(data);
        } else {
            markerQueue.push(data);
        }
    });
  </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap">
    </script>
</x-filament::page>