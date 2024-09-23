<?php
include 'db.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID.");
}

$id = (int)$_GET['id'];

// Fetch user data
$sql = "SELECT * FROM user_data WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated data from the form
    $name = $_POST['name'];
    $department = $_POST['department'];
    $mobile = $_POST['mobile'];
    $dob = $_POST['dob'];
    $father_name = $_POST['father_name'];
    $adhar_no = $_POST['adhar_no'];
    $local_address = $_POST['local_address'];
    $adhar_address = $_POST['adhar_address'];
    $contractor_name = $_POST['contractor_name'];
    $contractor_address = $_POST['contractor_address'];
    $pass_type = $_POST['pass_type'];
    $adhar_doc = $_FILES['adhar_doc'];
    $photo = $_FILES['photo'];

    // Update user data
    $update_sql = "UPDATE user_data SET 
                    name = ?, 
                    department = ?, 
                    mobile = ?, 
                    dob = ?, 
                    father_name = ?, 
                    adhar_no = ?, 
                    local_address = ?, 
                    adhar_address = ?, 
                    contractor_name = ?, 
                    contractor_address = ?, 
                    pass_type = ?
                    WHERE id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssssssi", 
                            $name, 
                            $department, 
                            $mobile, 
                            $dob, 
                            $father_name, 
                            $adhar_no, 
                            $local_address, 
                            $adhar_address, 
                            $contractor_name, 
                            $contractor_address, 
                            $pass_type, 
                            $id);
    
    if ($update_stmt->execute()) {
        $success = true;
    } else {
        $error = "Error updating profile: " . $conn->error;
    }

    // Handle file uploads
    if ($adhar_doc['error'] == UPLOAD_ERR_OK) {
        $adhar_doc_path = 'uploads/' . basename($adhar_doc['name']);
        move_uploaded_file($adhar_doc['tmp_name'], $adhar_doc_path);
        $conn->query("UPDATE user_data SET adhar_doc = '$adhar_doc_path' WHERE id = $id");
    }

    if ($photo['error'] == UPLOAD_ERR_OK) {
        $photo_path = 'uploads/' . basename($photo['name']);
        move_uploaded_file($photo['tmp_name'], $photo_path);
        $conn->query("UPDATE user_data SET photo = '$photo_path' WHERE id = $id");
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        header nav {
            background-color: #007bff;
            padding: 10px 20px;
            text-align: center;
        }
        header nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        header nav a:hover {
            text-decoration: underline;
        }
        .form-container {
            width: 100%;
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background: linear-gradient(to right, #ffffff, #e6e6e6);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="file"],
        .form-container select {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-container input[type="text"]:focus,
        .form-container input[type="date"]:focus,
        .form-container select:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-container button {
            width: 100%;
            padding: 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .form-container button:hover {
            background-color: #218838;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
    <script>
        function togglePoliceVerification() {
            var passtype = document.getElementById('pass_type').value;
            var policeVerificationField = document.getElementById('police_verification-field');
            var policeVerificationInput = document.getElementById('police_verification');

            if (passtype === 'Permanent') {
                policeVerificationField.style.display = 'block';
                policeVerificationInput.required = true;
            } else {
                policeVerificationField.style.display = 'none';
                policeVerificationInput.required = false;
            }
        }

        function toggleAddress() {
            var isChecked = document.getElementById('same_address').checked;
            document.getElementById('adhar_address').value = isChecked ? document.getElementById('local_address').value : '';
            document.getElementById('adhar_address').readOnly = isChecked;
        }

        window.onload = function() {
            togglePoliceVerification();
            document.getElementById('pass_type').addEventListener('change', togglePoliceVerification);
            document.getElementById('name').focus();
        };
    </script>
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

    <div class="form-container">
        <h2>Edit Profile</h2>

        <?php if (isset($success) && $success): ?>
            <div class="message success">
                Profile updated successfully. Redirecting to profile page...
                <script>
                    setTimeout(function() {
                        window.location.href = 'profile.php';
                    }, 500); // Redirects after .5 second
                </script>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <form action="edit_profile.php?id=<?php echo htmlspecialchars($id); ?>" method="post" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                <label for="pass_type">Pass Type:</label>
                <select id="pass_type" name="pass_type" onchange="togglePoliceVerification()" required>
                    <option value="">Select Pass Type</option>
                    <option value="Temporary" <?php echo isset($user['pass_type']) && $user['pass_type'] == 'Temporary' ? 'selected' : ''; ?>>Temporary</option>
                    <option value="Overtime" <?php echo isset($user['pass_type']) && $user['pass_type'] == 'Overtime' ? 'selected' : ''; ?>>Overtime</option>
                    <option value="Permanent" <?php echo isset($user['pass_type']) && $user['pass_type'] == 'Permanent' ? 'selected' : ''; ?>>Permanent</option>
                </select>

                <label for="department">Department:</label>
                <select id="department" name="department" required>
                    <option value="">Select Department</option>
                    <option value="Air Conditioning" <?php echo isset($user['department']) && $user['department'] == 'Air Conditioning' ? 'selected' : ''; ?>>Air Conditioning</option>
                    <option value="Mechanical" <?php echo isset($user['department']) && $user['department'] == 'Mechanical' ? 'selected' : ''; ?>>Mechanical</option>
                    <option value="Electrical" <?php echo isset($user['department']) && $user['department'] == 'Electrical' ? 'selected' : ''; ?>>Electrical</option>
                    <option value="Water Treatment Plant" <?php echo isset($user['department']) && $user['department'] == 'Water Treatment Plant' ? 'selected' : ''; ?>>Water Treatment Plant</option>
                    <option value="Controls" <?php echo isset($user['department']) && $user['department'] == 'Controls' ? 'selected' : ''; ?>>Controls</option>
                    <option value="MM" <?php echo isset($user['department']) && $user['department'] == 'MM' ? 'selected' : ''; ?>>MM</option>
                    <option value="Transport" <?php echo isset($user['department']) && $user['department'] == 'Transport' ? 'selected' : ''; ?>>Transport</option>
                    <option value="Civil Factory Maintenance" <?php echo isset($user['department']) && $user['department'] == 'Civil Factory Maintenance' ? 'selected' : ''; ?>>Civil Factory Maintenance</option>
                    <option value="Estate" <?php echo isset($user['department']) && $user['department'] == 'Estate' ? 'selected' : ''; ?>>Estate</option>
                    <option value="Horticulture" <?php echo isset($user['department']) && $user['department'] == 'Horticulture' ? 'selected' : ''; ?>>Horticulture</option>
                    <option value="Housekeeping" <?php echo isset($user['department']) && $user['department'] == 'Housekeeping' ? 'selected' : ''; ?>>Housekeeping</option>
                    <option value="PMG" <?php echo isset($user['department']) && $user['department'] == 'PMG' ? 'selected' : ''; ?>>PMG</option>
                </select>

                <label for="mobile">Mobile:</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>

                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" required>

                <label for="father_name">Father's Name:</label>
                <input type="text" id="father_name" name="father_name" value="<?php echo htmlspecialchars($user['father_name']); ?>" required>

                <label for="adhar_no">Aadhaar Number:</label>
                <input type="text" id="adhar_no" name="adhar_no" value="<?php echo htmlspecialchars($user['adhar_no']); ?>" required>

                <label for="local_address">Local Address:</label>
                <input type="text" id="local_address" name="local_address" value="<?php echo htmlspecialchars($user['local_address']); ?>" required>

                <label for="same_address">
                    <input type="checkbox" id="same_address" name="same_address" onclick="toggleAddress()"> Adhar Address same as Local Address
                </label>

                <label for="adhar_address">Aadhaar Address:</label>
                <input type="text" id="adhar_address" name="adhar_address" value="<?php echo htmlspecialchars($user['adhar_address']); ?>" required>

                <label for="contractor_name">Contractor's Name:</label>
                <input type="text" id="contractor_name" name="contractor_name" value="<?php echo htmlspecialchars($user['contractor_name']); ?>" required>

                <label for="contractor_address">Contractor's Address:</label>
                <input type="text" id="contractor_address" name="contractor_address" value="<?php echo htmlspecialchars($user['contractor_address']); ?>" required>

                <label for="adhar_doc">Aadhaar Document:</label>
                <input type="file" id="adhar_doc" name="adhar_doc">

                <label for="photo">Profile Photo:</label>
                <input type="file" id="photo" name="photo">

                <div id="police_verification-field" style="<?php echo $user['pass_type'] === 'Permanent' ? '' : 'display:none;'; ?>">
                    <label for="police_verification">Police Verification Document:</label>
                    <input type="file" id="police_verification" name="police_verification">
                </div>

                <button type="submit">Update Profile</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
