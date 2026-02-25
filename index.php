<?php
// index.php - Main Dashboard
require_once 'includes/db.php';
$page_title = 'Dashboard';
include 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Employees</h3>
        <?php
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
            echo "<p>" . $stmt->fetchColumn() . "</p>";
        } catch (Exception $e) {
            echo "<p>0</p>";
        }
        ?>
    </div>
    <div class="stat-card">
        <h3>Today's Shifts</h3>
        <?php
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM shifts WHERE DATE(start_time) = CURDATE()");
            $stmt->execute();
            echo "<p>" . $stmt->fetchColumn() . "</p>";
        } catch (Exception $e) {
            echo "<p>0</p>";
        }
        ?>
    </div>
    <div class="stat-card">
        <h3>Pending Payroll</h3>
        <?php
        try {
            $stmt = $pdo->query("
                SELECT SUM(s.total_hours * e.base_pay_rate) 
                FROM shifts s 
                JOIN employees e ON s.employee_id = e.id 
                WHERE s.status IN ('completed', 'verified')
            ");
            $pending = $stmt->fetchColumn();
            echo "<p>$" . number_format($pending ?: 0, 2) . "</p>";
        } catch (Exception $e) {
            echo "<p>$0.00</p>";
        }
        ?>
    </div>

    <div id="weather-widget" class="weather-card" style="display: none;">
        <div class="weather-info">
            <h3>Today's Weather</h3>
            <div id="weather-temp" class="weather-temp">--°C</div>
            <div id="weather-details" class="weather-details">Loading weather...</div>
            <div id="weather-location" class="weather-location">Detecting...</div>
        </div>
        <div id="weather-icon" class="weather-icon">☁️</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                fetchWeather(lat, lon);
            }, function (error) {
                console.warn("Geolocation failed:", error);
                fetchWeather(); // Fallback to IP
            }, { timeout: 10000 }); // 10s timeout
        } else {
            fetchWeather();
        }

        function fetchWeather(lat, lon) {
            const widget = document.getElementById('weather-widget');
            const details = document.getElementById('weather-details');

            let url = 'https://wttr.in/';
            if (lat && lon) {
                url += `${lat},${lon}`;
            }
            url += '?format=j1';

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error("Weather service unreachable");
                    return response.json();
                })
                .then(data => {
                    if (!data.current_condition || !data.current_condition[0]) throw new Error("Invalid weather data");

                    const current = data.current_condition[0];
                    const weatherDesc = current.weatherDesc ? current.weatherDesc[0].value : "Unknown";
                    const temp = current.temp_C;

                    let locationStr = "Unknown Location";
                    if (data.nearest_area && data.nearest_area[0]) {
                        const city = data.nearest_area[0].areaName ? data.nearest_area[0].areaName[0].value : "";
                        const country = data.nearest_area[0].country ? data.nearest_area[0].country[0].value : "";
                        locationStr = city + (country ? `, ${country}` : "");
                    }

                    document.getElementById('weather-temp').innerText = `${temp}°C`;
                    details.innerText = weatherDesc;
                    document.getElementById('weather-location').innerText = locationStr;

                    const desc = weatherDesc.toLowerCase();
                    let icon = '☀️';
                    if (desc.includes('cloud')) icon = '☁️';
                    if (desc.includes('rain') || desc.includes('drizzle')) icon = '🌧️';
                    if (desc.includes('sun') || desc.includes('clear')) icon = '☀️';
                    if (desc.includes('snow')) icon = '❄️';
                    if (desc.includes('thunder')) icon = '⚡';
                    if (desc.includes('mist') || desc.includes('fog')) icon = '🌫️';

                    document.getElementById('weather-icon').innerText = icon;
                    widget.style.display = 'flex';
                })
                .catch(err => {
                    console.error("Weather error:", err);
                    // Show widget with error message only if specifically requested or to show it failed
                    details.innerText = "Weather unavailable";
                    document.getElementById('weather-location').innerText = "Try refreshing page";
                    widget.style.display = 'flex';
                });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>