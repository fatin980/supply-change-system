<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input fields to prevent empty values
    if (
        isset($_POST['id'], $_POST['supplier'], $_POST['contact_person'], 
              $_POST['contact_number'], $_POST['email'], $_POST['address'], $_POST['status'])
        && !empty($_POST['id'])
    ) {
        // Sanitize inputs
        $id = intval($_POST['id']); // Ensures ID is an integer
        $supplier = trim($_POST['supplier']);
        $contact_person = trim($_POST['contact_person']);
        $contact_number = trim($_POST['contact_number']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $address = trim($_POST['address']);
        $status = trim($_POST['status']);

        // Use a prepared statement to prevent SQL injection
        $sql = "UPDATE suppliers SET 
                supplier = ?, 
                contact_person = ?, 
                contact_number = ?, 
                email = ?, 
                address = ?, 
                status = ? 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssssi", $supplier, $contact_person, $contact_number, $email, $address, $status, $id);
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "error: " . $stmt->error; // Debugging purpose (remove in production)
            }
            $stmt->close();
        } else {
            echo "error: " . $conn->error; // Debugging purpose
        }
    } else {
        echo "error: Missing or invalid input data";
    }

    $conn->close();
} else {
    echo "error: Invalid request method";
}
?>
