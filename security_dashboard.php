<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Initialize an array to store error messages
$errorMessages = [];

// Handle form submission for approval or rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['selected_users']) || empty($_POST['selected_users'])) {
        $errorMessages[] = 'Please select at least one record.';
    }

    if (empty($errorMessages)) {
        $action = $_POST['action'];
        $ids = $_POST['selected_users'];

        // Sanitize IDs for SQL
        $ids = array_map('intval', $ids); // Convert all IDs to integers
        $ids_list = implode(',', $ids);

        if ($action == 'approve') {
            // Check for permanent pass validation
            foreach ($ids as $id) {
                $passno = $_POST['passno'][$id] ?? null;
                $pass_type = $_POST['pass_type'][$id] ?? ''; // Ensure pass_type is sent from form

                if ($pass_type == 'Permanent') {
                    if (empty($passno)) {
                        $errorMessages[] = "Pass number is required for Permanent Pass.";
                    } else {
                        // Check for unique passno
                        $passno = $conn->real_escape_string($passno);
                        $checkPassNoSql = "SELECT id FROM user_data WHERE passno='$passno' AND id != $id";
                        $checkPassNoResult = $conn->query($checkPassNoSql);

                        if ($checkPassNoResult->num_rows > 0) {
                            $errorMessages[] = "Pass number is already in used.";
                        }
                    }
                }
            }

            // If any errors, show them on the page
            if (!empty($errorMessages)) {
                $errorMessage = implode("<br>", $errorMessages);
                echo "<div class='error'>$errorMessage</div>";
            } else {
                // If no errors, update the records
                $sql = "UPDATE user_data SET security_approval=true, status = 'Approved by Security' WHERE id IN ($ids_list)";
                
                if ($conn->query($sql) === TRUE) {
                    // Handle updating pass numbers if provided and no errors occurred
                    if (isset($_POST['passno'])) {
                        foreach ($_POST['passno'] as $id => $passno) {
                            $id = intval($id); // Sanitize ID
                            $passno = $conn->real_escape_string($passno); // Sanitize pass number
                            if (!empty($passno)) { // Only update passno if it's not empty
                                $sql = "UPDATE user_data SET passno='$passno' WHERE id=$id";
                                $conn->query($sql);
                            }
                        }
                    }
                    header("Location: security_dashboard.php");
                    exit();
                } else {
                    $errorMessages[] = "Error updating record: " . $conn->error;
                    echo "<div class='error'>" . implode("<br>", $errorMessages) . "</div>";
                }
            }
        } elseif ($action == 'reject') {
            // Reject the records
            $sql = "UPDATE user_data SET genaral_approval=false, department_approval=false, status = 'Rejected' WHERE id IN ($ids_list)";
            
            if ($conn->query($sql) === TRUE) {
                header("Location: security_dashboard.php");
                exit();
            } else {
                $errorMessages[] = "Error updating record: " . $conn->error;
                echo "<div class='error'>" . implode("<br>", $errorMessages) . "</div>";
            }
        }
    } else {
        echo "<div class='error'>" . implode("<br>", $errorMessages) . "</div>";
    }
}

// SQL query to fetch records approved by departments
$sql = "SELECT * FROM user_data WHERE genaral_approval=true AND department_approval=true AND (eshead_approval=true OR pass_type ='Permanent') AND security_approval=false";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
    /* Change Password button styling */
    .change-password-btn {
        padding: 8px 16px;
        background-color: grey;
        color: white;
        text-decoration: none;
        margin-left: 650px;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        border: 1px solid #28a745;
        transition: background-color 0.3s ease, border 0.3s ease;
    }

    .change-password-btn:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    /* Table container styling (unchanged) */
    .table-container {
        overflow-x: auto;
        margin-top: 40px;
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
</style>
    <script>
        function validateForm() {
            const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
            const checked = Array.from(checkboxes).some(checkbox => checkbox.checked);
            if (!checked) {
                alert('Please select at least one record.');
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <div class="container">
        <header>
            <h2>Security Dashboard</h2>
            <a href="change_password.php"class="change-password-btn" >Change Password</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>
        <form method="POST" action="security_dashboard.php" onsubmit="return validateForm();">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <!-- <th>ID</th> -->
                        <th>Name</th>
                        <th>Pass Type</th>
                        <th>Pass No</th>
                        <th>Department</th>
                        <th>Mobile</th>
                        <th>DOB</th>
                        <th>Father's Name</th>
                        <th>Aadhar No</th>
                        <th>Local Address</th>
                        <th>Aadhar Address</th>
                        <th>Contractor Name</th>
                        <th>Contractor Address</th>
                        <th>Aadhar Card Document</th>
                        <th>Photo</th>
                        <th>Police Verification</th>
                        <th>Issue Date</th>
                        <th>Valid Date</th>
                        <th>Dep.Approval</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            
            // Checkbox to select the user
            echo "<td><input type='checkbox' name='selected_users[]' value='" . htmlspecialchars($row['id']) . "'></td>";

            // User ID
            // echo "<td>" . htmlspecialchars($row['id']) . "</td>";

            // User Name
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";

            // Display pass_type as a hidden field
            echo "<td><input type='hidden' name='pass_type[" . htmlspecialchars($row['id']) . "]' value='" . htmlspecialchars($row['pass_type']) . "'>" . htmlspecialchars($row['pass_type']) . "</td>";

            // Only display passno if pass_type is 'Permanent'
            if (strtolower($row['pass_type']) == 'permanent') {
                $passno_value = (!empty($row['passno']) && $row['passno'] !== 'null') ? htmlspecialchars($row['passno'], ENT_QUOTES) : '';
                echo "<td>
                        <input type='text' 
                               name='passno[" . htmlspecialchars($row['id']) . "]' 
                               value='$passno_value' 
                               placeholder='Enter pass no' 
                               style='width: 100px; padding: 5px; border: 1px solid #ccc; border-radius: 5px;'>
                      </td>";
            } else {
                // If pass_type is not 'Permanent', leave the passno column empty
                echo "<td></td>";
            }

            // Other user details
            echo "<td>" . htmlspecialchars($row['department']) . "</td>";
            echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
            echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
            echo "<td>" . htmlspecialchars($row['father_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['adhar_no']) . "</td>";
            echo "<td>" . htmlspecialchars($row['local_address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['adhar_address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['contractor_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['contractor_address']) . "</td>";

            // Aadhar Document, Photo, and Police Verification links
            echo "<td><a href='uploads/" . htmlspecialchars($row['adhar_doc']) . "' target='_blank'>View</a></td>";
            echo "<td><a href='uploads/" . htmlspecialchars($row['photo']) . "' target='_blank'>View</a></td>";
            echo "<td><a href='uploads/" . htmlspecialchars($row['police_verification']) . "' target='_blank'>View</a></td>";

            // Issue Date and Valid Date fields
            echo "<td><input type='date' name='issue_date[" . htmlspecialchars($row['id']) . "]' value='" . htmlspecialchars($row['issue_date']) . "'></td>";
            echo "<td><input type='date' name='valid_date[" . htmlspecialchars($row['id']) . "]' value='" . htmlspecialchars($row['valid_date']) . "'></td>";

            // Department approval and Status
            echo "<td>" . htmlspecialchars($row['approval_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";

            echo "</tr>";
        }
    } else {
        // If no records are found
        echo "<tr><td colspan='19'>No data available</td></tr>";
    }
    ?>
                </tbody>
            </table>
            <br>
            <button type="submit" name="action" value="approve">Approve</button>
            <button type="submit" name="action" value="reject">Reject</button>
</div>
        </form>
    </div>
</body>
</html>
