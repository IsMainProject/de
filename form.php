<?php
include 'db.php';

// Fetch user data
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = null;

if ($user_id > 0) {
    $sql = "SELECT * FROM user_data WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fill Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        /* Header */
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

        .required-label::after {
    content: '*';
    color: red;
    margin-left: 4px;
    font-size: 16px;
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
    </style>
    <script>
        function togglePoliceVerification() {
    var passtype = document.getElementById('pass_type').value;
    var policeVerificationInput = document.getElementById('police_verification');
    var policeVerificationStar = document.getElementById('police_verification_star');

    // Show or hide the red star based on the pass type
    if (passtype === 'Permanent') {
        policeVerificationInput.required = true;
        policeVerificationStar.style.display = 'inline';
    } else {
        policeVerificationInput.required = false;
        policeVerificationStar.style.display = 'none';
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

        function sendData() {
            // Example function to send form data to gatepass.php
            document.getElementById('form').action = 'status.php';
            document.getElementById('form').submit();
        }
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
        <h2>Fill Form</h2>
        <form action="handle_form.php" method="post" enctype="multipart/form-data">
    <label for="name" class="required-label">Name:</label>
    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

    <label for="pass_type" class="required-label">Pass Type:</label>
    <select id="pass_type" name="pass_type" onchange="togglePoliceVerification()" required>
        <option value="">Select Pass Type</option>
        <option value="Temporary" <?php echo isset($user['pass_type']) && $user['pass_type'] == 'Temporary' ? 'selected' : ''; ?>>Temporary</option>
        <option value="Permanent" <?php echo isset($user['pass_type']) && $user['pass_type'] == 'Permanent' ? 'selected' : ''; ?>>Permanent</option>
    </select>

    <label for="mobile" class="required-label">Mobile Number:</label>
    <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>" pattern="\d{10}" title="Mobile number must be 10 digits" required>

    <label for="dob" class="required-label">Date of Birth:</label>
    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>" max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" title="Date of Birth must be at least 18 years ago" required>

    <label for="father_name" class="required-label">Father's Name:</label>
    <input type="text" id="father_name" name="father_name" value="<?php echo htmlspecialchars($user['father_name'] ?? ''); ?>" required>

    <label for="adhar_no" class="required-label">Aadhar No:</label>
    <input type="text" id="adhar_no" name="adhar_no" value="<?php echo htmlspecialchars($user['adhar_no'] ?? ''); ?>" pattern="\d{12}" title="Aadhar number must be 12 digits" required>

    <label for="local_address">Local Address:</label>
    <input type="text" id="local_address" name="local_address" value="<?php echo htmlspecialchars($user['local_address'] ?? ''); ?>">

    <label>
        <input type="checkbox" id="same_address" onclick="toggleAddress()"> Local Address and Aadhar Address are the same?
    </label>

    <label for="adhar_address" class="required-label">Aadhar Address:</label>
    <input type="text" id="adhar_address" name="adhar_address" value="<?php echo htmlspecialchars($user['adhar_address'] ?? ''); ?>" required>

    <label for="contractor_name" class="required-label">Name of Contractor:</label>
    <input type="text" id="contractor_name" name="contractor_name" value="<?php echo htmlspecialchars($user['contractor_name'] ?? ''); ?>" required>
    
    <label for="contractor_address">Address of Contractor:</label>
    <input type="text" id="contractor_address" name="contractor_address" value="<?php echo htmlspecialchars($user['contractor_address'] ?? ''); ?>">

    <label for="department" class="required-label">Department:</label>
    <select id="department" name="department" required>
        <option value="">Select Department</option>
        <option value="Air Conditioning" <?php echo isset($user['department']) && $user['department'] == 'Air Conditioning' ? 'selected' : ''; ?>>Air Conditioning</option>
        <option value="Mechanical" <?php echo isset($user['department']) && $user['department'] == 'Mechanical' ? 'selected' : ''; ?>>Mechanical</option>
        <option value="Electrical" <?php echo isset($user['department']) && $user['department'] == 'Electrical' ? 'selected' : ''; ?>>Electrical</option>
        <option value="Water Treatment Plant" <?php echo isset($user['department']) && $user['department'] == 'Water Treatment Plant' ? 'selected' : ''; ?>>Water Treatment Plant</option>
        <option value="Contracts" <?php echo isset($user['department']) && $user['department'] == 'Contracts' ? 'selected' : ''; ?>>Contracts</option>
        <option value="MM" <?php echo isset($user['department']) && $user['department'] == 'MM' ? 'selected' : ''; ?>>MM</option>
        <option value="Horticulture" <?php echo isset($user['department']) && $user['department'] == 'Horticulture' ? 'selected' : ''; ?>>Horticulture</option>
        <option value="HouseKeeping" <?php echo isset($user['department']) && $user['department'] == 'HouseKeeping' ? 'selected' : ''; ?>>HouseKeeping</option>
        <option value="Transport" <?php echo isset($user['department']) && $user['department'] == 'Transport' ? 'selected' : ''; ?>>Transport</option>
        <option value="Civil Factory Maintenance" <?php echo isset($user['department']) && $user['department'] == 'Civil Factory Maintenance' ? 'selected' : ''; ?>>Civil Factory Maintenance</option>
        <option value="Estate" <?php echo isset($user['department']) && $user['department'] == 'Estate' ? 'selected' : ''; ?>>Estate</option>
        <option value="PMG" <?php echo isset($user['department']) && $user['department'] == 'PMG' ? 'selected' : ''; ?>>PMG</option>
    </select>

    <label for="adhar_doc" class="required-label">Aadhar Card Document:</label>
    <input type="file" id="adhar_doc" name="adhar_doc" <?php if (!$user) echo ''; ?>required>

    <label for="photo" class="required-label">Photo:</label>
    <input type="file" id="photo" name="photo" <?php if (!$user) echo ''; ?>required>

    <label for="police_verification" id="police_verification_label" style="display: inline; margin-right: 5px;">Police Verification:</label>
<span id="police_verification_star" style="color: red; display: inline;">*</span>
<input type="file" id="police_verification" name="police_verification">


    <button type="submit">Submit</button>
</form>

    </div>
</body>
</html>
