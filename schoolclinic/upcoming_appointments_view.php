<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Appointments - School Clinic</title>
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

        .btn-danger {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: white;
        }

        .btn-warning:hover {
            background-color: #d35400;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-scheduled {
            background-color: #f1c40f;
            color: #fff;
        }

        .status-completed {
            background-color: #2ecc71;
            color: #fff;
        }

        .status-cancelled {
            background-color: #e74c3c;
            color: #fff;
        }

        .filter-section {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .filter-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
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

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }

        .empty-state h3 {
            margin-bottom: 10px;
        }

        .empty-state p {
            margin-bottom: 20px;
        }

        .today-highlight {
            background-color: #e8f4f8;
        }

        .tomorrow-highlight {
            background-color: #f0f9f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Upcoming Appointments</h2>
                <div>
                    <button class="btn btn-primary" onclick="refreshAppointments()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="filter-section">
                    <div class="filter-group">
                        <label for="dateFilter">Date Filter</label>
                        <select id="dateFilter" class="filter-control" onchange="filterAppointments()">
                            <option value="all">All Upcoming</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="week">This Week</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="searchInput">Search Student</label>
                        <input type="text" id="searchInput" class="filter-control" placeholder="Search by name..." onkeyup="filterAppointments()">
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="appointmentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="appointmentsTableBody">
                            <!-- Appointments will be loaded here -->
                        </tbody>
                    </table>
                    <div id="emptyState" class="empty-state" style="display: none;">
                        <i class="fas fa-calendar-check"></i>
                        <h3>No Upcoming Appointments</h3>
                        <p>There are no upcoming appointments scheduled.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Appointment Modal -->
    <div id="completeModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeCompleteModal()">&times;</span>
            <h2>Complete Appointment</h2>
            <form id="completeForm">
                <input type="hidden" id="appointment_id">
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" class="form-control" rows="4" placeholder="Add any notes about the appointment..."></textarea>
                </div>
                <div class="form-group">
                    <label for="follow_up">Follow-up Required</label>
                    <select id="follow_up" class="form-control">
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>
                <div class="form-group" id="followUpDateGroup" style="display: none;">
                    <label for="follow_up_date">Follow-up Date</label>
                    <input type="date" id="follow_up_date" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Mark as Completed</button>
                    <button type="button" class="btn btn-danger" onclick="closeCompleteModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadUpcomingAppointments();
            
            // Show/hide follow-up date based on selection
            document.getElementById('follow_up').addEventListener('change', function() {
                const followUpDateGroup = document.getElementById('followUpDateGroup');
                followUpDateGroup.style.display = this.value === 'yes' ? 'block' : 'none';
            });
            
            // Handle form submission
            document.getElementById('completeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                completeAppointment();
            });
        });

        function loadUpcomingAppointments() {
            fetch('appointments.php?status=scheduled')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('appointmentsTableBody');
                    tbody.innerHTML = '';
                    
                    if (data.data && data.data.length > 0) {
                        document.getElementById('emptyState').style.display = 'none';
                        
                        // Sort appointments by date and time
                        data.data.sort((a, b) => {
                            const dateA = new Date(a.appointment_date + ' ' + a.appointment_time);
                            const dateB = new Date(b.appointment_date + ' ' + b.appointment_time);
                            return dateA - dateB;
                        });
                        
                        data.data.forEach(appointment => {
                            const row = document.createElement('tr');
                            const appointmentDate = new Date(appointment.appointment_date);
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            
                            const tomorrow = new Date(today);
                            tomorrow.setDate(tomorrow.getDate() + 1);
                            
                            // Add highlight class for today and tomorrow
                            if (appointmentDate.getTime() === today.getTime()) {
                                row.classList.add('today-highlight');
                            } else if (appointmentDate.getTime() === tomorrow.getTime()) {
                                row.classList.add('tomorrow-highlight');
                            }
                            
                            row.innerHTML = `
                                <td>${appointment.appointment_id}</td>
                                <td>${appointment.student_name}</td>
                                <td>${formatDate(appointment.appointment_date)}</td>
                                <td>${formatTime(appointment.appointment_time)}</td>
                                <td>${appointment.reason}</td>
                                <td><span class="status-badge status-scheduled">Scheduled</span></td>
                                <td>
                                    <button class="btn btn-success" onclick="showCompleteModal(${appointment.appointment_id})">
                                        <i class="fas fa-check"></i> Complete
                                    </button>
                                    <button class="btn btn-danger" onclick="cancelAppointment(${appointment.appointment_id})">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else {
                        document.getElementById('emptyState').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function filterAppointments() {
            const dateFilter = document.getElementById('dateFilter').value;
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.getElementById('appointmentsTableBody').getElementsByTagName('tr');
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            const nextWeek = new Date(today);
            nextWeek.setDate(nextWeek.getDate() + 7);
            
            let visibleCount = 0;
            
            for (let row of rows) {
                const studentName = row.cells[1].textContent.toLowerCase();
                const dateCell = row.cells[2].textContent;
                const appointmentDate = parseDate(dateCell);
                
                let dateMatch = true;
                
                if (dateFilter === 'today') {
                    dateMatch = appointmentDate.getTime() === today.getTime();
                } else if (dateFilter === 'tomorrow') {
                    dateMatch = appointmentDate.getTime() === tomorrow.getTime();
                } else if (dateFilter === 'week') {
                    dateMatch = appointmentDate >= today && appointmentDate <= nextWeek;
                }
                
                const nameMatch = studentName.includes(searchText);
                
                if (dateMatch && nameMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
            
            // Show/hide empty state based on visible rows
            document.getElementById('emptyState').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        function showCompleteModal(appointmentId) {
            document.getElementById('appointment_id').value = appointmentId;
            document.getElementById('notes').value = '';
            document.getElementById('follow_up').value = 'no';
            document.getElementById('followUpDateGroup').style.display = 'none';
            document.getElementById('completeModal').style.display = 'block';
        }

        function closeCompleteModal() {
            document.getElementById('completeModal').style.display = 'none';
        }

        function completeAppointment() {
            const appointmentId = document.getElementById('appointment_id').value;
            const notes = document.getElementById('notes').value;
            const followUp = document.getElementById('follow_up').value;
            const followUpDate = followUp === 'yes' ? document.getElementById('follow_up_date').value : null;
            
            const appointmentData = {
                appointment_id: appointmentId,
                status: 'Completed',
                notes: notes,
                follow_up: followUp,
                follow_up_date: followUpDate
            };
            
            fetch('appointments.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(appointmentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeCompleteModal();
                    refreshAppointments();
                } else {
                    alert('Error completing appointment: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function cancelAppointment(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment?')) {
                const appointmentData = {
                    appointment_id: appointmentId,
                    status: 'Cancelled'
                };
                
                fetch('appointments.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(appointmentData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        refreshAppointments();
                    } else {
                        alert('Error cancelling appointment: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function refreshAppointments() {
            loadUpcomingAppointments();
        }

        function formatDate(dateString) {
            const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        function formatTime(timeString) {
            return timeString.substring(0, 5); // Format as HH:MM
        }

        function parseDate(dateString) {
            const parts = dateString.split(', ');
            const monthDay = parts[1].split(' ');
            const month = new Date(Date.parse(monthDay[0] + " 1, 2012")).getMonth() + 1;
            const day = parseInt(monthDay[1]);
            const year = parseInt(parts[2]);
            
            const date = new Date(year, month - 1, day);
            date.setHours(0, 0, 0, 0);
            return date;
        }
    </script>
</body>
</html> 