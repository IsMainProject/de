<?php
session_start();
include 'db.php';

$selected_department = ''; // Initialize selected department variable
$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department = $_POST['department'];
    $password = $_POST['password'];
    $selected_department = $department; // Store the selected department

    // Prepare statement to prevent SQL injection
    $sql = "SELECT password FROM admin_users WHERE department = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $department);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Trim and convert both passwords to lowercase for comparison
        if (strtolower(trim($password)) === strtolower(trim($row['password']))) {
            $_SESSION['department'] = $department;
            $_SESSION['is_logged_in'] = true;

            // Convert the department to lowercase for consistent comparison
            $lowercase_department = strtolower($department);
            if ($department === 'ESHead') {
                header("Location: eshead_dashboard.php");
            } elseif (in_array($lowercase_department, ['security', 'security2'])) {
                header("Location: {$lowercase_department}_dashboard.php");
            } else {
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: relative;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 15px;
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

        .form-group input, .form-group select {
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
    </style>
</head>
<body>
    <div class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Admin Login</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="department">Department:</label>
                    <select name="department" id="department" required>
                        <option value="Air Conditioning" <?php if ($selected_department == 'Air Conditioning') echo 'selected'; ?>>Air Conditioning</option>
                        <option value="Mechanical" <?php if ($selected_department == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
                        <option value="Electrical" <?php if ($selected_department == 'Electrical') echo 'selected'; ?>>Electrical</option>
                        <option value="Water Treatment Plant" <?php if ($selected_department == 'Water Treatment Plant') echo 'selected'; ?>>Water Treatment Plant</option>
                        <option value="Contracts" <?php if ($selected_department == 'Contracts') echo 'selected'; ?>>Contracts</option>
                        <option value="MM" <?php if ($selected_department == 'MM') echo 'selected'; ?>>MM</option>
                        <option value="Horticulture" <?php echo isset($user['department']) && $user['department'] == 'Horticulture' ? 'selected' : ''; ?>>Horticulture</option>
                        <option value="HouseKeeping" <?php echo isset($user['department']) && $user['department'] == 'HouseKeeping' ? 'selected' : ''; ?>>HouseKeeping</option>
                        <option value="Transport" <?php if ($selected_department == 'Transport') echo 'selected'; ?>>Transport</option>
                        <option value="Civil Factory Maintenance" <?php if ($selected_department == 'Civil Factory Maintenance') echo 'selected'; ?>>Civil Factory Maintenance</option>
                        <option value="Estate" <?php if ($selected_department == 'Estate') echo 'selected'; ?>>Estate</option>
                        <option value="PMG" <?php if ($selected_department == 'PMG') echo 'selected'; ?>>PMG</option>
                        <option value="ESHead" <?php if ($selected_department == 'ESHead') echo 'selected'; ?>>ESHead</option>
                        <option value="Security" <?php if ($selected_department == 'Security') echo 'selected'; ?>>Security</option>
                        <option value="Security2" <?php if ($selected_department == 'Security2') echo 'selected'; ?>>Security Head</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
                <?php if (!empty($error)) { ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>
            </form>
        </div>
    </div>
    <script>
        // Get the <span> element that closes the modal
        var span = document.querySelector('.close');

        // When the user clicks on <span> (x), redirect to index.php
        span.onclick = function() {
            window.location.href = 'index.php';
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            var modal = document.querySelector('.modal');
            if (event.target == modal) {
                modal.style.display = 'index.php';
            }
        }
    </script>
</body>
</html>
