<?php
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shipping_id = $_POST['shipping_id'];
    $requisitioners = $_POST['requisitioners'];
    $shipping_terms = $_POST['shipping_terms'];
    $deliver_via = $_POST['deliver_via'];
    $status = $_POST['status'];

    // Update shipping term
    $stmt = $conn->prepare("UPDATE shipping_terms SET requisitioners=?, shipping_terms=?, deliver_via=?, status=? WHERE shipping_id=?");
    $stmt->bind_param("ssssi", $requisitioners, $shipping_terms, $deliver_via, $status, $shipping_id);

    if ($stmt->execute()) {
        header("Location: shipping_list.php?success=1");
        exit();
    } else {
        echo "Error updating record.";
    }

    $stmt->close();
    $conn->close();
}
?>
