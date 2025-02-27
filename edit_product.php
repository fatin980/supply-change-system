<?php
include 'config.php'; // Database connection
include 'header.php';

// Check if product_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Product ID.");
}

$product_id = $_GET['id'];

// Fetch existing product data
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<style>
    .container {
        margin-top: 60px;
        padding: 20px;
    }

    /* Two-column layout */
    .form-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .form-column {
        flex: 1; /* Equal width */
        min-width: 250px; /* Ensures responsiveness */
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    input, select, textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
        height: 80px;
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
    <h2>Edit Product</h2>

    <form action="update_product.php" method="post">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

        <div class="form-container">
            <!-- Left Column -->
            <div class="form-column">
                <div class="form-group">
                    <label>Product Name:</label>
                    <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" required><?php echo $product['description']; ?></textarea>
                </div>
            </div>

            <!-- Right Column -->
            <div class="form-column">
                <div class="form-group">
                    <label>Unit Price:</label>
                    <input type="number" name="unit_price" value="<?php echo $product['unit_price']; ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Currency:</label>
                    <select name="currency" required>
                        <option value="RM" <?php if ($product['currency'] == "RM") echo "selected"; ?>>RM</option>
                        <option value="USD" <?php if ($product['currency'] == "USD") echo "selected"; ?>>USD</option>
                        <option value="INR" <?php if ($product['currency'] == "INR") echo "selected"; ?>>INR</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" required>
                        <option value="active" <?php if ($product['status'] == "active") echo "selected"; ?>>Active</option>
                        <option value="inactive" <?php if ($product['status'] == "inactive") echo "selected"; ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
