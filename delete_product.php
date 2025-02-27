<?php
include 'config.php'; // Database connection

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Product ID.");
}

$product_id = $_GET['id'];

// Delete query
$query = "DELETE FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    // Check if the table is empty
    $result = $conn->query("SELECT COUNT(*) AS total FROM products");
    $row = $result->fetch_assoc();

    if ($row['total'] == 0) {
        // Reset auto-increment if the table is empty
        $conn->query("ALTER TABLE products AUTO_INCREMENT = 1");
    }

    echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
} else {
    echo "<script>alert('Error deleting product.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
