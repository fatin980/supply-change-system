<?php
include 'header.php';
include 'config.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
    <link rel="stylesheet" href="css/supplier.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<div class="content">
    <div class="content-box">
    <h2>List of Suppliers</h2>
    
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <form class="search-form">
            <select id="searchCategory" class="search-category" onchange="fetchData(1)">
                <option value="supplier">Supplier</option>
                <option value="contact_person">Contact Person</option>
                <option value="contact_number">Contact Number</option>
                <option value="email">Email</option>
                <option value="address">Address</option>
                <option value="status">Status</option>
            </select>
            <input type="text" id="search" placeholder="Search..." onkeyup="fetchData(1)" autocomplete="off">
            <button type="button" class="clear-btn" onclick="fetchData(1)">✖</button>
        </form>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">Create New</button>
    </div>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
            <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm">
                    <label>Supplier Name:</label>
                    <input type="text" name="supplier" class="form-control" required>

                    <label>Contact Person:</label>
                    <input type="text" name="contact_person" class="form-control" required>

                    <label>Contact Number:</label>
                    <input type="text" name="contact_number" class="form-control" required>

                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required>

                    <label>Address:</label>
                    <textarea name="address" class="form-control" required></textarea>

                    <label>Status:</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <br>
                    <button type="submit" class="btn btn-success">Add Supplier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog view-modal-dialog">
        <div class="modal-content view-modal-content">
            <div class="modal-header view-modal-header">
                <h5 class="modal-title">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body view-modal-body">
                <p><strong>Supplier Name:</strong> <span id="supplier_name"></span></p>
                <p><strong>Contact Person:</strong> <span id="contact_person"></span></p>
                <p><strong>Contact Number:</strong> <span id="contact_number"></span></p>
                <p><strong>Email:</strong> <span id="email"></span></p>
                <p><strong>Address:</strong> <span id="address"></span></p> 
                <p><strong>Status:</strong> 
                    <span class="badge" id="view_status" style="padding: 5px 10px 5px 10px; margin-top: 5px; width: 60px; margin-right: 50%; text-align: center;"></span>
                </p>              
            </div>
            <div class="modal-footer view-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog view-modal-dialog">
        <div class="modal-content view-modal-content">
            <div class="modal-header view-modal-header">
                <h5 class="modal-title">Edit Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body view-modal-body">
                <input type="hidden" id="edit_id">
                
                <div class="mb-3">
                    <label class="form-label">Supplier Name</label>
                    <input type="text" class="form-control" id="edit_supplier">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Contact Person</label>
                    <input type="text" class="form-control" id="edit_contact_person">
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="edit_contact_number">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="edit_email">
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" id="edit_address">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="edit_status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer view-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save_changes">Save Changes</button>
            </div>
        </div>
    </div>
</div>



    <div class="table-container">
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Time Created</th>
                    <th>Supplier</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <th>Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody id="table-content">
                <!-- Data will be fetched dynamically via AJAX -->
            </tbody>
        </table>
    </div>
    <div class='pagination'></div>
</div>
</div>

<?php include 'footer.php'; ?>

<script>
    function toggleSidebar() {
            let sidebar = document.querySelector('.sidebar');
            let content = document.querySelector('.content');
            sidebar.classList.toggle('open');
            content.classList.toggle('shift');
        }

        function fetchData(page) {
            let searchQuery = $("#search").val();
            let category = $("#searchCategory").val();

            $.ajax({
                url: "fetch_suppliers.php",
                type: "GET",
                data: { query: searchQuery, category: category, page: page },
                success: function (response) {
                    let parts = response.split("<!--PAGINATION-->");
                    $("#table-content").html(parts[0]); // Update table
                    $(".pagination").html(parts[1]); // Update pagination
                }
            });
        }

    $(document).ready(function () {
    $("#addSupplierForm").submit(function (event) {
        event.preventDefault(); // Prevent default form submission
        
        let formData = $(this).serialize(); // Get form data

        $.ajax({
            url: "add_supplier.php", // Ensure this file exists
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.trim() === "success") {
                    alert("Supplier added successfully!");
                    $("#addSupplierModal").modal("hide"); // Close modal
                    $("#addSupplierForm")[0].reset(); // Reset form
                    fetchData(1); // Refresh supplier list
                } else {
                    alert("Error: " + response);
                }
            }
        });
    });
});

$(document).on("click", ".view_data", function () {
    let supplierId = $(this).data("id");

    $.ajax({
        url: "get_supplier.php",
        type: "POST",
        data: { id: supplierId },
        dataType: "json",
        success: function (data) {
            if (data) {
                $("#supplier_name").text(data.supplier);
                $("#contact_person").text(data.contact_person);
                $("#contact_number").text(data.contact_number);
                $("#email").text(data.email);
                $("#address").text(data.address);

                // DEBUG: Check if status is received
                console.log("Status Received: ", data.status);

                // Target the correct status badge in View Modal
                let statusBadge = $("#view_status");
                statusBadge.text(data.status); // Set status text

                // Change the badge color dynamically
                if (data.status && data.status.toLowerCase() === "active") {
                    statusBadge.removeClass("bg-secondary").addClass("bg-success");
                } else {
                    statusBadge.removeClass("bg-success").addClass("bg-secondary");
                }

                // Show the modal
                $("#viewModal").modal("show");
            }
        },
        error: function () {
            alert("Failed to fetch supplier data.");
        }
    });
});



$(document).on("click", ".edit_data", function () {
    let supplierId = $(this).data("id"); // Get supplier ID

    $.ajax({
        url: "get_supplier.php", // PHP file to fetch data
        type: "POST",
        data: { id: supplierId },
        dataType: "json",
        success: function (response) {
            $("#edit_id").val(response.id);
            $("#edit_supplier").val(response.supplier);
            $("#edit_contact_person").val(response.contact_person);
            $("#edit_contact_number").val(response.contact_number);
            $("#edit_email").val(response.email);
            $("#edit_address").val(response.address);
            $("#edit_status").val(response.status);

            $("#editModal").modal("show"); // Open the modal
        }
    });
});

$("#save_changes").click(function () {
    let id = $("#edit_id").val();
    let supplier = $("#edit_supplier").val();
    let contact_person = $("#edit_contact_person").val();
    let contact_number = $("#edit_contact_number").val();
    let email = $("#edit_email").val();
    let address = $("#edit_address").val();
    let status = $("#edit_status").val();

    $.ajax({
        url: "update_supplier.php", // PHP file to handle the update
        type: "POST",
        data: {
            id: id,
            supplier: supplier,
            contact_person: contact_person,
            contact_number: contact_number,
            email: email,
            address: address,
            status: status
        },
        success: function (response) {
            if (response === "success") {
                alert("Supplier updated successfully!");
                $("#editModal").modal("hide");
                location.reload(); // Reload page to show updated data
            } else {
                alert("Update failed!");
            }
        }
    });
});



    $(document).on("click", ".delete_data", function () {
    let id = $(this).data("id"); // Get supplier ID
    if (confirm("Are you sure you want to delete this supplier?")) {
        $.post("delete_supplier.php", { id: id }, function (response) {
            if (response === "success") {
                alert("Deleted successfully!");
                location.reload(); // Refresh the page after deletion
            } else {
                alert("Failed to delete supplier!");
            }
        });
    }
});



    $(document).ready(function () {
        fetchData(1);
    });
</script>
</body>
</html>