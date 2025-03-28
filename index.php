<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Smart Fire System Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #f8f9fa;
        }
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
        .card-custom {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        .status-card {
            color: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
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
            <nav class="navbar navbar-success bg-success text-white p-2 rounded">
                <div class="container-fluid">
                    <a class="navbar-brand text-white">Smart Fire System Detection</a>
                </div>
            </nav>
            <div id="statusCard" class="status-card bg-secondary mt-3">
                <h5>System Status</h5>
                <p id="statusMessage">Fetching data...</p>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card card-custom">
                        <canvas id="tempChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-custom">
                        <canvas id="smokeChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mt-3">
                    <div class="card card-custom">
                        <canvas id="humidityChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mt-3">
                    <div class="card card-custom">
                        <canvas id="flameChart"></canvas>
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
                    <tbody id="dataTable">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        const ctxTemp = document.getElementById('tempChart').getContext('2d');
        const ctxSmoke = document.getElementById('smokeChart').getContext('2d');
        const ctxHumidity = document.getElementById('humidityChart').getContext('2d');
        const ctxFlame = document.getElementById('flameChart').getContext('2d');

        const tempChart = new Chart(ctxTemp, { type: 'line', data: { labels: [], datasets: [{ label: 'Temperature (°C)', data: [], borderColor: 'red' }] } });
        const smokeChart = new Chart(ctxSmoke, { type: 'line', data: { labels: [], datasets: [{ label: 'Smoke Level', data: [], borderColor: 'gray' }] } });
        const humidityChart = new Chart(ctxHumidity, { type: 'line', data: { labels: [], datasets: [{ label: 'Humidity (%)', data: [], borderColor: 'blue' }] } });
        const flameChart = new Chart(ctxFlame, { type: 'line', data: { labels: [], datasets: [{ label: 'Flame Level', data: [], borderColor: 'orange' }] } });

        async function fetchData() {
            try {
                const response = await fetch('server/controller.php');
                const data = await response.json();
                updateDashboard(data);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function updateDashboard(data) {
            const now = new Date().toLocaleTimeString();
            tempChart.data.labels.push(now);
            smokeChart.data.labels.push(now);
            humidityChart.data.labels.push(now);
            flameChart.data.labels.push(now);
            
            tempChart.data.datasets[0].data.push(data.temperature);
            smokeChart.data.datasets[0].data.push(data.smoke);
            humidityChart.data.datasets[0].data.push(data.humidity);
            flameChart.data.datasets[0].data.push(data.flame);
            
            tempChart.update();
            smokeChart.update();
            humidityChart.update();
            flameChart.update();

            document.getElementById('statusMessage').textContent = `System Status: ${data.temperature > 40 ? 'Warning' : 'Normal'}`;
            
            const tableData = `
                <tr><td>Temperature</td><td>${data.temperature}°C</td><td>${data.temperature > 40 ? 'High' : 'Normal'}</td><td>${data.temperature > 40 ? 'Fire Risk!' : 'Safe'}</td></tr>
                <tr><td>Smoke Level</td><td>${data.smoke}</td><td>${data.smoke > 550 ? 'High' : 'Normal'}</td><td>${data.smoke > 550 ? 'Evacuate' : 'Safe'}</td></tr>
                <tr><td>Humidity</td><td>${data.humidity}%</td><td>${data.humidity < 30 ? 'Low' : 'Normal'}</td><td>${data.humidity < 30 ? 'Dry Conditions' : 'Safe'}</td></tr>
                <tr><td>Flame</td><td>${data.flame}</td><td>${data.flame === 0 ? 'Fire' : 'Normal'}</td><td>${data.flame === 0 ? 'Emergency' : 'Safe'}</td></tr>`;
            document.getElementById('dataTable').innerHTML = tableData;
        }

        setInterval(fetchData, 5000);
    </script>
</body>
</html>
