<?php
    include 'config.php'; // Include database connection file
    include 'header.php';

    $limit = 10; // Number of entries per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Fetch shipping terms with search functionality
    $query = "SELECT * FROM shipping_terms WHERE requisitioners LIKE ? OR shipping_terms LIKE ? OR deliver_via LIKE ? LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $start, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    // Count total records for pagination
    $totalQuery = "SELECT COUNT(*) AS total FROM shipping_terms 
                   WHERE requisitioners LIKE ? 
                   OR shipping_terms LIKE ? 
                   OR deliver_via LIKE ?";
    $stmtTotal = $conn->prepare($totalQuery);
    $stmtTotal->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmtTotal->execute();
    $totalResult = $stmtTotal->get_result()->fetch_assoc();
    $total = $totalResult['total'];
    $pages = ceil($total / $limit);
?>
    <style>
    /* Modal Background */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Dim background */
        justify-content: center;
        align-items: center;
    }

    /* Modal Content Box */
    .modal-content {
        background: #fff;
        padding: 20px;
        width: 400px;
        border-radius: 8px;
        position: relative;
        text-align: center;
    }

    /* Close Button */
    .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
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
        <h2>Shipping Terms List</h2>

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
                <h2>Add New Shipping Term</h2>
                <form id="shippingForm">
                    <input type="text" id="requisitioners" placeholder="Requisitioners" required>

                    <!-- Shipping Terms Dropdown -->
                    <select id="shipping_terms" required>
                        <option value="FOB">FOB</option>
                        <option value="CIF">CIF</option>
                        <option value="NET 30">NET 30</option>
                        <option value="NET 14">NET 14</option>
                        <option value="NET 60">NET 60</option>
                    </select>

                    <!-- Delivery Method Dropdown -->
                    <select id="deliver_via" required>
                        <option value="Virtual Live Classroom">Virtual Live Classroom</option>
                        <option value="Face to Face">Face to Face</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="E-Learning">E-Learning</option>
                        <option value="Certification">Certification</option>
                        <option value="Onsite Client Location">Onsite Client Location</option>
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
                    <th>Requisitioners</th>
                    <th>Shipping Terms</th>
                    <th>Deliver Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['shipping_id']; ?></td>
                        <td><?php echo $row['date_created']; ?></td>
                        <td><?php echo $row['requisitioners']; ?></td>
                        <td><?php echo $row['shipping_terms']; ?></td>
                        <td><?php echo $row['deliver_via']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>

                        <td>
                            <a href="view_shipping.php?id=<?php echo $row['shipping_id']; ?>" class="btn btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_shipping.php?id=<?php echo $row['shipping_id']; ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_shipping.php?id=<?php echo $row['shipping_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this shipping term?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

<!-- Pagination -->
<div class="pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>

</div>

<?php include 'footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let searchInput = document.getElementById("search");

    searchInput.addEventListener("keyup", function () {
        let search = this.value.trim();

        let xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_shipping.php?search=" + encodeURIComponent(search), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.querySelector("tbody").innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });
});



// Sidebar Toggle
    function toggleSidebar() {
        let sidebar = document.querySelector(".sidebar");
        let content = document.querySelector(".content");
        sidebar.classList.toggle("open");
        content.classList.toggle("shift");
    }

// Open Modal
function openModal() {
    document.getElementById("customModal").style.display = "flex";
}

// Close Modal
function closeModal() {
    document.getElementById("customModal").style.display = "none";
}

// Close modal when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById("customModal");
    if (event.target === modal) {
        closeModal();
    }
};

document.getElementById("shippingForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent form from reloading the page

    let requisitioners = document.getElementById("requisitioners").value;
    let shipping_terms = document.getElementById("shipping_terms").value;
    let deliver_via = document.getElementById("deliver_via").value;
    let status = document.getElementById("status").value;

    let formData = new FormData();
    formData.append("requisitioners", requisitioners);
    formData.append("shipping_terms", shipping_terms);
    formData.append("deliver_via", deliver_via);
    formData.append("status", status);

    fetch("save_shipping_term.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Append new row to the table dynamically
            let tableBody = document.querySelector("tbody");
            let newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td>${data.id}</td>
                <td>${data.date_created}</td>
                <td>${requisitioners}</td>
                <td>${shipping_terms}</td>
                <td>${deliver_via}</td>
                <td>${status.charAt(0).toUpperCase() + status.slice(1)}</td>
                <td>
                    <a href="view_shipping.php?id=${data.id}" class="btn btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="edit_shipping.php?id=${data.id}" class="btn btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="delete_shipping.php?id=${data.id}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this shipping term?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            `;
            tableBody.prepend(newRow); // Add new row at the top

            closeModal(); // Close the modal
            document.getElementById("shippingForm").reset(); // Reset form fields
        } else {
            alert("Error saving data!");
        }
    })
    .catch(error => console.error("Error:", error));
});

</script>
</body>
</html>