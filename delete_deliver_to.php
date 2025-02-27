<?php
// Start the session and include the database connection
session_start();
include('config.php');

// Check if the ID is passed
if (isset($_GET['id'])) {
    $deliver_to_id = $_GET['id'];

    // Prepare the SQL query to delete the deliver_to from the database using prepared statements
    $stmt = $conn->prepare("DELETE FROM deliver_to WHERE id = ?");
    $stmt->bind_param("i", $deliver_to_id); // Bind the deliver_to_id as an integer ("i")

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        // Redirect back to the main page
        header("Location: deliver_to.php");
        exit();
    } else {
        // If there is an error, display it
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // If no ID is provided, redirect to the main page
    header("Location: deliver_to.php");
    exit();
}
?>