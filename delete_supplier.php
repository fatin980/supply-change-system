<?php
session_start();
include 'config.php';

// Ensure the request is POST and contains an ID
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = $conn->real_escape_string($_POST['id']);

    // Check if the supplier exists
    $check_sql = "SELECT * FROM suppliers WHERE id = '$id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Perform the delete operation
        $delete_sql = "DELETE FROM suppliers WHERE id = '$id'";
        if ($conn->query($delete_sql)) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "not_found"; // Supplier does not exist
    }
} else {
    echo "invalid_request";
}

$conn->close();
?>