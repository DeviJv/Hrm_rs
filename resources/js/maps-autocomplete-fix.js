window.addEventListener("DOMContentLoaded", () => {
    let lastStatus = null;

    setInterval(() => {
        const marker = window.FilamentGoogleMaps?.markers?.location;
        const statusInput = document.querySelector("#data\\.status_kerja_sama");

        if (!marker || !statusInput) {
            console.log("Menunggu marker/status...");
            return;
        }

        const status = statusInput.value;

        if (status === lastStatus) return;

        lastStatus = status;

        let iconUrl;

        switch (status) {
            case "sudah":
                iconUrl =
                    "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
                break;
            case "BELUM":
                iconUrl =
                    "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
                break;
            default:
                iconUrl =
                    "https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_black.png";
                break;
        }

        // 💡 Always re-set icon to fresh marker instance
        if (marker.setIcon) {
            marker.setIcon(iconUrl);
            console.log("Marker updated icon:", iconUrl);
        } else {
            console.warn("marker.setIcon is not available");
        }
    }, 1000);
});
