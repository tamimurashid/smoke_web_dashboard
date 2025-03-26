<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Alarm Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #121212;
            color: white;
        }
        .gauge-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .gauge {
            max-width: 200px;
            margin: auto;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: #343a40;
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            color: white;
            padding: 10px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <span class="navbar-brand mb-0 h1 ms-3">Fire Alarm Monitoring</span>
    </nav>
    
    <div class="sidebar">
        <h4 class="text-center">Dashboard</h4>
        <a href="#"><i class="material-icons">dashboard</i> Home</a>
        <a href="#"><i class="material-icons">settings</i> Settings</a>
        <a href="#"><i class="material-icons">logout</i> Logout</a>
    </div>
    
    <div class="main-content">
        <div class="row text-center">
            <div class="col-md-6 gauge-container">
                <canvas id="tempChart" class="gauge"></canvas>
                <div id="tempValue">Temperature: 0°C</div>
            </div>
            <div class="col-md-6 gauge-container">
                <canvas id="smokeChart" class="gauge"></canvas>
                <div id="smokeValue">Smoke Level: 0</div>
            </div>
        </div>
        <div class="row text-center mt-4">
            <div class="col-md-6 gauge-container">
                <canvas id="humidityChart" class="gauge"></canvas>
                <div id="humidityValue">Humidity: 0%</div>
            </div>
            <div class="col-md-6 gauge-container">
                <canvas id="flameChart" class="gauge"></canvas>
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
    
    <script>
        let tempChart, smokeChart, humidityChart, flameChart;

        function initializeCharts() {
            const ctxTemp = document.getElementById('tempChart').getContext('2d');
            const ctxSmoke = document.getElementById('smokeChart').getContext('2d');
            const ctxHumidity = document.getElementById('humidityChart').getContext('2d');
            const ctxFlame = document.getElementById('flameChart').getContext('2d');

            tempChart = new Chart(ctxTemp, {
                type: 'doughnut',
                data: { labels: ['Temperature'], datasets: [{ data: [0], backgroundColor: ['red'] }] }
            });

            smokeChart = new Chart(ctxSmoke, {
                type: 'doughnut',
                data: { labels: ['Smoke'], datasets: [{ data: [0], backgroundColor: ['gray'] }] }
            });

            humidityChart = new Chart(ctxHumidity, {
                type: 'doughnut',
                data: { labels: ['Humidity'], datasets: [{ data: [0], backgroundColor: ['blue'] }] }
            });

            flameChart = new Chart(ctxFlame, {
                type: 'doughnut',
                data: { labels: ['Flame'], datasets: [{ data: [0], backgroundColor: ['orange'] }] }
            });
        }

        async function fetchData() {
            try {
                const response = await fetch('server/controller.php');
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                console.log('Fetched Data:', data);
                updateCharts(data);
                updateTable(data);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function updateCharts(data) {
            tempChart.data.datasets[0].data = [data.temperature];
            smokeChart.data.datasets[0].data = [data.smoke];
            humidityChart.data.datasets[0].data = [data.humidity];
            flameChart.data.datasets[0].data = [data.flame];
            tempChart.update();
            smokeChart.update();
            humidityChart.update();
            flameChart.update();
        }

        function updateTable(data) {
            document.getElementById('tempTableValue').textContent = `${data.temperature}°C`;
            document.getElementById('smokeTableValue').textContent = `${data.smoke}`;
            document.getElementById('humidityTableValue').textContent = `${data.humidity}%`;
            document.getElementById('flameTableValue').textContent = `${data.flame}`;
            dcument.getElementById('tempValue').textContent = `${data.temperature}°C`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
            fetchData();
            setInterval(fetchData, 5000);
        });
    </script>
</body>
</html>
