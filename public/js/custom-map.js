window.initCustomMap = function () {
    const el = document.getElementById("custom-map");
    if (!el) return console.warn("🛑 #custom-map not found");

    const markers = JSON.parse(el.dataset.markers || "[]");

    const map = new google.maps.Map(el, {
        center: { lat: -6.5525309, lng: 106.7753126 },
        zoom: 13,
    });

    markers.forEach((m) => {
        const marker = new google.maps.Marker({
            position: { lat: m.lat, lng: m.lng },
            map,
            title: m.nama,
        });

        const info = new google.maps.InfoWindow({
            content: `<strong>${m.nama}</strong><br><a href="${m.url}" target="_blank">Lihat Detail</a>`,
        });

        marker.addListener("click", () => {
            info.open(map, marker);
        });
    });

    console.log(`✅ ${markers.length} markers loaded`);
};
