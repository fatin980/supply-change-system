<?php 
include("config.php");

// Get search and pagination inputs
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Rows per page
$offset = ($page - 1) * $limit;

// Build search query
$whereClause = "";
if (!empty($search)) {
    $whereClause = " WHERE name LIKE '%$search%' OR company_name LIKE '%$search%' OR address LIKE '%$search%' OR city LIKE '%$search%' OR zip LIKE '%$search%' OR contact LIKE '%$search%'";
}

// Count total rows (AFTER applying search filter)
$countQuery = "SELECT COUNT(*) AS total FROM deliver_to $whereClause";
$countResult = $conn->query($countQuery);
$totalRows = ($countResult->num_rows > 0) ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalRows / $limit);

// **If searching, ignore pagination**
if (!empty($search)) {
    $sql = "SELECT * FROM deliver_to $whereClause"; // No LIMIT, show all results
} else {
    $sql = "SELECT * FROM deliver_to $whereClause LIMIT $limit OFFSET $offset";
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
    <title>Deliver To</title>
    <link rel="icon" type="image/x-icon" href="img/Junzo_logo.png">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/page.css">
    <!-- FontAwesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include ("header.php") ?>

    <div class="content">
        <div class="content-container">
            <div class="header-container">
                <h2>Deliver To</h2>
                <button class="create-btn" onclick="openModal('createDeliverTo')">
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
                            <th>Name</th>
                            <th>Company</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Zip</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                <tbody id="deliverToTable">
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
                            <td><?php echo $row['id'] ?></td>
                            <td><?php echo $row['date_created'] ?></td>
                            <td><?php echo $row['name'] ?></td>
                            <td><?php echo $row['company_name'] ?></td>
                            <td><?php echo $row['address'] ?></td>
                            <td><?php echo $row['city'] ?></td>
                            <td><?php echo $row['zip'] ?></td>
                            <td><?php echo $row['contact'] ?></td>
                            <td><?php echo $statusClass ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="dropbtn" onclick="toggleDropdown(this)">Action <i class="fa-solid fa-caret-down"></i></button>
                                    <div class="dropdown-content">
                                        <!-- <a href="view_deliver_to.php?id=<?= $row['id'] ?>"><i class="fa-regular fa-eye"></i> View</a> -->
                                        <a onclick="openDeliverToModal(<?= $row['id'] ?>)"><i class="fa-regular fa-eye"></i> View</a>
                                        <a onclick="openEditDeliverToModal(<?= $row['id'] ?>)"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                        <a href="delete_deliver_to.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this entry?')"><i class="fa-regular fa-trash-can"></i> Delete</a>
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

    <!-- Modals -->
    <?php include("create_deliver_to_modal.php"); ?>

    <!-- Modal for displaying delivery details -->
    <div id="viewDeliverToModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div id="deliveryDetailsContainer">
            </div>
        </div>
    </div>

    <!-- Edit Deliver To Modal -->
    <div id="editDeliverToModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editDeliverToModal')">&times;</span>
            <h3>Edit Delivery Details</h3>
        
            <form id="editDeliverToForm">
                <input type="hidden" id="editDeliverToId" name="id">
            
                <label>Name:</label>
                <input type="text" id="editName" name="name" required>

                <label>Company:</label>
                <input type="text" id="editCompany" name="company" required>

                <label>Address:</label>
                <input type="text" id="editAddress" name="address" required>

                <label>City:</label>
                <input type="text" id="editCity" name="city" required>

                <label>Zip:</label>
                <input type="text" id="editZip" name="zip" required>

                <label>Contact:</label>
                <input type="text" id="editContact" name="contact" required>

                <label>Status:</label>
                <select id="editStatus" name="status">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>

                <button type="submit" class="submit-btn">Update</button>
            </form>
        </div>
    </div>

    <?php include ("footer.php") ?>

    <script>
        function toggleSidebar() {
            let sidebar = document.querySelector('.sidebar');
            let content = document.querySelector('.content');
            sidebar.classList.toggle('open');
            content.classList.toggle('shift');
        }

        function openModal(modalId) {
            let modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.classList.add('show');
                }, 10);
            }
        }

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
        }

        // Function to open deliver to details modal
        function openDeliverToModal(deliverToId) {
            $.ajax({
                url: 'view_deliver_to.php',
                type: 'GET',
                data: { id: deliverToId },
                success: function(response) {
                    $('#deliveryDetailsContainer').html(response);  
                    // $('#viewDeliverToModal').css('display', 'flex');  // Show the modal
                    openModal('viewDeliverToModal');
                },
                error: function() {
                    alert('Failed to fetch delivery details.');
                }
            });
        }

        function openEditDeliverToModal(deliverToId) {
            $.ajax({
                url: 'get_deliver_to.php',
                type: 'GET',
                data: { id: deliverToId },
                success: function(response) {
                    let data = JSON.parse(response);

                    // Populate modal fields with existing data
                    $('#editDeliverToId').val(data.id);
                    $('#editName').val(data.name);
                    $('#editCompany').val(data.company_name);
                    $('#editAddress').val(data.address);
                    $('#editCity').val(data.city);
                    $('#editZip').val(data.zip);
                    $('#editContact').val(data.contact);
                    $('#editStatus').val(data.status);

                    openModal('editDeliverToModal'); // Show modal
                },
                error: function() {
                    alert('Failed to fetch delivery details.');
                }
            });
        }

        // Handle form submission
        $('#editDeliverToForm').submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: 'update_deliver_to.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    alert(response); // Show success message
                    closeModal('editDeliverToModal'); // Close modal
                    location.reload(); // Refresh the page
                },
                error: function() {
                    alert('Failed to update delivery details.');
                }        
            });
        });

        $(document).ready(function() {
            $('#search').on('keyup', function() {
                let searchTerm = $(this).val();
        
                $.ajax({
                    url: 'deliver_to.php',
                    type: 'GET',
                    data: { search: searchTerm },
                    success: function(response) {
                        let newTable = $(response).find('#deliverToTable').html();
                        $('#deliverToTable').html(newTable);
                    }
                });
            });
        });
    </script>
</body>
</html>