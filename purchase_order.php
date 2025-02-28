<?php
    include 'config.php'; // Include database connection file
    include 'header.php';

    $limit = 10; // Number of entries per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Fetch purchase orders with search functionality
    $query = "SELECT * FROM p_order WHERE po_no LIKE ? OR quotation_no LIKE ? OR project_code LIKE ? OR status LIKE ? OR payment_status LIKE ? 
              LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("ssssssi", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $start, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    // Count total records for pagination
    $totalQuery = "SELECT COUNT(*) AS total FROM p_order WHERE po_no LIKE ? OR quotation_no LIKE ? OR project_code LIKE ? OR status LIKE ? 
                   OR payment_status LIKE ?";
    $stmtTotal = $conn->prepare($totalQuery);
    $stmtTotal->bind_param("sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
    $stmtTotal->execute();
    $totalResult = $stmtTotal->get_result()->fetch_assoc();
    $total = $totalResult['total'];
    $pages = ceil($total / $limit);
?>
</head>
<body>
<div class="container">
        <h2>Purchase Orders</h2>

        <div class="header-row">
            <div class="button-container">
                <a href="add_purchase_order.php" class="btn btn-success">Create New</a>
            </div>
            <div class="search-container">
                <input type="text" id="search" placeholder="Search..." onkeyup="searchOrders()">
            </div>
        </div>

        <!-- Table -->
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>PO Number</th>
                    <th>Quotation Number</th>
                    <th>Project Code</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['p_id']; ?></td>
                        <td><?php echo $row['po_no']; ?></td>
                        <td><?php echo $row['quotation_no']; ?></td>
                        <td><?php echo $row['project_code']; ?></td>
                        <td><?php echo $row['start_date']; ?></td>
                        <td><?php echo $row['end_date']; ?></td>
                        <td><?php echo number_format($row['total_price'], 2); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td><?php echo ucfirst($row['payment_status']); ?></td>

                        <td>
                            <a href="view_order.php?id=<?php echo $row['p_id']; ?>" class="btn btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_order.php?id=<?php echo $row['p_id']; ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_order.php?id=<?php echo $row['p_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this purchase order?');">
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
        xhr.open("GET", "fetch_purchase_orders.php?search=" + encodeURIComponent(search), true);
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

</script>
</body>
</html>