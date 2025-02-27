<?php
include 'config.php'; // Database connection
include 'header.php';

$query = "SELECT * FROM products ORDER BY product_id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product's Details</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #2e2d2d;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            margin-right: 5px;
        }
        .edit-btn {
            background-color: #4CAF50;
        }
        .delete-btn {
            background-color: #f44336;
        }
        .add-btn {
            display: inline-block;
            margin-bottom: 10px;
            background-color: #007BFF;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .container {
            margin-top: 60px;
            margin-left: 0; /* Default when sidebar is closed */
            padding: 20px;
            transition: margin-left 0.3s ease-in-out; /* Smooth transition */
        }

        .container.shift {
            margin-left: 250px; /* Same width as the sidebar */
        }

        .back-icon {
            text-decoration: none;
            color: black;
            font-size: 20px;
            margin-right: 10px;
        }

        .back-icon:hover {
            color: #007BFF;
        }

        .header-title {
            display: flex;
            align-items: center;
        }

    </style>
</head>
<body>
    <div class="container">
    <h2 class="header-title">
        <a href="products.php" class="back-icon">&#8592;</a> Product's Details
    </h2>

    <table>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Currency</th>
            <th>Status</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['product_id']}</td>
                        <td>{$row['product_name']}</td>
                        <td>{$row['unit_price']}</td>
                        <td>{$row['currency']}</td>
                        <td>{$row['status']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No products found.</td></tr>";
        }
        ?>
    </table>
    </div>
    <?php include 'footer.php';?>
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

<?php
$conn->close();
?>
