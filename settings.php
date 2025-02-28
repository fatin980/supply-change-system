<?php
include ("config.php");

// Fetch settings from the database
$sql = "SELECT setting_key, setting_value FROM settings";
$result = $conn->query($sql);

$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        $value = $conn->real_escape_string($value);
        $sql = "UPDATE settings SET setting_value='$value' WHERE setting_key='$key'";
        $conn->query($sql);
    }

    // Handle logo upload
    if (!empty($_FILES['system_logo']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["system_logo"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
        // Allow only specific file types
        $allowedTypes = ["jpg", "jpeg", "png"];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["system_logo"]["tmp_name"], $targetFilePath)) {
                // Save the path as "uploads/filename" in the database
                $relativePath = $targetDir . $fileName;
                $sql = "UPDATE settings SET setting_value='$relativePath' WHERE setting_key='system_logo'";
                $conn->query($sql);
            }
        }
    }

    echo "<script>alert('System information updated successfully!'); 
    window.location.href = 'settings.php';</script>";
    exit();
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="icon" type="image/x-icon" href="img/Junzo_logo.png">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/page.css">
    <!-- FontAwesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include ("header.php") ?>

    <div class="content">
        <div class="content-container">
            <div class="header-container">
                <h2>System Information</h2>
            </div>
            <div class="form-container">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <label>System Name</label>
                    </div>
                    <div class="form-row">
                        <input type="text" name="system_name" class="form-control" value="<?php echo $settings['system_name']; ?>" required>
                    </div>
                    <div class="form-row">
                        <label>System Short Name</label>
                    </div>
                    <div class="form-row">
                        <input type="text" name="system_short_name" class="form-control" value="<?php echo $settings['system_short_name']; ?>" required>
                    </div>
                    <div class="form-row">
                        <label>Company Name</label>
                    </div>
                    <div class="form-row">
                        <input type="text" name="company_name" class="form-control" value="<?php echo $settings['company_name']; ?>" required>
                    </div>
                    <div class="form-row">
                        <label>Company Email</label>
                    </div>
                    <div class="form-row">
                        <input type="email" name="company_email" class="form-control" value="<?php echo $settings['company_email']; ?>" required>
                    </div>
                    <div class="form-row">
                        <label>Company Address</label>
                    </div>
                    <div class="form-row">
                        <textarea name="company_address" class="form-control" required><?php echo $settings['company_address']; ?></textarea>
                    </div>
                    <div class="form-row">
                        <label>Contact</label>
                    </div>
                    <div class="form-row">
                        <input type="text" name="company_contact" class="form-control" value="<?php echo $settings['company_contact']; ?>" required>
                    </div>
                    <div class="form-row">
                        <label>System Logo</label>
                    </div>
                    <div class="form-row">
                        <input type="file" name="system_logo" class="form-control" accept=".png, .jpg, .jpeg">
                    </div>
                    <div class="form-row">
                        <div class="img-container">
                            <img src="<?php echo $settings['system_logo']; ?>" alt="System Logo">
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <?php include ("footer.php") ?>

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