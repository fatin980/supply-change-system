<?php
include 'config.php'; // Database connection
include 'header.php';
?>
<link rel="stylesheet" href="css/add-po.css"> <!-- Custom CSS file -->
</head>
<body>

<div class="content">
    <h2>Create New Purchase Order</h2>

    <form id="purchaseOrderForm">
        <div class="form-container">
            <div class="form-group">
                <label for="po_no">PO Number:</label>
                <input type="text" id="po_no" name="po_no" value="" readonly>
                <small class="text-muted"><em>Leave this blank to Automatically Generate upon saving</em></small>
            </div>

            <div class="form-group">
                <label for="deliver_to">Deliver To:</label>
                <select id="deliver_to" name="deliver_to" required>
                    <option value="">Select Receiver</option>
                    <?php

                    $query = "SELECT id, name FROM deliver_to"; // Adjust column names if needed
                    $result = $conn->query($query);

                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="shipping_terms">Requisitioner:</label>
                <select id="shipping_terms" name="shipping_terms" required>
                    <option value="">Select Requisitioner</option>
                    <?php

                    $query = "SELECT shipping_id, requisitioners FROM shipping_terms";
                    $result = $conn->query($query);

                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['shipping_id'] . '">' . $row['requisitioners'] . '</option>';
                    }
                    ?>
                </select>
            </div>


            <div class="form-group">
                <label for="quotation_no">Quotation Number:</label>
                <input type="text" id="quotation_no" name="quotation_no" required>
            </div>

            <div class="form-group">
                <label for="project_code">Project Code:</label>
                <input type="text" id="project_code" name="project_code" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>

            <div class="form-group">
                <label for="payment_terms">Payment Terms:</label>
                <input type="text" id="payment_terms" name="payment_terms" required>
            </div>
    </div>

            <div class="table-container">
            <table border="1" width="100%" id="orderTable">
                <thead>
                    <tr>
                        <th></th> <!-- Delete icon column -->
                        <th>Item</th>
                        <th>Description</th>
                        <th>Day</th>
                        <th>Unit</th>
                        <th>Currency</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><button type="button" class="delete-row">🗑</button></td>
                        <td>
                            <select class="item-dropdown">
                                <option value="">Select Item</option>
                                <?php
                                $query = "SELECT product_id, product_name, description, unit_price FROM products";
                                $result = $conn->query($query);

                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['product_id'] . '" data-description="' . $row['description'] . '" data-price="' . $row['unit_price'] . '">' . $row['product_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td><input type="text" class="description" readonly></td>
                        <td><input type="number" class="day" min="1" value="1"></td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <input type="number" class="unit" min="1" value="1">
                                <button type="button" class="add-unit">+</button>
                            </div>
                        </td>
                        <td><input type="text" class="currency" readonly value="MYR"></td>
                        <td><input type="text" class="price" readonly></td>
                        <td><input type="text" class="total" readonly></td>
                    </tr>
                </tbody>
            </table>

                <div class="button-container" style="margin-top: 20px; text-align: right;">
                    <button type="button" id="addRow" class="btn btn-success">+ Add Row</button>
                </div>
            </div>                 

            <div class="form-container">
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label for="payment_status">Payment Status:</label>
                <select id="payment_status" name="payment_status">
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
        </div>

        <div class="button-container">
            <button type="submit" class="btn btn-success">Submit</button>
            <a href="purchase_order.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
    </div>

    <?php include 'footer.php'; ?>
    <script>
    
        document.addEventListener("DOMContentLoaded", function () {
        // Ensure form submission works properly
        let purchaseOrderForm = document.getElementById("purchaseOrderForm");
        if (purchaseOrderForm) {
            purchaseOrderForm.addEventListener("submit", function (event) {
                event.preventDefault();

                let formData = new FormData(this);

                fetch("save_purchase_order.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Purchase Order Created Successfully!");
                        window.location.href = "purchase_orders.php";
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
            });
        }

        // Table Products
        let productData = <?php echo json_encode($products ?? []); ?>; // Ensure productData is always an object

        function updateRow(row) {
            let itemDropdown = row.querySelector(".item-dropdown");
            let description = row.querySelector(".description");
            let price = row.querySelector(".price");
            let unit = row.querySelector(".unit");
            let day = row.querySelector(".day");
            let total = row.querySelector(".total");

            if (!itemDropdown || !description || !price || !unit || !day || !total) return;

            let item = itemDropdown.value;

            if (item && productData[item]) {
                description.value = productData[item].description;
                price.value = productData[item].price;
                total.value = (parseFloat(productData[item].price) * parseInt(unit.value || 0) * parseInt(day.value || 0)).toFixed(2);
            } else {
                description.value = "";
                price.value = "";
                total.value = "";
            }
        }

        let orderTable = document.querySelector("#orderTable");
        if (orderTable) {
            orderTable.addEventListener("change", function (e) {
                if (e.target.classList.contains("item-dropdown") || e.target.classList.contains("unit") || e.target.classList.contains("day")) {
                    updateRow(e.target.closest("tr"));
                }
            });

            orderTable.addEventListener("click", function (e) {
                if (e.target.classList.contains("add-unit")) {
                    let unitInput = e.target.previousElementSibling;
                    if (unitInput) {
                        unitInput.value = parseInt(unitInput.value || 0) + 1;
                        updateRow(e.target.closest("tr"));
                    }
                }
                if (e.target.classList.contains("delete-row")) {
                    let row = e.target.closest("tr");
                    if (document.querySelectorAll("#orderTable tbody tr").length > 1) {
                        row.remove();
                    } else {
                        alert("You must have at least one row!");
                    }
                }
            });
        }

        let addRowButton = document.querySelector("#addRow");
        if (addRowButton) {
            addRowButton.addEventListener("click", function () {
                let tableBody = document.querySelector("#orderTable tbody");
                if (tableBody && tableBody.rows.length > 0) {
                    let newRow = tableBody.rows[0].cloneNode(true);
                    newRow.querySelectorAll("input").forEach(input => input.value = "");
                    newRow.querySelector(".item-dropdown").value = "";
                    tableBody.appendChild(newRow);
                }
            });
        }

        // Sidebar Toggle
        let sidebarToggle = document.querySelector(".sidebar-toggle");
        if (sidebarToggle) {
            sidebarToggle.addEventListener("click", function () {
                let sidebar = document.querySelector(".sidebar");
                let content = document.querySelector(".content");
                if (sidebar && content) {
                    sidebar.classList.toggle("open");
                    content.classList.toggle("shift");
                }
            });
        }
    });


    </script>

</body>
</html>
