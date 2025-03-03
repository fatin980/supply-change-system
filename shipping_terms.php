<?php
include 'config.php';

// Get search and pagination inputs
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Rows per page
$offset = ($page - 1) * $limit;

// Build search query
$whereClause = "";
if (!empty($search)) {
    $whereClause = " WHERE requisitioners LIKE '%$search%' 
    OR shipping_terms LIKE '%$search%'
    OR deliver_via LIKE '%$search%'";
}

// Count total rows (AFTER applying search filter)
$countQuery = "SELECT COUNT(*) AS total FROM shipping_terms  $whereClause";
$countResult = $conn->query($countQuery);
$totalRows = ($countResult->num_rows > 0) ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalRows / $limit);

// **If searching, ignore pagination**
if (!empty($search)) {
    $sql = "SELECT * FROM shipping_terms $whereClause"; // No LIMIT, show all results
} else {
    $sql = "SELECT * FROM shipping_terms $whereClause LIMIT $limit OFFSET $offset";
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
    <title>Shipping Terms</title>
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
                <h2>Shipping Terms List</h2>
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
                            <th>Requisitioners</th>
                            <th>Shipping Terms</th>
                            <th>Deliver Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                        <?php
                        $statusClass = '';

                        switch ($row['status']) {
                            case 'Active':
                                $statusClass = '<button style="background-color: #28a745; color: white; border: none; padding: 3px 6px; border-radius: 8px; cursor: pointer; font-weight: bold;">Active</button>';
                                break;
                            case 'Inactive':
                                $statusClass = '<button style="background-color: #aaaaaa; color: white; border: none; padding: 3px 6px; border-radius: 8px; cursor: pointer; font-weight: bold;">Inactive</button>';
                                break;
                        }
                        ?>
                            <tr>
                                <td><?php echo $row['shipping_id']; ?></td>
                                <td><?php echo $row['date_created']; ?></td>
                                <td><?php echo $row['requisitioners']; ?></td>
                                <td><?php echo $row['shipping_terms']; ?></td>
                                <td><?php echo $row['deliver_via']; ?></td>
                                <td><?php echo $statusClass ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="dropbtn" onclick="toggleDropdown(this)">Action <i class="fa-solid fa-caret-down"></i></button>
                                        <div class="dropdown-content">
                                            <a href="view_shipping.php?id=<?php echo $row['shipping_id']; ?>"><i class="fa-regular fa-eye"></i> View</a>
                                            <a href="edit_shipping.php?id=<?php echo $row['shipping_id']; ?>"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                            <a href="delete_shipping.php?id=<?php echo $row['shipping_id']; ?>" onclick="return confirm('Are you sure you want to delete this entry?')"><i class="fa-regular fa-trash-can"></i> Delete</a>
                                        </div>
                                    </div>
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
            <h3>Add New Shipping Term</h3>
            <form id="shippingForm">
                <label>Requisitioners:</label>
                <input type="text" id="requisitioners" required>

                <!-- Shipping Terms Dropdown -->
                <label>Shipping Terms:</label>
                <select id="shipping_terms" required>
                    <option value="FOB">FOB</option>
                    <option value="CIF">CIF</option>
                    <option value="NET 30">NET 30</option>
                    <option value="NET 14">NET 14</option>
                    <option value="NET 60">NET 60</option>
                </select>

                <!-- Delivery Method Dropdown -->
                <label>Deliver Via:</label>
                <select id="deliver_via" required>
                    <option value="Virtual Live Classroom">Virtual Live Classroom</option>
                    <option value="Face to Face">Face to Face</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="E-Learning">E-Learning</option>
                    <option value="Certification">Certification</option>
                    <option value="Onsite Client Location">Onsite Client Location</option>
                </select>

                <!-- Status Dropdown -->
                <label>Status:</label>
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

        function toggleDropdown(button) {
            let dropdown = button.nextElementSibling;
            dropdown.classList.toggle("show");

            // Close other dropdowns when opening a new one
            document.querySelectorAll(".dropdown-content").forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.remove("show");
                }
            });
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