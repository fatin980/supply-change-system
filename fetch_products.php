<?php
include 'config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM products WHERE product_name LIKE ? OR currency LIKE ? LIMIT 10";
$stmt = $conn->prepare($query);
$searchParam = "%$search%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['product_id']; ?></td>
        <td><?php echo $row['date_created']; ?></td>
        <td><?php echo $row['product_name']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['unit_price']; ?></td>
        <td><?php echo $row['currency']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <a href="view_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-info"><i class="fas fa-eye"></i></a>
            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
            <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
        </td>
    </tr>
<?php endwhile; ?>
