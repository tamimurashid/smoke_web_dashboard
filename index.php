<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Smart Fire System Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//cdn.rawgit.com/Mikhus/canvas-gauges/gh-pages/download/2.1.7/all/gauge.min.js"></script> 
    <script src="script.js" defer></script>
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar a.active {
            background: #198754;
        }
        .card-status {
            padding: 15px;
            color: white;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <nav class="sidebar">
            <div class="links mt-3">
                <a href="#" class="active"><span class="material-icons">dashboard</span> Dashboard</a>
                <a href="#"><span class="material-icons">settings</span> Settings</a>
                <a href="#"><span class="material-icons">notifications</span> Alerts</a>
            </div>
        </nav>
        <div class="container-fluid mt-2" style="margin-left: 260px;">
            <nav class="navbar navbar-success bg-success">
                <div class="container-fluid">
                    <a class="navbar-brand text-white">Smart Fire System Detection</a>
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </nav>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div id="statusCard" class="card-status bg-secondary">
                        <h5>System Status</h5>
                        <p id="statusMessage">Fetching data...</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-3">
                    <canvas id="tempChart"></canvas>
                    <div id="tempValue">Temperature: 0°C</div>
                </div>
                <div class="col-md-3">
                    <canvas id="smokeChart"></canvas>
                    <div id="smokeValue">Smoke Level: 0</div>
                </div>
                <div class="col-md-3">
                    <canvas id="humidityChart"></canvas>
                    <div id="humidityValue">Humidity: 0%</div>
                </div>
                <div class="col-md-3">
                    <canvas id="flameChart"></canvas>
                    <div id="flameValue">Flame Level: 0</div>
                </div>
            </div>
            <div class="row mt-4">
                 <div class="row mt-4">
                    <div class="col-md-3">
                        <canvas id="tempGauge"></canvas>
                    </div>
                    <div class="col-md-3">
                        <canvas id="humidityGauge"></canvas>
                    </div>
                    <div class="col-md-3">
                        <canvas id="smokeGauge"></canvas>
                    </div>
                    <div class="col-md-3">
                        <canvas id="flameGauge"></canvas>
                    </div>
                </div>
            </div>
            <div class="table-responsive mt-4">
                <table class="table table-light table-bordered">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Guidance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Temperature</td>
                            <td id="tempTableValue">0°C</td>
                            <td id="tempStatus">Normal</td>
                            <td id="tempGuide">Safe</td>
                        </tr>
                        <tr>
                            <td>Smoke Level</td>
                            <td id="smokeTableValue">0</td>
                            <td id="smokeStatus">Normal</td>
                            <td id="smokeGuide">Safe</td>
                        </tr>
                        <tr>
                            <td>Humidity</td>
                            <td id="humidityTableValue">0%</td>
                            <td id="humidityStatus">Normal</td>
                            <td id="humidityGuide">Safe</td>
                        </tr>
                        <tr>
                            <td>Flame</td>
                            <td id="flameTableValue">1</td>
                            <td id="flameStatus">Normal</td>
                            <td id="flameGuide">Safe</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        let tempChart, smokeChart, humidityChart, flameChart;

        function initializeCharts() {
            tempChart = new Chart(document.getElementById('tempChart').getContext('2d'), { type: 'doughnut', data: { datasets: [{ data: [0], backgroundColor: ['red'] }] } });
            smokeChart = new Chart(document.getElementById('smokeChart').getContext('2d'), { type: 'doughnut', data: { datasets: [{ data: [0], backgroundColor: ['gray'] }] } });
            humidityChart = new Chart(document.getElementById('humidityChart').getContext('2d'), { type: 'doughnut', data: { datasets: [{ data: [0], backgroundColor: ['blue'] }] } });
            flameChart = new Chart(document.getElementById('flameChart').getContext('2d'), { type: 'doughnut', data: { datasets: [{ data: [0], backgroundColor: ['orange'] }] } });
        }

        async function fetchData() {
            try {
                const response = await fetch('server/controller.php');
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                updateDashboard(data);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function updateDashboard(data) {
            // Update gauge charts
            tempChart.data.datasets[0].data = [data.temperature];
            smokeChart.data.datasets[0].data = [data.smoke];
            humidityChart.data.datasets[0].data = [data.humidity];
            flameChart.data.datasets[0].data = [data.flame];
            tempChart.update();
            smokeChart.update();
            humidityChart.update();
            flameChart.update();

            // Update labels under gauges
            document.getElementById('tempValue').textContent = `Temperature: ${data.temperature}°C`;
            document.getElementById('smokeValue').textContent = `Smoke Level: ${data.smoke}`;
            document.getElementById('humidityValue').textContent = `Humidity: ${data.humidity}%`;
            document.getElementById('flameValue').textContent = `Flame Level: ${data.flame}`;

            // Update table values
            document.getElementById('tempTableValue').textContent = `${data.temperature}°C`;
            document.getElementById('smokeTableValue').textContent = `${data.smoke}`;
            document.getElementById('humidityTableValue').textContent = `${data.humidity}%`;
            document.getElementById('flameTableValue').textContent = `${data.flame}`;

            // Determine status and guidance
            updateStatusAndGuidance("temp", data.temperature, 40, "High Temperature Detected!", "Critical Fire Risk", "Temperature is safe");
            updateStatusAndGuidance("smoke", data.smoke, 550, "High Smoke Levels!", "Evacuate Immediately", "Smoke levels normal");
            updateStatusAndGuidance("humidity", data.humidity, 30, "Low Humidity!", "Possible Dry Conditions", "Humidity level is normal");
            updateStatusAndGuidance("flame", data.flame, 0, "No flame detected", "Fire Emergency!", "Fire detected");

            // Update overall system status
            let status = "Normal";
            let bgColor = "bg-success";
            
            if (data.temperature > 40 && data.smoke > 550 && data.flame === 0) {
                status = "Critical Fire Condition!";
                bgColor = "bg-danger";
            } else if (data.temperature > 30 || data.smoke > 550 || data.flame === 0) {
                status = "Warning: Potential Fire Risk";
                bgColor = "bg-warning";
            } else if (data.temperature < 20) {
                status = "Low Temperature";
                bgColor = "bg-info";
            }

            const statusCard = document.getElementById('statusCard');
            statusCard.className = `card-status ${bgColor}`;
            document.getElementById('statusMessage').textContent = `System Status: ${status}`;
        }

        // Function to update status and guidance dynamically
        function updateStatusAndGuidance(param, value, threshold, warningMsg, dangerMsg, safeMsg) {
            const statusElement = document.getElementById(`${param}Status`);
            const guideElement = document.getElementById(`${param}Guide`);

            if (value >= threshold) {
                statusElement.textContent = "Warning";
                guideElement.textContent = warningMsg;
            } else {
                statusElement.textContent = "Normal";
                guideElement.textContent = safeMsg;
            }

            if (param === "flame" && value === 0) {
                statusElement.textContent = "Danger";
                guideElement.textContent = dangerMsg;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
            fetchData();
            setInterval(fetchData, 5000);
        });
    </script>
</body>
</html>
