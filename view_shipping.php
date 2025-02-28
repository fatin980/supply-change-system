<?php
include 'config.php'; // Database connection
include 'header.php';

$query = "SELECT * FROM shipping_terms ORDER BY shipping_id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Details</title>
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
            color: white;
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
            <a href="shipping_terms.php" class="back-icon">&#8592;</a> Shipping Details
        </h2>

        <table>
            <tr>
                <th>Shipping ID</th>
                <th>Requisitioners</th>
                <th>Shipping Terms</th>
                <th>Delivery Method</th>
                <th>Status</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['shipping_id']}</td>
                            <td>{$row['requisitioners']}</td>
                            <td>{$row['shipping_terms']}</td>
                            <td>{$row['deliver_via']}</td>
                            <td>{$row['status']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No shipping details found.</td></tr>";
            }
            ?>
        </table>
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

<?php
$conn->close();
?>
