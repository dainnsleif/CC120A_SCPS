<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records Management - School Clinic</title>
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
            max-width: 800px;
            border-radius: 8px;
            max-height: 80vh;
            overflow-y: auto;
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

        .search-box {
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Medical Records Management</h2>
                <button class="btn btn-primary" onclick="showAddRecordModal()">
                    <i class="fas fa-plus"></i> New Medical Record
                </button>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search by student name..." onkeyup="filterRecords()">
                </div>
                <div class="table-responsive">
                    <table id="recordsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Visit Date</th>
                                <th>Symptoms</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                                <th>Follow-up Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recordsTableBody">
                            <!-- Medical records will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Record Modal -->
    <div id="recordModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">New Medical Record</h2>
            <form id="recordForm">
                <input type="hidden" id="record_id">
                <div class="form-group">
                    <label for="student_id">Student</label>
                    <select id="student_id" class="form-control" required>
                        <!-- Students will be loaded here -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="symptoms">Symptoms</label>
                    <textarea id="symptoms" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea id="diagnosis" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="treatment">Treatment</label>
                    <textarea id="treatment" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="prescription">Prescription</label>
                    <textarea id="prescription" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea id="notes" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="follow_up_date">Follow-up Date</label>
                    <input type="date" id="follow_up_date" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load medical records and students when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadMedicalRecords();
            loadStudents();
        });

        function loadMedicalRecords() {
            fetch('medical_records.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('recordsTableBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(record => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${record.record_id}</td>
                            <td>${record.student_name}</td>
                            <td>${record.visit_date}</td>
                            <td>${record.symptoms}</td>
                            <td>${record.diagnosis || '-'}</td>
                            <td>${record.treatment || '-'}</td>
                            <td>${record.follow_up_date || '-'}</td>
                            <td>
                                <button class="btn btn-primary" onclick="editRecord(${JSON.stringify(record)})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteRecord(${record.record_id})">
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

        function showAddRecordModal() {
            document.getElementById('modalTitle').textContent = 'New Medical Record';
            document.getElementById('recordForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('recordModal').style.display = 'block';
        }

        function editRecord(record) {
            document.getElementById('modalTitle').textContent = 'Edit Medical Record';
            document.getElementById('record_id').value = record.record_id;
            document.getElementById('student_id').value = record.student_id;
            document.getElementById('symptoms').value = record.symptoms;
            document.getElementById('diagnosis').value = record.diagnosis;
            document.getElementById('treatment').value = record.treatment;
            document.getElementById('prescription').value = record.prescription;
            document.getElementById('notes').value = record.notes;
            document.getElementById('follow_up_date').value = record.follow_up_date;
            document.getElementById('recordModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('recordModal').style.display = 'none';
        }

        function deleteRecord(recordId) {
            if (confirm('Are you sure you want to delete this medical record?')) {
                fetch(`medical_records.php?id=${recordId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadMedicalRecords();
                    } else {
                        alert('Error deleting record: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function filterRecords() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.getElementById('recordsTableBody').getElementsByTagName('tr');

            for (let row of rows) {
                const studentName = row.cells[1].textContent.toLowerCase();
                row.style.display = studentName.includes(searchText) ? '' : 'none';
            }
        }

        document.getElementById('recordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const recordId = document.getElementById('record_id').value;
            const recordData = {
                student_id: document.getElementById('student_id').value,
                symptoms: document.getElementById('symptoms').value,
                diagnosis: document.getElementById('diagnosis').value,
                treatment: document.getElementById('treatment').value,
                prescription: document.getElementById('prescription').value,
                notes: document.getElementById('notes').value,
                follow_up_date: document.getElementById('follow_up_date').value
            };

            if (recordId) {
                recordData.record_id = recordId;
            }

            fetch('medical_records.php', {
                method: recordId ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(recordData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeModal();
                    loadMedicalRecords();
                } else {
                    alert('Error saving record: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html> 