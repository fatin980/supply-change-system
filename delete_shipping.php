<?php
include 'config.php'; // Database connection

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Shipping ID.");
}

$shipping_id = $_GET['id'];

// Delete query
$query = "DELETE FROM shipping_terms WHERE shipping_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $shipping_id);

if ($stmt->execute()) {
    // Check if the table is empty
    $result = $conn->query("SELECT COUNT(*) AS total FROM shipping_terms");
    $row = $result->fetch_assoc();

    if ($row['total'] == 0) {
        // Reset auto-increment if the table is empty
        $conn->query("ALTER TABLE shipping_terms AUTO_INCREMENT = 1");
    }

    echo "<script>alert('Shipping term deleted successfully!'); window.location.href='shipping_terms.php';</script>";
} else {
    echo "<script>alert('Error deleting shipping term.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>