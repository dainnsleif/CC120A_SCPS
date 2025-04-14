<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Management - School Clinic</title>
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

        .btn-danger {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
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

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Appointments Management</h2>
                <button class="btn btn-primary" onclick="showAddAppointmentModal()">
                    <i class="fas fa-plus"></i> New Appointment
                </button>
            </div>
            <div class="card-body">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Appointment Modal -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">New Appointment</h2>
            <form id="appointmentForm">
                <input type="hidden" id="appointment_id">
                <div class="form-group">
                    <label for="student_id">Student</label>
                    <select id="student_id" class="form-control" required>
                        <!-- Students will be loaded here -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointment_date">Date</label>
                    <input type="date" id="appointment_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="appointment_time">Time</label>
                    <input type="time" id="appointment_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason</label>
                    <textarea id="reason" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" class="form-control" required>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load appointments and students when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadAppointments();
            loadStudents();
        });

        function loadAppointments() {
            fetch('appointments.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('appointmentsTableBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(appointment => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${appointment.appointment_id}</td>
                            <td>${appointment.student_name}</td>
                            <td>${appointment.appointment_date}</td>
                            <td>${appointment.appointment_time}</td>
                            <td>${appointment.reason}</td>
                            <td><span class="status-badge status-${appointment.status.toLowerCase()}">${appointment.status}</span></td>
                            <td>
                                <button class="btn btn-primary" onclick="editAppointment(${JSON.stringify(appointment)})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteAppointment(${appointment.appointment_id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function loadStudents() {
            fetch('students.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('student_id');
                    select.innerHTML = '<option value="">Select Student</option>';
                    
                    data.data.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.student_id;
                        option.textContent = `${student.last_name}, ${student.first_name}`;
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function showAddAppointmentModal() {
            document.getElementById('modalTitle').textContent = 'New Appointment';
            document.getElementById('appointmentForm').reset();
            document.getElementById('appointment_id').value = '';
            document.getElementById('appointmentModal').style.display = 'block';
        }

        function editAppointment(appointment) {
            document.getElementById('modalTitle').textContent = 'Edit Appointment';
            document.getElementById('appointment_id').value = appointment.appointment_id;
            document.getElementById('student_id').value = appointment.student_id;
            document.getElementById('appointment_date').value = appointment.appointment_date;
            document.getElementById('appointment_time').value = appointment.appointment_time;
            document.getElementById('reason').value = appointment.reason;
            document.getElementById('status').value = appointment.status;
            document.getElementById('appointmentModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('appointmentModal').style.display = 'none';
        }

        function deleteAppointment(appointmentId) {
            if (confirm('Are you sure you want to delete this appointment?')) {
                fetch(`appointments.php?id=${appointmentId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadAppointments();
                    } else {
                        alert('Error deleting appointment: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const appointmentId = document.getElementById('appointment_id').value;
            const appointmentData = {
                student_id: document.getElementById('student_id').value,
                appointment_date: document.getElementById('appointment_date').value,
                appointment_time: document.getElementById('appointment_time').value,
                reason: document.getElementById('reason').value,
                status: document.getElementById('status').value
            };

            if (appointmentId) {
                appointmentData.appointment_id = appointmentId;
            }

            fetch('appointments.php', {
                method: appointmentId ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(appointmentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeModal();
                    loadAppointments();
                } else {
                    alert('Error saving appointment: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html> 