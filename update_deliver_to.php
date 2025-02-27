<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE deliver_to SET 
              name='$name', 
              company_name='$company', 
              address='$address', 
              city='$city', 
              zip='$zip', 
              contact='$contact', 
              status='$status' 
              WHERE id='$id'";

    if ($conn->query($query)) {
        echo "Delivery details updated successfully!";
    } else {
        echo "Error updating delivery details: " . $conn->error;
    }
}
?>