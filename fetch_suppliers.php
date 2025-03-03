<?php
session_start();
include 'config.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'supplier';

$allowedColumns = ['supplier', 'contact_person', 'contact_number', 'email', 'address', 'status'];
if (!in_array($category, $allowedColumns)) {
    $category = 'supplier';
}

// Query for fetching suppliers
$sql = "SELECT * FROM suppliers";
$count_sql = "SELECT COUNT(*) as total FROM suppliers";

if (!empty($search)) {
    $sql .= " WHERE $category LIKE '%$search%'";
    $count_sql .= " WHERE $category LIKE '%$search%'";
}

$sql .= " LIMIT $limit OFFSET $offset";

$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $i = $offset + 1;
    while ($row = $result->fetch_assoc()) {
        $status = strtolower($row['status']);
        $statusClass = ($status === 'active') ? 'status-active' : 'status-inactive';

        echo "<tr>
            <td>{$i}</td>
            <td>" . date("Y-m-d H:i", strtotime($row['date_created'])) . "</td>
            <td>{$row['supplier']}</td>
            <td>{$row['contact_person']} <br> {$row['contact_number']}</td>
            <td>{$row['email']}</td>
            <td>{$row['address']}</td>
            <td><span class='status-badge {$statusClass}'>" . htmlspecialchars($row['status']) . "</span></td>";

        if ($_SESSION['role'] == 'admin') {
            echo '<td align="center">
                <div class="dropdown">
                    <button class="dropdown-button" onclick="toggleDropdown(event)">Actions<i class="fa-solid fa-caret-down"></i></button>
                    <div class="dropdown-menu">
                        <button class="view_data" data-id="' . $row['id'] . '">
                            <i class="fa-regular fa-eye"></i> View
                        </button>
                            <button class="edit_data" data-id="' . $row['id'] . '">
                            <i class="fa-regular fa-pen-to-square"></i> Edit
                        </button>
                            <button class="delete_data" data-id="' . $row['id'] . '">
                            <i class="fa-regular fa-trash-can"></i> Delete
                        </button>
                    </div>
                </div>
            </td>';
        }
        echo "</tr>";
        $i++;
    }
} else {
    echo "<tr><td colspan='8' style='text-align: center; font-weight: bold;'>No suppliers found</td></tr>";
}

// Pagination
$pagination = "<div class='pagination'>";

// Previous button
if ($page > 1) {
    $prev = $page - 1;
    $pagination .= "<a href='#' onclick='fetchData($prev)' class='page-link'>&laquo; Prev</a>";
}

// Numbered pagination
$range = 1;
$start = max(1, $page - $range);
$end = min($total_pages, $page + $range);

if ($start > 1) {
    $pagination .= "<a href='#' onclick='fetchData(1)' class='page-link'>1</a>";
    if ($start > 2) {
        $pagination .= "<span class='dots'>...</span>";
    }
}

for ($i = $start; $i <= $end; $i++) {
    $active = ($i == $page) ? "active" : "";
    $pagination .= "<a href='#' onclick='fetchData($i)' class='page-link $active'>$i</a>";
}

if ($end < $total_pages) {
    if ($end < $total_pages - 1) {
        $pagination .= "<span class='dots'>...</span>";
    }
    $pagination .= "<a href='#' onclick='fetchData($total_pages)' class='page-link'>$total_pages</a>";
}

// Next button
if ($page < $total_pages) {
    $next = $page + 1;
    $pagination .= "<a href='#' onclick='fetchData($next)' class='page-link'>Next &raquo;</a>";
}

$pagination .= "</div>";

echo "<!--PAGINATION-->";
echo $pagination;
$conn->close();
?>
