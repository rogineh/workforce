<?php
// index.php - Main Dashboard
require_once 'includes/db.php';
$page_title = 'Dashboard';
include 'includes/header.php';
?>

<div class="stats-grid">
    <div id="weather-widget" class="weather-card" style="display: none;">
        <div class="weather-info">
            <h3>Today's Weather</h3>
            <div id="weather-temp" class="weather-temp">--°C</div>
            <div id="weather-details" class="weather-details">Loading local weather...</div>
            <div id="weather-location" class="weather-location">Detecting location...</div>
        </div>
        <div id="weather-icon" class="weather-icon">☁️</div>
    </div>

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
        <p>0</p>
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
                console.error("Error getting location:", error);
                // Fallback to auto-IP weather if geolocation is denied
                fetchWeather();
            });
        } else {
            fetchWeather();
        }

        function fetchWeather(lat, lon) {
            let url = 'https://wttr.in/';
            if (lat && lon) {
                url += `${lat},${lon}`;
            }
            url += '?format=j1';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const current = data.current_condition[0];
                    const weatherDesc = current.weatherDesc[0].value;
                    const temp = current.temp_C;
                    const city = data.nearest_area[0].areaName[0].value;
                    const country = data.nearest_area[0].country[0].value;

                    document.getElementById('weather-temp').innerText = `${temp}°C`;
                    document.getElementById('weather-details').innerText = weatherDesc;
                    document.getElementById('weather-location').innerText = `${city}, ${country}`;

                    // Simple weather icon mapping
                    const desc = weatherDesc.toLowerCase();
                    let icon = '☀️';
                    if (desc.includes('cloud')) icon = '☁️';
                    if (desc.includes('rain')) icon = '🌧️';
                    if (desc.includes('sun') || desc.includes('clear')) icon = '☀️';
                    if (desc.includes('snow')) icon = '❄️';
                    if (desc.includes('thunder')) icon = '⚡';

                    document.getElementById('weather-icon').innerText = icon;
                    document.getElementById('weather-widget').style.display = 'flex';
                })
                .catch(err => {
                    console.error("Error fetching weather:", err);
                });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>