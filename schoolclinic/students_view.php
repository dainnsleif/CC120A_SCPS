<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Management - School Clinic</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Students Management</h2>
                <button class="btn btn-primary" onclick="showAddStudentModal()">
                    <i class="fas fa-plus"></i> Add New Student
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="studentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Grade Level</th>
                                <th>Section</th>
                                <th>Gender</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <!-- Students will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Student Modal -->
    <div id="studentModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Add New Student</h2>
            <form id="studentForm">
                <input type="hidden" id="student_id">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="grade_level">Grade Level</label>
                    <input type="text" id="grade_level" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="section">Section</label>
                    <input type="text" id="section" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="birth_date">Birth Date</label>
                    <input type="date" id="birth_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" class="form-control">
                </div>
                <div class="form-group">
                    <label for="blood_type">Blood Type</label>
                    <input type="text" id="blood_type" class="form-control">
                </div>
                <div class="form-group">
                    <label for="allergies">Allergies</label>
                    <textarea id="allergies" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="guardian_name">Guardian Name</label>
                    <input type="text" id="guardian_name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="guardian_contact">Guardian Contact</label>
                    <input type="text" id="guardian_contact" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load students when page loads
        document.addEventListener('DOMContentLoaded', loadStudents);

        function loadStudents() {
            fetch('students.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('studentsTableBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(student => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${student.student_id}</td>
                            <td>${student.last_name}, ${student.first_name}</td>
                            <td>${student.grade_level}</td>
                            <td>${student.section}</td>
                            <td>${student.gender}</td>
                            <td>${student.contact_number || '-'}</td>
                            <td>
                                <button class="btn btn-primary" onclick="editStudent(${JSON.stringify(student)})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteStudent(${student.student_id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function showAddStudentModal() {
            document.getElementById('modalTitle').textContent = 'Add New Student';
            document.getElementById('studentForm').reset();
            document.getElementById('student_id').value = '';
            document.getElementById('studentModal').style.display = 'block';
        }

        function editStudent(student) {
            document.getElementById('modalTitle').textContent = 'Edit Student';
            document.getElementById('student_id').value = student.student_id;
            document.getElementById('first_name').value = student.first_name;
            document.getElementById('last_name').value = student.last_name;
            document.getElementById('grade_level').value = student.grade_level;
            document.getElementById('section').value = student.section;
            document.getElementById('birth_date').value = student.birth_date;
            document.getElementById('gender').value = student.gender;
            document.getElementById('address').value = student.address;
            document.getElementById('contact_number').value = student.contact_number;
            document.getElementById('blood_type').value = student.blood_type;
            document.getElementById('allergies').value = student.allergies;
            document.getElementById('guardian_name').value = student.guardian_name;
            document.getElementById('guardian_contact').value = student.guardian_contact;
            document.getElementById('studentModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('studentModal').style.display = 'none';
        }

        function deleteStudent(studentId) {
            if (confirm('Are you sure you want to delete this student?')) {
                fetch(`students.php?id=${studentId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadStudents();
                    } else {
                        alert('Error deleting student: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        document.getElementById('studentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const studentId = document.getElementById('student_id').value;
            const studentData = {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                grade_level: document.getElementById('grade_level').value,
                section: document.getElementById('section').value,
                birth_date: document.getElementById('birth_date').value,
                gender: document.getElementById('gender').value,
                address: document.getElementById('address').value,
                contact_number: document.getElementById('contact_number').value,
                blood_type: document.getElementById('blood_type').value,
                allergies: document.getElementById('allergies').value,
                guardian_name: document.getElementById('guardian_name').value,
                guardian_contact: document.getElementById('guardian_contact').value
            };

            if (studentId) {
                studentData.student_id = studentId;
            }

            fetch('students.php', {
                method: studentId ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(studentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeModal();
                    loadStudents();
                } else {
                    alert('Error saving student: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html> 