<?php
session_start();
include('config.php');

// Get the deliver_to ID from the URL
if (isset($_GET['id'])) {
    $deliver_to_id = $_GET['id'];

    // Fetch delivery details from the database
    $result = $conn->query("SELECT * FROM deliver_to WHERE id = $deliver_to_id");

    if ($result->num_rows > 0) {
        $delivery = $result->fetch_assoc();
    } else {
        echo "Delivery ID not found!";
        exit;
    }
} else {
    echo "Invalid delivery ID!";
    exit;
}
?>

<!-- Delivery Details Modal -->
<span class="close-btn" onclick="closeModal('viewDeliverToModal')">&times;</span>
<div class="form-header-container">
    <h3>Delivery Details</h3>
</div>
<div class="form-container">
<div class="form-row">
    <div class="form-field">
        <strong>Name</strong>
    </div>
    <div class="form-field">
        : <?php echo $delivery['name']; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-field">
        <strong>Company</strong>
    </div>
    <div class="form-field">
        : <?php echo $delivery['company_name']; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-field">
        <strong>Address</strong>
    </div>
    <div class="form-field">
        : <?php echo $delivery['address']; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-field">
        <strong>City</strong>
    </div>
    <div class="form-field">
        : <?php echo $delivery['city']; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-field">
        <strong>Zip</strong>
    </div>
    <div class="form-field">
        : <?php echo $delivery['zip']; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-field">
        <strong>Contact</strong>
    </div>
    <div class="form-field">
        : <?php echo $delivery['contact']; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-field">
        <strong>Status</strong>
    </div>
    <div class="form-field">
        : <?php switch ($delivery['status']) {
        case 'Active':
            echo '<button style="background-color: #28a745; color: white; border: none; padding: 3px 6px; border-radius: 8px; cursor: pointer; font-weight: bold;">Active</button>';
            break;
        case 'Inactive':
            echo '<button style="background-color: #aaaaaa; color: white; border: none; padding: 3px 6px; border-radius: 8px; cursor: pointer; font-weight: bold;">Inactive</button>';
            break;
        } ?>
    </div>
</div>
</div>