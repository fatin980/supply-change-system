<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "INSERT INTO suppliers (supplier, contact_person, contact_number, email, address, status, date_created) 
            VALUES ('$supplier', '$contact_person', '$contact_number', '$email', '$address', '$status', NOW())";

    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "Error: " . $conn->error;  // Debugging output
    }
}
?>
