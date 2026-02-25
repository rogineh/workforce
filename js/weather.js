document.addEventListener('DOMContentLoaded', function () {
    const widget = document.getElementById('weather-widget');
    if (!widget) return;

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            fetchWeather(lat, lon);
        }, function (error) {
            console.warn("Geolocation failed:", error);
            fetchWeather(); // Fallback to Brisbane
        }, { timeout: 10000 });
    } else {
        fetchWeather();
    }

    function fetchWeather(lat, lon) {
        const details = document.getElementById('weather-details');
        const tempEl = document.getElementById('weather-temp');
        const iconEl = document.getElementById('weather-icon');
        const locEl = document.getElementById('weather-location');

        // Fallback coordinates (Brisbane, QLD)
        const defaultLat = -27.47;
        const defaultLon = 153.02;

        const useLat = lat ? parseFloat(lat).toFixed(2) : defaultLat;
        const useLon = lon ? parseFloat(lon).toFixed(2) : defaultLon;

        const url = `https://api.open-meteo.com/v1/forecast?latitude=${useLat}&longitude=${useLon}&current_weather=true&timezone=auto`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error("Weather service unreachable");
                return response.json();
            })
            .then(data => {
                if (!data.current_weather) throw new Error("Invalid weather data");

                const cw = data.current_weather;
                const temp = Math.round(cw.temperature);
                const code = cw.weathercode;

                if (tempEl) tempEl.innerText = `${temp}°C`;
                if (locEl) locEl.innerText = lat ? "Local Weather" : "Brisbane, AU";

                // WMO Weather interpretation codes
                let desc = "Clear";
                let icon = "☀️";

                if (code >= 1 && code <= 3) { desc = "Partly Cloudy"; icon = "⛅"; }
                if (code >= 45 && code <= 48) { desc = "Foggy"; icon = "🌫️"; }
                if (code >= 51 && code <= 67) { desc = "Rainy"; icon = "🌧️"; }
                if (code >= 71 && code <= 77) { desc = "Snowy"; icon = "❄️"; }
                if (code >= 80 && code <= 82) { desc = "Showers"; icon = "🌦️"; }
                if (code >= 95) { desc = "Thunderstorm"; icon = "⚡"; }

                if (details) details.innerText = desc;
                if (iconEl) iconEl.innerText = icon;
                widget.style.display = 'flex';
            })
            .catch(err => {
                console.error("Weather error:", err);
                if (details) details.innerText = "Weather unavailable";
                widget.style.display = 'flex';
            });
    }
});
