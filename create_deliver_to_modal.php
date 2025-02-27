<?php
include("config.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['name']);
    $new_company_name = trim($_POST['company_name']);
    $new_address = trim($_POST['address']);
    $new_city = trim($_POST['city']);
    $new_zip = trim($_POST['zip']);
    $new_contact = trim($_POST['contact']);
    $new_status = $_POST['status'];

    if (empty($new_name) || empty($new_company_name) || empty($new_address) || empty($new_city) || empty($new_zip) || empty($new_contact)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        $query = "INSERT INTO deliver_to (name, company_name, address, city, zip, contact, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("sssssss", $new_name, $new_company_name, $new_address, $new_city, $new_zip, $new_contact, $new_status);
            
            if ($stmt->execute()) {
                echo "<script>alert('New deliver to created successfully!'); 
                window.location.href = 'deliver_to.php';</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }
            
            $stmt->close();
        } else {
            echo "<script>alert('Database error!');</script>";
        }
    }
}

$conn->close();
?>
<html>
<head>
</head>
<body>
    <!-- Create Deliver To Modal -->
    <div id="createDeliverTo" class="modal" data-modal="createDeliverTo">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('createDeliverTo')">&times;</span>
            <h3>Create New Deliver To</h3>
            <form action="" method="POST">
                <label>Name:</label>
                <input type="text" name="name" required>

                <label>Company:</label>
                <input type="text" name="company_name" required>

                <label>Address:</label>
                <input type="text" name="address" required>

                <label>City:</label>
                <input type="text" name="city" required>

                <label>Zip:</label>
                <input type="text" name="zip" required>

                <label>Contact:</label>
                <input type="text" name="contact" required>

                <label>Status:</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>