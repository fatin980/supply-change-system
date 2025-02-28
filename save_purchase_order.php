<?php
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch the last PO number from the database
    $sql = "SELECT po_no FROM p_orders ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $lastPoNumber = "JZ10000000000"; // Default if no record found

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastPoNumber = $row['po_no'];
    }

    // Generate the next PO number
    $po_no = "JZ" . str_pad((intval(str_replace("JZ", "", $lastPoNumber)) + 1), 11, "0", STR_PAD_LEFT);

    // Retrieve other form values
    $quotation_no = $_POST['quotation_no'];
    $project_code = $_POST['project_code'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $payment_terms = $_POST['payment_terms'];
    $unit = $_POST['unit'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];
    $date_created = date("Y-m-d H:i:s"); // Get current timestamp

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO p_orders (po_no, quotation_no, project_code, start_date, end_date, payment_terms, unit, total_price, status, payment_status, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssdsis", $po_no, $quotation_no, $project_code, $start_date, $end_date, $payment_terms, $unit, $total_price, $status, $payment_status, $date_created);

    if ($stmt->execute()) {
        $last_id = $conn->insert_id; // Get inserted ID
        echo json_encode([
            "success" => true,
            "po_no" => $po_no, // Return the generated PO number
            "id" => $last_id,
            "date_created" => $date_created
        ]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
