<?php
include 'config.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // Debug: Print JSON response
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    } else {
        echo json_encode(null);
    }

    $stmt->close();
    $conn->close();
}
?>