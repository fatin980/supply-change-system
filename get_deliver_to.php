<?php
include('config.php');

if (isset($_GET['id'])) {
    $deliver_to_id = $_GET['id'];
    $result = $conn->query("SELECT * FROM deliver_to WHERE id = $deliver_to_id");

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Not found"]);
    }
}
?>