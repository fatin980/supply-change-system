<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $supplier = $_POST['supplier'];
    $contact_person = $_POST['contact_person'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    $sql = "UPDATE suppliers SET 
            supplier = '$supplier',
            contact_person = '$contact_person',
            contact_number = '$contact_number',
            email = '$email',
            address = '$address',
            status = '$status'
            WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }

    $conn->close();
}
?>