<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Ensure department session variable is set
if (!isset($_SESSION['department'])) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    $department = $_SESSION['department'];

    // Prepare statement to prevent SQL injection
    $sql = "SELECT password FROM admin_users WHERE department = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $department);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify current password
        if ($current_password === $row['password']) {
            // Update password in the database
            $update_sql = "UPDATE admin_users SET password = ? WHERE department = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param('ss', $new_password, $department);

            if ($update_stmt->execute()) {
                // Success message
                $success = "Password updated successfully. You will be logged out.";
                
                // Destroy the session to log out the user
                session_destroy(); 

                // Redirect to login page
                header("Location: admin_login.php");
                exit();
            } else {
                $error = "Error updating password: " . $conn->error;
            }
        } else {
            $error = "Current password is incorrect.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <style>
        /* Basic Reset */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        /* Form Styles */
        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        .error {
            color: #e74c3c;
            margin-top: 10px;
            font-size: 14px;
        }

        .success {
            color: #2ecc71;
            margin-top: 10px;
            font-size: 14px;
        }
        
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Change Password</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div class="form-group">
                <button type="submit">Change Password</button>
            </div>
            <?php if (isset($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>
            <?php if (isset($success)) { ?>
                <div class="success"><?php echo $success; ?></div>
            <?php } ?>
        </form>
    </div>
</body>
</html>
