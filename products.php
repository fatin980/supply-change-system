<?php
    include 'config.php'; // Include database connection file
    include 'header.php';

    $limit = 10; // Number of entries per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Fetch products with search functionality
    $query = "SELECT * FROM products WHERE product_name LIKE ? OR currency LIKE ? LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("ssii", $searchParam, $searchParam, $start, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    // Count total records for pagination
    $totalQuery = "SELECT COUNT(*) AS total FROM products WHERE product_name LIKE ? OR currency LIKE ?";
    $stmtTotal = $conn->prepare($totalQuery);
    $stmtTotal->bind_param("ss", $searchParam, $searchParam);
    $stmtTotal->execute();
    $totalResult = $stmtTotal->get_result()->fetch_assoc();
    $total = $totalResult['total'];
    $pages = ceil($total / $limit);

?>

    <!-- Modal Styles -->
    <style>
    /* Modal Overlay */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6); /* Darker overlay */
        backdrop-filter: blur(5px); /* Adds blur effect */
        opacity: 0;
        transition: opacity 0.3s ease-in-out; /* Smooth fade-in effect */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fff;
        padding: 25px;
        width: 40%;
        max-width: 500px; /* Ensures it doesn't get too wide */
        text-align: center;
        border-radius: 12px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.95); /* Slight zoom effect */
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2); /* Softer shadow */
        opacity: 0;
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
    }

    /* When Modal is Shown */
    .modal.show {
        opacity: 1;
    }

    .modal.show .modal-content {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1); /* Zooms in smoothly */
    }

    /* Close Button */
    .close-btn {
        float: right;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        color: #555;
        transition: color 0.3s;
    }

    .close-btn:hover {
        color: #d9534f; /* Bootstrap danger color */
    }

    /* Input Fields & Select */
    .modal-content input,
    .modal-content textarea,
    .modal-content select {
        width: 80%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        transition: 0.2s;
    }

    .modal-content input:focus,
    .modal-content textarea:focus,
    .modal-content select:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        outline: none;
    }

    /* Buttons */
    .modal-content .btn {
        width: 80%;
        padding: 10px;
        font-size: 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: 0.2s;
    }

    .modal-content .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    .modal-content .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Responsive Design */
    @media (max-width: 600px) {
        .modal-content {
            width: 80%;
            padding: 20px;
        }
    }

    </style>
</head>
<body>
    <div class="container">
        <h2>Product List</h2>

        <div class="header-row">
            <div class="button-container">
                <button class="btn btn-success" onclick="openModal()">Create New</button>
            </div>
            <div class="search-container">
                <input type="text" id="search" placeholder="Search...">
            </div>
        </div>

        <!-- Custom Modal -->
        <div id="customModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2>Add New Product</h2>
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
                    
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>


        <!-- Table -->
        <table border="1">
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
                <?php while ($row = $result->fetch_assoc()): ?>
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
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
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
            function openModal() {
                let modal = document.getElementById("customModal");
                modal.classList.add("show");
                modal.style.display = "block";
            }

            // Close Modal
            function closeModal() {
                let modal = document.getElementById("customModal");
                modal.classList.remove("show");
                setTimeout(() => {
                    modal.style.display = "none";
                }, 300); // Delay to match transition effect
            }

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
