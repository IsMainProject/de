<?php
session_start();
include 'db.php';

// Pagination settings
$records_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch total number of records
$total_sql = "SELECT COUNT(*) as total FROM user_data WHERE genaral_approval=false AND status != 'Refilled'";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch records for the current page
$sql = "SELECT * FROM user_data WHERE genaral_approval=false AND status != 'Refilled' ORDER BY id DESC LIMIT $offset, $records_per_page";
$result = $conn->query($sql);

// Handle sending data to the department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_users'])) {
    $selected_ids = $_POST['selected_users'];
    $ids_list = implode(',', array_map('intval', $selected_ids));
    
    // Update records to 'Pending in Department'
    $update_sql = "UPDATE user_data SET genaral_approval=true, status = 'Pending in Department' WHERE id IN ($ids_list)";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Selected records have been sent to the department.'); window.location.href='status.php';</script>";
    } else {
        echo "Error updating records: " . $conn->error;
    }
}

// Check if the user requested to re-fill the form
if (isset($_GET['refill_id'])) {
    $id = $_GET['refill_id'];
    
    // Update status to 'Refilled'
    $update_sql = "UPDATE user_data SET status = 'Refilled' WHERE id=$id";
    if ($conn->query($update_sql) === TRUE) {
        // Redirect to the form page with the selected ID
        header("Location: form.php?id=$id");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GatePass Status</title>
    <style>
       /* Global Styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Header */
header {
    background-color: #007bff; /* Simple primary color */
    padding: 15px 0;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    width: 100%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

nav {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Simple Button Styles */
nav a {
    background-color: #0069d9; /* Simple blue color */
    padding: 10px 20px;
    border-radius: 4px; /* Subtle rounded corners */
    color: #fff;
    font-weight: 500;
    text-decoration: none;
    margin: 0 10px;
    font-size: 14px;
    transition: background-color 0.2s ease;
}

/* Hover Effect */
nav a:hover {
    background-color: #0056b3; /* Slightly darker on hover */
}

/* Active Link */
nav a.active {
    background-color: #0056b3; /* Same as hover state to keep it simple */
}

/* Mobile Header Styles */
@media (max-width: 768px) {
    nav {
        flex-direction: column;
    }

    nav a {
        margin: 5px 0;
        width: 80%;
        text-align: center;
    }
}

/* Main Container */
main {
    max-width: 95%;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #007bff;
    color: #fff;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

/* Pagination */
.pagination {
    text-align: center;
    margin: 20px 0;
}

.pagination a {
    margin: 0 5px;
    padding: 8px 16px;
    text-decoration: none;
    color: #007bff;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.pagination a.active {
    background-color: #007bff;
    color: #fff;
}

.pagination a:hover {
    background-color: #ddd;
}

/* Button Styles */
button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="form.php">Fill Form</a>
            <a href="profile.php">Profile</a>
            <a href="status.php">GatePass</a>
            <a href="user_home.php">Back</a>
            <a href="index.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>GatePass Request</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <form method="POST" action="status.php">
                <table>
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Name</th>
                            <th>Pass Type</th>
                            <th>Department</th>
                            <th>Mobile Number</th>
                            <th>Date of Birth</th>
                            <th>Father's Name</th>
                            <th>Aadhar No</th>
                            <th>Contractor Name</th>
                            <th>Aadhar Card Document</th>
                            <th>Photo</th>
                            <th>Police Verification</th>
                            <th>Rejected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="selected_users[]" value="<?php echo $row['id']; ?>"></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['pass_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['dob'])); ?></td>
                                <td><?php echo htmlspecialchars($row['father_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['adhar_no']); ?></td>
                                <td><?php echo htmlspecialchars($row['contractor_name']); ?></td>
                                <td><a href="uploads/<?php echo htmlspecialchars($row['adhar_doc']); ?>" target="_blank">View</a></td>
                                <td><a href="uploads/<?php echo htmlspecialchars($row['photo']); ?>" target="_blank">View</a></td>
                                <td><a href="uploads/<?php echo htmlspecialchars($row['police_verification']); ?>" target="_blank">View</a></td>
                                <td>
                                    <?php if ($row['status'] == 'Rejected'): ?>
                                        <a href="status.php?refill_id=<?php echo $row['id']; ?>">Re-fill Form</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <button type="submit">Send to Department</button>
            </form>
        <?php else: ?>
            <p>No data available.</p>
        <?php endif; ?>

        <!-- Pagination Controls -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>

<?php $conn->close(); ?>
