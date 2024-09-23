<?php
include 'db.php';

// Pagination settings
$records_per_page = 9; // Number of records to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $records_per_page; // Offset for SQL query

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = '';

if ($search) {
    $search = $conn->real_escape_string($search);
    $search_condition = "WHERE name LIKE '%$search%' OR department LIKE '%$search%' OR mobile LIKE '%$search%' OR father_name LIKE '%$search%' OR adhar_no LIKE '%$search%'";
}

// Fetch total number of records
$total_sql = "SELECT COUNT(*) as total FROM user_data $search_condition";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch records for the current page
$sql = "SELECT * FROM user_data $search_condition ORDER BY id DESC LIMIT $offset, $records_per_page";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $users = [];
}

// Function to determine status
function getStatus($user) {
    if ($user['securityhead_approval']) {
        return 'Completed'; // All approvals are done, including SecurityHead
    } elseif ($user['security_approval'] && !$user['securityhead_approval']) {
        return 'Approved by Security'; // Security approval is done, but SecurityHead is pending
    } elseif ($user['eshead_approval'] && !$user['security_approval']) {
        return 'Approved by EsHead'; // EsHead approval is done, but Security is pending
    } elseif ($user['department_approval'] && !$user['eshead_approval']) {
        return 'Approved by Department'; // Department approval is done, but EsHead is pending
    } elseif ($user['genaral_approval'] && !$user['department_approval']) {
        return 'Pending Department Approval'; // General approval is done, but Department is pending
    } else {
        return 'Pending General Approval'; // No approvals are done yet
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Include your styles here -->
    <style>
/* Global Styles */
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f5f7fa;
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
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Flexbox for Inline Heading and Search */
.header-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 10px 0;
}

.header-flex h2 {
    font-size: 28px;
    margin: 0;
    color: #007bff;
    font-weight: 600;
}

/* Search Form */
.search-form {
    display: flex;
    align-items: center;
}

.search-form input[type="text"] {
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px 0 0 5px;
    width: 250px;
    font-size: 16px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.search-form input[type="text"]:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    outline: none;
}

.search-form button {
    padding: 10px 20px;
    border: none;
    background-color: #007bff;
    color: #fff;
    border-radius: 0 5px 5px 0;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s, box-shadow 0.3s;
}

.search-form button:hover {
    background-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

/* Scrollable Table Container */
.table-container {
    overflow-x: auto;
    margin-top: 20px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px; /* Prevent squashing on smaller screens */
    background: #fff;
}

th, td {
    padding: 12px;
    border: 1px solid #dee2e6;
    text-align: left;
    white-space: nowrap;
}

th {
    background-color: #007bff;
    color: #fff;
    font-size: 16px;
}

td {
    font-size: 15px;
}

tr:nth-child(even) {
    background-color: #f8f9fa;
}

tr:hover {
    background-color: #e2e6ea;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
}

.pagination a {
    margin: 0 5px;
    padding: 10px 20px;
    text-decoration: none;
    color: #007bff;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s, color 0.3s;
}

.pagination a.active {
    background-color: #007bff;
    color: #fff;
}

.pagination a:hover {
    background-color: #e9ecef;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-flex {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-flex h2 {
        margin-bottom: 10px;
    }

    .search-form {
        width: 100%;
    }

    .search-form input[type="text"] {
        width: 100%;
    }

    .search-form button {
        width: 100%;
        margin-top: 5px;
    }

    table {
        font-size: 14px;
    }

    .pagination a {
        padding: 8px 12px;
        font-size: 14px;
    }
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
        <!-- Flexbox Header -->
        <div class="header-flex">
            <h2>Profile</h2>
            <div class="search-form">
                <form method="get" action="profile.php">
                    <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Pass Type</th>
                        <th>Mobile Number</th>
                        <th>Date of Birth</th>
                        <th>Father's Name</th>
                        <th>Aadhar No</th>
                        <th>Local Address</th>
                        <th>Aadhar Address</th>
                        <th>Name of Contractor</th>
                        <th>Address of Contractor</th>
                        <th>Aadhar Card Document</th>
                        <th>Photo</th>
                        <th>Police Verification</th>
                        <th>Issue Date</th>  
                        <th>Valid Date</th> 
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Pass Number</th> <!-- New Pass Number Column -->
                        <th>Download Pass</th> <!-- New Download Pass Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
    <td><?php echo htmlspecialchars($user['name']); ?></td>
    <td><?php echo htmlspecialchars($user['department']); ?></td>
    <td><?php echo htmlspecialchars($user['pass_type']); ?></td>
    <td><?php echo htmlspecialchars($user['mobile']); ?></td>
    <td><?php echo htmlspecialchars($user['dob']); ?></td>
    <td><?php echo htmlspecialchars($user['father_name']); ?></td>
    <td><?php echo htmlspecialchars($user['adhar_no']); ?></td>
    <td><?php echo htmlspecialchars($user['local_address']); ?></td>
    <td><?php echo htmlspecialchars($user['adhar_address']); ?></td>
    <td><?php echo htmlspecialchars($user['contractor_name']); ?></td>
    <td><?php echo htmlspecialchars($user['contractor_address']); ?></td>
    <td><a href="uploads/<?php echo htmlspecialchars($user['adhar_doc']); ?>" target="_blank">View</a></td>
    <td><a href="uploads/<?php echo htmlspecialchars($user['photo']); ?>" target="_blank">View</a></td>
    <td><a href="uploads/<?php echo htmlspecialchars($user['police_verification']); ?>" target="_blank">View</a></td>
    <td><?php echo htmlspecialchars($user['issue_date']); ?></td>
    <td><?php echo htmlspecialchars($user['valid_date']); ?></td>
    <td><?php echo getStatus($user); ?></td>
    <td><a href="edit_profile.php?id=<?php echo htmlspecialchars($user['id']); ?>">Edit</a></td>
    <td>
        <?php if ($user['securityhead_approval']): ?>
            <?php echo htmlspecialchars($user['passno']); ?>
        <?php else: ?>
            Not Available
        <?php endif; ?>
    </td>
    <td>
        <?php if (getStatus($user) == 'Completed'): ?>
            <a href="download_pass.php?id=<?php echo htmlspecialchars($user['id']); ?>" target="_blank">Download Pass</a>
        <?php else: ?>
            Not Available
        <?php endif; ?>
    </td>
</tr>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="17" style="text-align: center;">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Next</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
