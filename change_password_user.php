<?php
session_start();

if (!isset($_SESSION['stored_password'])) {
    $_SESSION['stored_password'] = "Bel@123"; // Initial password
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($old_password == $_SESSION['stored_password'] && $new_password == $confirm_password) {
            $_SESSION['stored_password'] = $new_password;
            $_SESSION['loggedin'] = true;
            $_SESSION['message'] = "Password changed successfully.";
            header("Location: user_home.php");
            exit();
        } else {
            $error = "Invalid details or passwords do not match.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        /* Basic Reset */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        /* Modal Styles */
        .modal {
            display: block; /* Show by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
    <div id="changePasswordModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>

            <!-- Change Password Form -->
            <form method="post" id="changePasswordForm">
                <h2>Change Password</h2>
                <div class="form-group">
                    <label for="old_password">Old Password:</label>
                    <input type="password" id="old_password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="change_password">Change Password</button>
                </div>
                <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
                <?php if (!empty($_SESSION['message'])) { echo "<p class='success'>{$_SESSION['message']}</p>"; unset($_SESSION['message']); } ?>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById("changePasswordModal");
        var closeModal = document.getElementById("closeModal");

        closeModal.onclick = function() {
            window.location.href = 'user_home.php';
        };

        window.onclick = function(event) {
            if (event.target === modal) {
                window.location.href = 'user_home.php';
            }
        };
    </script>
</body>
</html>
