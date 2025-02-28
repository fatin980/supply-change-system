<?php
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requisitioners = $_POST['requisitioners'];
    $shipping_terms = $_POST['shipping_terms'];
    $deliver_via = $_POST['deliver_via'];
    $status = $_POST['status'];
    $date_created = date("Y-m-d H:i:s"); // Get current timestamp

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO shipping_terms (requisitioners, shipping_terms, deliver_via, status, date_created) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $requisitioners, $shipping_terms, $deliver_via, $status, $date_created);

    if ($stmt->execute()) {
        $last_id = $conn->insert_id; // Get inserted ID
        echo json_encode([
            "success" => true,
            "id" => $last_id,
            "date_created" => $date_created
        ]);
    } else {
        echo json_encode(["success" => false]);
    }

    $stmt->close();
    $conn->close();
}
?>
