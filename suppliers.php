<?php

include 'config.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
    <link rel="stylesheet" href="css/supplier.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<?php include 'header.php'; ?>
<div class="content">
    <div class="content-container">
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
        <button class="btn add-supplier-btn" onclick="openModal('addSupplierModal')">Create New</button>

    </div>

    <!-- Add Supplier Modal -->
<div id="addSupplierModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Supplier</h2>
            <button class="close-btn" onclick="closeModal('addSupplierModal')">✖</button>
        </div>
        <div class="modal-body">
            <form id="addSupplierForm">
                <label>Supplier Name:</label>
                <input type="text" name="supplier" required>

                <label>Contact Person:</label>
                <input type="text" name="contact_person" required>

                <label>Contact Number:</label>
                <input type="text" name="contact_number" required>

                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Address:</label>
                <textarea name="address" required></textarea>

                <label>Status:</label>
                <select name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                
                <button type="submit" class="add">Add Supplier</button>
            </form>
        </div>
    </div>
</div>


<!-- View Supplier Modal -->
<div id="viewModal" class="view-modal" style="display: none;">
    <div class="view-modal-content">
        <div class="view-modal-header">
            <h2>Supplier Details</h2>
        <span class="close-btn" onclick="closeModal('viewModal')">✖</span>
        </div>
        <div class="view-modal-body">
        <p><strong>Supplier:</strong> <span id="supplier_name"></span></p>
        <p><strong>Contact Person:</strong> <span id="contact_person"></span></p>
        <p><strong>Contact Number:</strong> <span id="contact_number"></span></p>
        <p><strong>Email:</strong> <span id="email"></span></p>
        <p><strong>Address:</strong> <span id="address"></span></p>
        <p><strong>Status:</strong> 
            <span id="view_status" class="badge"></span>
        </p>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
        <h2>Edit Supplier</h2>
        <span class="close-btn" onclick="closeModal('editModal')">✖</span>
        </div>
        <div class="modal-body">
        <input type="hidden" id="edit_id">
        <label>Supplier Name: <input type="text" id="edit_supplier"></label><br>
        <label>Contact Person: <input type="text" id="edit_contact_person"></label><br>
        <label>Contact Number: <input type="text" id="edit_contact_number"></label><br>
        <label>Email: <input type="email" id="edit_email"></label><br>
        <label>Address: <textarea type="text" id="edit_address"></textarea></label><br>
        <label>Status: <select id="edit_status">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select></label><br>
        <button class= "edit" onclick="saveChanges()">Save</button>
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

// Open modal function
function openModal(modalId) {
    document.getElementById(modalId).style.display = "flex";
}

// Close modal function
function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

window.onclick = function (event) {
    let viewModal = document.getElementById("viewModal");
    let addSupplierModal = document.getElementById("addSupplierModal");
    let editModal = document.getElementById("editModal");

    if (event.target === viewModal) {
        closeModal("viewModal");
    }
    if (event.target === addSupplierModal) {
        closeModal("addSupplierModal");
    }
    if (event.target === editModal) {
        closeModal("editModal");
    }
};



// Handle form submission
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
                    closeModal("addSupplierModal"); // Close modal
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
                
                let statusBadge = $("#view_status");
                statusBadge.text(data.status);
                
                if (data.status.toLowerCase() === "active") {
                    statusBadge.css("background", "green").css("color", "white");
                } else {
                    statusBadge.css("background", "gray").css("color", "white");
                }

                openModal("viewModal"); // Open custom modal
            }
        }
    });
});

$(document).on("click", ".edit_data", function () {
    let supplierId = $(this).data("id");

    $.ajax({
        url: "get_supplier.php",
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

            // Fix: Ensure correct status selection
            let statusValue = response.status.toLowerCase() === "active" ? "Active" : "Inactive";
            $("#edit_status").val(statusValue);

            openModal("editModal"); // Open modal
        }
    });
});


// Save changes function
function saveChanges() {
    let id = $("#edit_id").val();
    let supplier = $("#edit_supplier").val();
    let contact_person = $("#edit_contact_person").val();
    let contact_number = $("#edit_contact_number").val();
    let email = $("#edit_email").val();
    let address = $("#edit_address").val();
    let status = $("#edit_status").val();

    $.ajax({
        url: "update_supplier.php",
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
                closeModal("editModal");
                location.reload();
            } else {
                alert("Update failed!");
            }
        }
    });
}



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

document.addEventListener("click", function (event) {
    const dropdowns = document.querySelectorAll(".dropdown-menu");

    dropdowns.forEach((menu) => {
        if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
            menu.classList.remove("show");
        }
    });
});

function toggleDropdown(event) {
    event.stopPropagation();
    let menu = event.target.nextElementSibling;
    menu.classList.toggle("show");
}



    $(document).ready(function () {
        fetchData(1);
    });
</script>
</body>
</html>
