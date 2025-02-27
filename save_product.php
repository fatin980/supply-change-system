<?php
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST["product_name"];
    $description = $_POST["description"];
    $unit_price = $_POST["unit_price"];
    $currency = $_POST["currency"];
    $status = $_POST["status"];

    $product_id = uniqid("P_"); // Generate a unique ID
    $date_created = date("Y-m-d H:i:s"); // Get the current timestamp

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO products (product_id, product_name, description, unit_price, currency, status, date_created) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsss", $product_id, $product_name, $description, $unit_price, $currency, $status, $date_created);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
