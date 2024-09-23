<?php
session_start();
include 'db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['department'] !== 'ESHead') {
    header('Location: admin_login.php');
    exit();
}

// SQL query to fetch records approved by departments with pass type Temporary or Overtime
$sql = "SELECT * FROM user_data WHERE genaral_approval=true and department_approval=true and eshead_approval=false AND pass_type IN ('Temporary')";
$result = $conn->query($sql);

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
        // Move records to Security stage
        $sql = "UPDATE user_data SET eshead_approval=true,status = 'Approved By EsHead' WHERE id IN ($ids_list)";
    } elseif ($action == 'reject') {
        // Reject the records
        $sql = "UPDATE user_data SET genaral_approval=false and department_approval=false WHERE id IN ($ids_list)";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: eshead_dashboard.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>ESHead Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>.table-container {
    overflow-x: auto;
    margin-top: 50px;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
                } 
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
            <h2>ESHead Dashboard</h2>
            <a href="change_password.php" class="change-password-btn">Change Password</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>
        <form method="POST" action="eshead_dashboard.php" onsubmit="return validateForm();">
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
            echo "<td><a href='uploads/" . $row['adhar_doc'] . "' target='_blank'>View</a></td>";
            echo "<td><a href='uploads/" . $row['photo'] . "' target='_blank'>View</a></td>";
            echo "<td><a href='uploads/" . $row['police_verification'] . "' target='_blank'>View</a></td>";
            echo "<td><input type='date' name='issue_date[" . $row['id'] . "]' value='" . htmlspecialchars($row['issue_date']) . "'></td>";
            echo "<td><input type='date' name='valid_date[" . $row['id'] . "]' value='" . htmlspecialchars($row['valid_date']) . "'></td>";
            echo "<td>" . htmlspecialchars($row['approval_name']) . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='16'>No data available.</td></tr>";
    }
    ?>
</tbody>
            </table>
            <br>
            <button type="submit" name="action" value="approve">Approve</button>
            <button type="submit" name="action" value="reject">Reject</button>
    </DIV>
        </form>
    </div>
</body>
</html>
