<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - School Clinic</title>
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

        .stock-warning {
            color: var(--warning-color);
            font-weight: 500;
        }

        .stock-critical {
            color: var(--accent-color);
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Inventory Management</h2>
                <button class="btn btn-primary" onclick="showAddItemModal()">
                    <i class="fas fa-plus"></i> Add New Item
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="inventoryTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item Name</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Expiry Date</th>
                                <th>Reorder Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            <!-- Inventory items will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Item Modal -->
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Add New Item</h2>
            <form id="itemForm">
                <input type="hidden" id="item_id">
                <div class="form-group">
                    <label for="item_name">Item Name</label>
                    <input type="text" id="item_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" class="form-control" required min="0">
                </div>
                <div class="form-group">
                    <label for="unit">Unit</label>
                    <input type="text" id="unit" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="expiry_date">Expiry Date</label>
                    <input type="date" id="expiry_date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="reorder_level">Reorder Level</label>
                    <input type="number" id="reorder_level" class="form-control" required min="0">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load inventory when page loads
        document.addEventListener('DOMContentLoaded', loadInventory);

        function loadInventory() {
            fetch('inventory.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('inventoryTableBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(item => {
                        const row = document.createElement('tr');
                        const stockClass = item.quantity <= item.reorder_level ? 
                            (item.quantity === 0 ? 'stock-critical' : 'stock-warning') : '';
                        
                        row.innerHTML = `
                            <td>${item.item_id}</td>
                            <td>${item.item_name}</td>
                            <td>${item.description || '-'}</td>
                            <td class="${stockClass}">${item.quantity}</td>
                            <td>${item.unit}</td>
                            <td>${item.expiry_date || '-'}</td>
                            <td>${item.reorder_level}</td>
                            <td>
                                <button class="btn btn-primary" onclick="editItem(${JSON.stringify(item)})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteItem(${item.item_id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function showAddItemModal() {
            document.getElementById('modalTitle').textContent = 'Add New Item';
            document.getElementById('itemForm').reset();
            document.getElementById('item_id').value = '';
            document.getElementById('itemModal').style.display = 'block';
        }

        function editItem(item) {
            document.getElementById('modalTitle').textContent = 'Edit Item';
            document.getElementById('item_id').value = item.item_id;
            document.getElementById('item_name').value = item.item_name;
            document.getElementById('description').value = item.description;
            document.getElementById('quantity').value = item.quantity;
            document.getElementById('unit').value = item.unit;
            document.getElementById('expiry_date').value = item.expiry_date;
            document.getElementById('reorder_level').value = item.reorder_level;
            document.getElementById('itemModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('itemModal').style.display = 'none';
        }

        function deleteItem(itemId) {
            if (confirm('Are you sure you want to delete this item?')) {
                fetch(`inventory.php?id=${itemId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadInventory();
                    } else {
                        alert('Error deleting item: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        document.getElementById('itemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const itemId = document.getElementById('item_id').value;
            const itemData = {
                item_name: document.getElementById('item_name').value,
                description: document.getElementById('description').value,
                quantity: document.getElementById('quantity').value,
                unit: document.getElementById('unit').value,
                expiry_date: document.getElementById('expiry_date').value,
                reorder_level: document.getElementById('reorder_level').value
            };

            if (itemId) {
                itemData.item_id = itemId;
            }

            fetch('inventory.php', {
                method: itemId ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(itemData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeModal();
                    loadInventory();
                } else {
                    alert('Error saving item: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html> 