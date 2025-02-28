<?php
include 'config.php'; // Database connection
include 'header.php';

// Check if shipping_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Shipping ID.");
}

$shipping_id = $_GET['id'];

// Fetch existing shipping data
$query = "SELECT * FROM shipping_terms WHERE shipping_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $shipping_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Shipping term not found.");
}

$shipping = $result->fetch_assoc();
?>

<style>
    .container {
        margin-top: 60px;
        padding: 20px;
    }

    .form-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .form-column {
        flex: 1;
        min-width: 250px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    input, select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .button-group {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }

    .btn {
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        width: 120px;
        text-align: center;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }
</style>

<body>

<div class="container">
    <h2>Edit Shipping Term</h2>

    <form action="update_shipping.php" method="post">
        <input type="hidden" name="shipping_id" value="<?php echo $shipping['shipping_id']; ?>">

        <div class="form-container">
            <!-- Left Column -->
            <div class="form-column">
                <div class="form-group">
                    <label>Requisitioners:</label>
                    <input type="text" name="requisitioners" value="<?php echo $shipping['requisitioners']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Shipping Terms:</label>
                    <select name="shipping_terms" required>
                        <option value="FOB" <?php if ($shipping['shipping_terms'] == 'FOB') echo 'selected'; ?>>FOB</option>
                        <option value="CIF" <?php if ($shipping['shipping_terms'] == 'CIF') echo 'selected'; ?>>CIF</option>
                        <option value="NET 30" <?php if ($shipping['shipping_terms'] == 'NET 30') echo 'selected'; ?>>NET 30</option>
                        <option value="NET 14" <?php if ($shipping['shipping_terms'] == 'NET 14') echo 'selected'; ?>>NET 14</option>
                        <option value="NET 60" <?php if ($shipping['shipping_terms'] == 'NET 60') echo 'selected'; ?>>NET 60</option>
                    </select>
                </div>
            </div>

            <!-- Right Column -->
            <div class="form-column">
                <div class="form-group">
                    <label>Delivery Method:</label>
                    <select name="deliver_via" required>
                        <option value="Virtual Live Classroom" <?php if ($shipping['deliver_via'] == 'Virtual Live Classroom') echo 'selected'; ?>>Virtual Live Classroom</option>
                        <option value="Face to Face" <?php if ($shipping['deliver_via'] == 'Face to Face') echo 'selected'; ?>>Face to Face</option>
                        <option value="Hybrid" <?php if ($shipping['deliver_via'] == 'Hybrid') echo 'selected'; ?>>Hybrid</option>
                        <option value="E-Learning" <?php if ($shipping['deliver_via'] == 'E-Learning') echo 'selected'; ?>>E-Learning</option>
                        <option value="Certification" <?php if ($shipping['deliver_via'] == 'Certification') echo 'selected'; ?>>Certification</option>
                        <option value="Onsite Client Location" <?php if ($shipping['deliver_via'] == 'Onsite Client Location') echo 'selected'; ?>>Onsite Client Location</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" required>
                        <option value="Active" <?php if ($shipping['status'] == 'Active') echo 'selected'; ?>>Active</option>
                        <option value="Inactive" <?php if ($shipping['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="shipping_terms.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
<script>
        function toggleSidebar() {
            let sidebar = document.querySelector('.sidebar');
            let content = document.querySelector('.content');
            sidebar.classList.toggle('open');
            content.classList.toggle('shift');
        }
    </script>

</body>
</html>
