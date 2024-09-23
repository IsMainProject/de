<?php
session_start();
include 'db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['selected_users']) || empty($_POST['selected_users'])) {
        echo "<script>alert('Please select at least one record.'); window.history.back();</script>";
        exit();
    }

    $action = $_POST['action'];
    $ids = $_POST['selected_users'];

    // Sanitize IDs for SQL
    $ids = array_map('intval', $ids); // Convert all IDs to integers
    $ids_list = implode(',', $ids);

    if ($action == 'approve') {
        // Update status to Approved by SecurityHead
        $sql = "UPDATE user_data SET securityhead_approval=true, status = 'Approved by SecurityHead' WHERE id IN ($ids_list)";
    } elseif ($action == 'reject') {
        // Reject the records
        $sql = "UPDATE user_data SET genaral_approval=false, department_approval=false, security_approval=false, status = 'Rejected' WHERE id IN ($ids_list)";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: security2_dashboard.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// SQL query to fetch records approved by SecurityHead
$sql = "SELECT * FROM user_data WHERE genaral_approval=true AND department_approval=true AND security_approval=true AND securityhead_approval=false";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Security Head Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
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
            <h2>Security Head Dashboard</h2>
            <a href="change_password.php"class="change-password-btn" >Change Password</a>
            <a href="logout.php" class="logout-btn">Logout</a>
            <style>
    /* Change Password button styling */
    .change-password-btn {
        padding: 8px 16px;
        background-color: grey;
        color: white;
        text-decoration: none;
        margin-left: 600px;
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
        </header>
        <form method="POST" action="security2_dashboard.php" onsubmit="return validateForm();">
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
                        <th>Issue Date</th>
                        <th>Valid Date</th>
                        <th>Aadhar Card Document</th>
                        <th>Photo</th>
                        <th>Police Verification</th>
                        <th>Dep.Approval</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='selected_users[]' value='" . $row['id'] . "'></td>";
                        // echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['pass_type'] . "</td>";
                        echo "<td>" . $row['passno'] . "</td>";
                        echo "<td>" . $row['department'] . "</td>";
                        echo "<td>" . $row['mobile'] . "</td>";
                        echo "<td>" . $row['dob'] . "</td>";
                        echo "<td>" . $row['father_name'] . "</td>";
                        echo "<td>" . $row['adhar_no'] . "</td>";
                        echo "<td>" . $row['local_address'] . "</td>";
                        echo "<td>" . $row['adhar_address'] . "</td>";
                        echo "<td>" . $row['contractor_name'] . "</td>";
                        echo "<td>" . $row['contractor_address'] . "</td>";
                        echo "<td>" . $row['issue_date'] . "</td>"; // Display issue_date
                        echo "<td>" . $row['valid_date'] . "</td>"; // Display Valid Date
                        echo "<td><a href='uploads/" . $row['adhar_doc'] . "' target='_blank'>View</a></td>";
                        echo "<td><a href='uploads/" . $row['photo'] . "' target='_blank'>View</a></td>";
                        echo "<td><a href='uploads/" . $row['police_verification'] . "' target='_blank'>View</a></td>";
                        echo "<td>" . htmlspecialchars($row['approval_name']) . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='19'>No data available.</td></tr>";
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
