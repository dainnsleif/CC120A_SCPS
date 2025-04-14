<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - School Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .report-section {
            margin-bottom: 30px;
        }

        .report-section h3 {
            margin-bottom: 15px;
            color: var(--dark-color);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-card h4 {
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .chart-container {
            margin-top: 20px;
            height: 300px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Reports</h2>
                <div>
                    <button class="btn btn-primary" onclick="generateReport()">
                        <i class="fas fa-sync"></i> Generate Report
                    </button>
                    <button class="btn btn-success" onclick="exportReport()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="report_type">Report Type</label>
                        <select id="report_type" class="form-control">
                            <option value="medical">Medical Records</option>
                            <option value="appointments">Appointments</option>
                            <option value="inventory">Inventory</option>
                        </select>
                    </div>
                </div>

                <div class="report-section">
                    <h3>Summary Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h4>Total Records</h4>
                            <div class="value" id="totalRecords">0</div>
                        </div>
                        <div class="stat-card">
                            <h4>Total Students</h4>
                            <div class="value" id="totalStudents">0</div>
                        </div>
                        <div class="stat-card">
                            <h4>Total Appointments</h4>
                            <div class="value" id="totalAppointments">0</div>
                        </div>
                        <div class="stat-card">
                            <h4>Low Stock Items</h4>
                            <div class="value" id="lowStockItems">0</div>
                        </div>
                    </div>
                </div>

                <div class="report-section">
                    <h3>Trends</h3>
                    <div class="chart-container">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>

                <div class="report-section">
                    <h3>Detailed Data</h3>
                    <div class="table-responsive">
                        <table id="reportTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Type</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <!-- Report data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let trendsChart = null;

        document.addEventListener('DOMContentLoaded', () => {
            // Set default date range to last 30 days
            const today = new Date();
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(today.getDate() - 30);

            document.getElementById('start_date').value = thirtyDaysAgo.toISOString().split('T')[0];
            document.getElementById('end_date').value = today.toISOString().split('T')[0];

            generateReport();
        });

        function generateReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const reportType = document.getElementById('report_type').value;

            fetch(`reports.php?start_date=${startDate}&end_date=${endDate}&type=${reportType}`)
                .then(response => response.json())
                .then(data => {
                    updateStatistics(data.statistics);
                    updateTrendsChart(data.trends);
                    updateDetailedData(data.details);
                })
                .catch(error => console.error('Error:', error));
        }

        function updateStatistics(stats) {
            document.getElementById('totalRecords').textContent = stats.total_records;
            document.getElementById('totalStudents').textContent = stats.total_students;
            document.getElementById('totalAppointments').textContent = stats.total_appointments;
            document.getElementById('lowStockItems').textContent = stats.low_stock_items;
        }

        function updateTrendsChart(trends) {
            const ctx = document.getElementById('trendsChart').getContext('2d');
            
            if (trendsChart) {
                trendsChart.destroy();
            }

            trendsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trends.dates,
                    datasets: [{
                        label: 'Medical Records',
                        data: trends.medical_records,
                        borderColor: '#3498db',
                        tension: 0.1
                    }, {
                        label: 'Appointments',
                        data: trends.appointments,
                        borderColor: '#2ecc71',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function updateDetailedData(details) {
            const tbody = document.getElementById('reportTableBody');
            tbody.innerHTML = '';
            
            details.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.date}</td>
                    <td>${item.student_name}</td>
                    <td>${item.type}</td>
                    <td>${item.details}</td>
                `;
                tbody.appendChild(row);
            });
        }

        function exportReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const reportType = document.getElementById('report_type').value;

            window.location.href = `reports.php?export=1&start_date=${startDate}&end_date=${endDate}&type=${reportType}`;
        }
    </script>
</body>
</html> 