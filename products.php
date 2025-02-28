<?php
include 'config.php'; // Include database connection file

// Get search and pagination inputs
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Rows per page
$offset = ($page - 1) * $limit;

// Build search query
$whereClause = "";
if (!empty($search)) {
    $whereClause = " WHERE product_name LIKE '%$search%' OR currency LIKE '%$search%'";
}

// Count total rows (AFTER applying search filter)
$countQuery = "SELECT COUNT(*) AS total FROM products $whereClause";
$countResult = $conn->query($countQuery);
$totalRows = ($countResult->num_rows > 0) ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalRows / $limit);

// **If searching, ignore pagination**
if (!empty($search)) {
    $sql = "SELECT * FROM products $whereClause"; // No LIMIT, show all results
} else {
    $sql = "SELECT * FROM products $whereClause LIMIT $limit OFFSET $offset";
}

$result = $conn->query($sql);
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link rel="icon" type="image/x-icon" href="img/Junzo_logo.png">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/page.css">
    <!-- FontAwesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include ('header.php'); ?>

    <div class="content">
        <div class="content-container">
            <div class="header-container">
                <h2>Product List</h2>
                <button class="create-btn" onclick="openModal('customModal')">
                <i class="fa-solid fa-plus"></i> Create New
                </button>
            </div>

            <div class="search-container">
                <input type="text" id="search" placeholder="Search...">
            </div>

            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date Created</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Unit Price</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?php echo $row['product_id']; ?></td>
                                <td><?php echo $row['date_created']; ?></td>
                                <td><?php echo $row['product_name']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['unit_price']; ?></td>
                                <td><?php echo $row['currency']; ?></td>
                                <td><?php echo $row['status']; ?></td>
    
                                <td>
                                    <a href="view_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <a href="?page=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>" <?= ($page == 1) ? 'style="pointer-events: none; opacity: 0.5;"' : '' ?>>Previous</a>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <a href="?page=<?= min($totalPages, $page + 1) ?>&search=<?= urlencode($search) ?>" <?= ($page == $totalPages) ? 'style="pointer-events: none; opacity: 0.5;"' : '' ?>>Next</a>
            </div>
        </div>
    </div>

    <!-- Custom Modal -->
    <div id="customModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('customModal')">&times;</span>
            <h3>Add New Product</h3>
            <form id="productForm">
                <input type="text" id="product_name" placeholder="Product Name" required>
                <textarea id="description" placeholder="Description" required></textarea>
                <input type="number" id="unit_price" placeholder="Unit Price" step="0.01" required>
                    
                <!-- Currency Dropdown (RM, USD, INR) -->
                <select id="currency" required>
                    <option value="RM">RM</option>
                    <option value="USD">USD</option>
                    <option value="INR">INR</option>
                </select>
                    
                <!-- Status Dropdown -->
                <select id="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                    
                <button type="submit" class="submit-btn">Save</button>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Sidebar Toggle
            function toggleSidebar() {
                let sidebar = document.querySelector(".sidebar");
                let content = document.querySelector(".content");
                sidebar.classList.toggle("open");
                content.classList.toggle("shift");
            }

            // Search Products (Live Search)
            document.getElementById("search").addEventListener("input", function () {
                let search = this.value.trim();

                let xhr = new XMLHttpRequest();
                xhr.open("GET", "fetch_products.php?search=" + encodeURIComponent(search), true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.querySelector("tbody").innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            });

            // Open Modal
            function openModal(modalId) {
                let modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'flex';
                    setTimeout(() => {
                        modal.classList.add('show');
                    }, 10);
                }
            }

            // Close Modal
            function closeModal(modalId) {
            let modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                }
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                let modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    if (event.target === modal) {
                        closeModal(modal.id);
                    }
                });

                if (!event.target.matches('.dropbtn')) {
                    document.querySelectorAll(".dropdown-content").forEach(menu => {
                        menu.classList.remove("show");
                    });
                }
            };

            // Product Form Submission (AJAX)
            document.getElementById("productForm").addEventListener("submit", function (event) {
                event.preventDefault();

                let product_name = document.getElementById("product_name").value.trim();
                let description = document.getElementById("description").value.trim();
                let unit_price = document.getElementById("unit_price").value.trim();
                let currency = document.getElementById("currency").value;
                let status = document.getElementById("status").value;

                // Validation check
                if (!product_name || !description || !unit_price || !currency || !status) {
                    alert("Please fill in all fields before submitting.");
                    return;
                }

                let xhr = new XMLHttpRequest();
                xhr.open("POST", "save_product.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert("Product added successfully!");
                        closeModal();
                        location.reload();
                    }
                };

                let params = `product_name=${encodeURIComponent(product_name)}&description=${encodeURIComponent(description)}&unit_price=${encodeURIComponent(unit_price)}&currency=${encodeURIComponent(currency)}&status=${encodeURIComponent(status)}`;
                xhr.send(params);
            });

            // Expose functions globally if needed
            window.toggleSidebar = toggleSidebar;
            window.openModal = openModal;
            window.closeModal = closeModal;
        });

    </script>
</body>
</html>