<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Smart Fire System Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="#" class="active"><span class="material-icons">dashboard</span> Dashboard</a>
            <a href="#"><span class="material-icons">settings</span> Settings</a>
            <a href="#"><span class="material-icons">notifications</span> Alerts</a>
        </nav>
        <div class="container-fluid" style="margin-left: 260px;">
            <header class="bg-success text-center py-3">
                <h1 class="fw-bold h3 text-white my-1">Smart Fire System Dashboard</h1>
            </header>
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
            <div class="table-responsive mt-4">
                <table class="table table-dark table-bordered">
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
                            <td id="flameTableValue">0</td>
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
            tempChart.data.datasets[0].data = [data.temperature];
            smokeChart.data.datasets[0].data = [data.smoke];
            humidityChart.data.datasets[0].data = [data.humidity];
            flameChart.data.datasets[0].data = [data.flame];
            tempChart.update();
            smokeChart.update();
            humidityChart.update();
            flameChart.update();

            document.getElementById('tempTableValue').textContent = `${data.temperature}°C`;
            document.getElementById('smokeTableValue').textContent = `${data.smoke}`;
            document.getElementById('humidityTableValue').textContent = `${data.humidity}%`;
            document.getElementById('flameTableValue').textContent = `${data.flame}`;

            let status = "Normal";
            let bgColor = "bg-success";
            if (data.temperature > 50 || data.smoke > 300 || data.flame > 1) {
                status = "Critical";
                bgColor = "bg-danger";
            } else if (data.temperature > 30 || data.smoke > 100) {
                status = "Warning";
                bgColor = "bg-warning";
            }

            const statusCard = document.getElementById('statusCard');
            statusCard.className = `card-status ${bgColor}`;
            document.getElementById('statusMessage').textContent = `System Status: ${status}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
            fetchData();
            setInterval(fetchData, 5000);
        });
    </script>
</body>
</html>
