<x-filament::page>
    {{-- Inject Markers Data --}}
    <style>
      .custom-label {
          background: white;
          position: absolute;
          padding: 4px 8px;
          border-radius: 4px;
          box-shadow: 0 5px 10px rgba(0,0,0,0.3);
          white-space: nowrap;
          transform: translate(-50%, -120%);
          border: 3px solid green;
          text-transform: capitalize;
      }
      .custom-label::after {
            content: "";
            position: absolute;
            bottom: -6px; /* posisi triangle */
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid white;
      }
  </style>
    <div id="map" class="w-full rounded-xl shadow" style="height: 80vh; min-height: 400px;"></div>
      <script>
            window.markersData = @js($this->markers);
        </script>
 <script>
      function initMap() {
          const map = new google.maps.Map(document.getElementById('map'), {
              center: { lat: -6.2, lng: 106.8 },
              zoom: 10,
          });
  
          if (!window.markersData || !Array.isArray(window.markersData)) {
              console.error('Markers data is missing or invalid');
              return;
          }
  
          const bounds = new google.maps.LatLngBounds();
  
          window.markersData.forEach(m => {
              const iconUrl = {
                  sudah: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                  belum: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
              }[m.status] || 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png';
  
              const marker = new google.maps.Marker({
                  position: { lat: m.lat, lng: m.lng },
                  map,
                  title: m.nama,
                  icon: {
                      url: iconUrl,
                      scaledSize: new google.maps.Size(35, 35), // opsional ukuran icon
                      labelOrigin: new google.maps.Point(17, -5),
                  },
                  label: {
                        text: m.nama,
                        color: "#000",
                        fontSize: "12px",
                        fontWeight: "bold",
                        className: "custom-label"
                  },
              });
              
              bounds.extend(marker.getPosition());
  
              const infoWindow = new google.maps.InfoWindow({
                  content: m.info, // HTML info dari backend
              });
  
              marker.addListener('click', () => {
                  infoWindow.open(map, marker);
              });
          });
  
          map.fitBounds(bounds);
      }
  </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap">
    </script>
</x-filament::page>