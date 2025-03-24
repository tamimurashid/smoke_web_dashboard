<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Alarm Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center">Fire Alarm Monitoring System</h2>
        <div class="row">
            <div class="col-md-6">
                <canvas id="tempChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="smokeChart"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="humidityChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="flameChart"></canvas>
            </div>
        </div>
        <div class="alert alert-danger mt-4 d-none" id="alertBox"></div>
    </div>

    <script>
        async function fetchData() {
            try {
                const response = await fetch('server/controller.php');
                const data = await response.json();
                updateCharts(data);
                checkAlerts(data);
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

        function checkAlerts(data) {
            const alertBox = document.getElementById('alertBox');
            if (data.status.includes('Fire') || data.status.includes('High')) {
                alertBox.textContent = data.status;
                alertBox.classList.remove('d-none');
            } else {
                alertBox.classList.add('d-none');
            }
        }

        const tempChart = new Chart(document.getElementById('tempChart'), {
            type: 'doughnut',
            data: { labels: ['Temperature'], datasets: [{ data: [0], backgroundColor: ['red'] }] }
        });
        const smokeChart = new Chart(document.getElementById('smokeChart'), {
            type: 'doughnut',
            data: { labels: ['Smoke'], datasets: [{ data: [0], backgroundColor: ['gray'] }] }
        });
        const humidityChart = new Chart(document.getElementById('humidityChart'), {
            type: 'doughnut',
            data: { labels: ['Humidity'], datasets: [{ data: [0], backgroundColor: ['blue'] }] }
        });
        const flameChart = new Chart(document.getElementById('flameChart'), {
            type: 'doughnut',
            data: { labels: ['Flame'], datasets: [{ data: [0], backgroundColor: ['orange'] }] }
        });

        fetchData();
        setInterval(fetchData, 5000);
    </script>
</body>
</html>
