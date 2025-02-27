<?php
include 'config.php'; // Database connection

// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $unit_price = $_POST['unit_price'];
    $currency = $_POST['currency'];
    $status = $_POST['status'];

    // Validate inputs (optional but recommended)
    if (empty($product_id) || empty($product_name) || empty($description) || empty($unit_price) || empty($currency) || empty($status)) {
        die("⚠️ Error: All fields are required.");
    }

    // Prepare SQL statement to update product
    $query = "UPDATE products 
              SET product_name = ?, description = ?, unit_price = ?, currency = ?, status = ? 
              WHERE product_id = ?";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("❌ Error preparing statement: " . $conn->error);
    }

    // Bind parameters and execute
    $stmt->bind_param("ssdssi", $product_name, $description, $unit_price, $currency, $status, $product_id);

    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: products.php?success=Product updated successfully");
        exit();
    } else {
        die("❌ Error updating product: " . $stmt->error);
    }

    // Close statement & connection
    $stmt->close();
    $conn->close();
} else {
    die("⚠️ Invalid request.");
}
