<?php
session_start();
include 'config.php';

$limit = 5;
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
        echo "<tr>
            <td>{$i}</td>
            <td>" . date("Y-m-d H:i", strtotime($row['date_created'])) . "</td>
            <td>{$row['supplier']}</td>
            <td>{$row['contact_person']} <br> {$row['contact_number']}</td>
            <td>{$row['email']}</td>
            <td>{$row['address']}</td>
            <td><span class='badge " . 
            (($row['status'] === 'Active' || strtolower($row['status']) === 'active') ? 'bg-success' : 'bg-secondary') . "'>" . 
            htmlspecialchars($row['status']) . "</span></td>";
        if ($_SESSION['role'] == 'admin') {
            echo '<td align="center">
                <div class="btn-group">
                    <button type="btn" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon py-0" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Action <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button class="dropdown-item view_data" data-id="' . $row['id'] . '">
                                <span class="fa fa-info text-primary"></span> View
                            </button>
                        </li>
                        <li><div class="dropdown-divider"></div></li>
                        <li>
                            <button class="dropdown-item edit_data" data-id="' . $row['id'] . '">
                                <span class="fa fa-edit text-primary"></span> Edit
                            </button>
                        </li>
                        <li><div class="dropdown-divider"></div></li>
                        <li>
                            <button class="dropdown-item delete_data" data-id="' . $row['id'] . '">
                                <span class="fa fa-trash text-danger"></span> Delete
                            </button>
                        </li>
                    </ul>
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
