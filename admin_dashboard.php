<?php
session_start();
include 'db.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

$department = $_SESSION['department'];

// SQL query to fetch records for the logged-in department
$sql = "SELECT id, name, pass_type, passno, mobile, dob, father_name, adhar_no, local_address, adhar_address, contractor_name, contractor_address, adhar_doc, photo, police_verification, issue_date, valid_date, approval_name, status FROM user_data WHERE genaral_approval = true AND department_approval = false AND department = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $department); // Assuming department is a string
$stmt->execute();
$result = $stmt->get_result();

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
        // Prepare arrays for issue_date, valid_date, and approval_name
        $issue_dates = $_POST['issue_date'];
        $valid_dates = $_POST['valid_date'];
        $approval_names = $_POST['approval_name'];

        // Ensure all selected users have valid issue_date, valid_date, and approval_name
        foreach ($ids as $id) {
            if (empty($issue_dates[$id]) || empty($valid_dates[$id]) || empty($approval_names[$id])) {
                echo "<script>alert('Please ensure all selected records have Issue Date, Valid Date, and Approval Name.'); window.history.back();</script>";
                exit();
            }
        }

        // Update the selected records with new issue_date, valid_date, and approval status
        foreach ($ids as $id) {
            $issue_date = $issue_dates[$id];
            $valid_date = $valid_dates[$id];
            $approval_name = $approval_names[$id];

            // Update query to approve the selected users
            $sql = "UPDATE user_data SET department_approval=true, issue_date=?, valid_date=?, approval_name=?, status='Approved by Department' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $issue_date, $valid_date, $approval_name, $id); // Bind the issue, valid dates, approval name, and user ID
            $stmt->execute();
        }

    } elseif ($action == 'reject') {
        // Reject the records
        $sql = "UPDATE user_data SET genaral_approval = false, status = 'Rejected' WHERE id IN ($ids_list)";
        $conn->query($sql);
    }

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>.table-container {
    overflow-x: auto;
    margin-top: 50px;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}</style>
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
            <h2>Welcome, <?php echo htmlspecialchars($department); ?> Department</h2>
            <nav>
                <a href="change_password.php" style="background-color: grey; padding: 10px 20px; color: white; border-radius: 5px; text-decoration: none; font-weight: bold; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); transition: background-color 0.3s, box-shadow 0.3s;">Change Password</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </header>
        <form method="POST" action="admin_dashboard.php" onsubmit="return validateForm();">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <!-- <th>ID</th> -->
                        <th>Name</th>
                        <th>Pass Type</th>
                        <th>Pass No</th>
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

                            // Editable issue_date, valid_date, and approval_name fields
                            echo "<td><input type='date' name='issue_date[" . $row['id'] . "]' value='" . $row['issue_date'] . "'></td>";
                            echo "<td><input type='date' name='valid_date[" . $row['id'] . "]' value='" . $row['valid_date'] . "'></td>";
                            echo "<td><input type='text' name='approval_name[" . $row['id'] . "]' value='" . $row['approval_name'] . "'></td>";

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
