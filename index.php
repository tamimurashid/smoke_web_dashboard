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
            <script>
                let tempGauge = new RadialGauge({
                    renderTo: 'tempGauge',
                    width: 200,
                    height: 200,
                    units: "°C",
                    minValue: -10,
                    maxValue: 100,
                    majorTicks: [0, 20, 40, 60, 80, 100],
                    highlights: [
                        { from: 0, to: 30, color: "green" },
                        { from: 31, to: 60, color: "yellow" },
                        { from: 61, to: 100, color: "red" }
                    ],
                    value: 0,
                    animationDuration: 1500
                }).draw();
                
                let humidityGauge = new RadialGauge({
                    renderTo: 'humidityGauge',
                    width: 200,
                    height: 200,
                    units: "%",
                    minValue: 0,
                    maxValue: 100,
                    majorTicks: [0, 20, 40, 60, 80, 100],
                    highlights: [
                        { from: 0, to: 30, color: "red" },
                        { from: 31, to: 70, color: "green" },
                        { from: 71, to: 100, color: "blue" }
                    ],
                    value: 0,
                    animationDuration: 1500
                }).draw();
                
                let smokeGauge = new RadialGauge({
                    renderTo: 'smokeGauge',
                    width: 200,
                    height: 200,
                    units: "AQI",
                    minValue: 0,
                    maxValue: 500,
                    majorTicks: [0, 100, 200, 300, 400, 500],
                    highlights: [
                        { from: 0, to: 100, color: "green" },
                        { from: 101, to: 300, color: "yellow" },
                        { from: 301, to: 500, color: "red" }
                    ],
                    value: 0,
                    animationDuration: 1500
                }).draw();
                
                let flameGauge = new RadialGauge({
                    renderTo: 'flameGauge',
                    width: 200,
                    height: 200,
                    units: "Flame Intensity",
                    minValue: 0,
                    maxValue: 10,
                    majorTicks: [0, 2, 4, 6, 8, 10],
                    highlights: [
                        { from: 0, to: 3, color: "green" },
                        { from: 4, to: 7, color: "yellow" },
                        { from: 8, to: 10, color: "red" }
                    ],
                    value: 0,
                    animationDuration: 1500
                }).draw();
                
                async function fetchData() {
                    try {
                        const response = await fetch('server/controller.php');
                        if (!response.ok) throw new Error('Network response was not ok');
                        const data = await response.json();
                        tempGauge.value = data.temperature;
                        humidityGauge.value = data.humidity;
                        smokeGauge.value = data.smoke;
                        flameGauge.value = data.flame;
                    } catch (error) {
                        console.error('Error fetching data:', error);
                    }
                }
                
                document.addEventListener('DOMContentLoaded', () => {
                    fetchData();
                    setInterval(fetchData, 5000);
                });
            </script>
        </div>
    </div>
</body>
</html>
