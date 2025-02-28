<?php
include 'config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM shipping_terms WHERE requisitioners LIKE ? LIMIT 10";
$stmt = $conn->prepare($query);
$searchParam = "%$search%";
$stmt->bind_param("s", $searchParam);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['shipping_id']; ?></td>
            <td><?php echo $row['date_created']; ?></td>
            <td><?php echo $row['requisitioners']; ?></td>
            <td><?php echo $row['shipping_terms']; ?></td>
            <td><?php echo $row['deliver_via']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <a href="view_shipping.php?id=<?php echo $row['shipping_id']; ?>" class="btn btn-info"><i class="fas fa-eye"></i></a>
                <a href="edit_shipping.php?id=<?php echo $row['shipping_id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                <a href="delete_shipping.php?id=<?php echo $row['shipping_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
    <?php endwhile;
} else {
    echo "<tr><td colspan='3'>No results found.</td></tr>";
}

$stmt->close();
$conn->close();
?>
