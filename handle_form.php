<?php
include 'db.php';

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$errors = [];

// Collect and sanitize form data
$name = sanitize_input($_POST['name'] ?? '');
$passno = sanitize_input($_POST['passno'] ?? 'null');
$pass_type = sanitize_input($_POST['pass_type'] ?? ''); // New field for pass type
$mobile = sanitize_input($_POST['mobile'] ?? '');
$dob = sanitize_input($_POST['dob'] ?? '');
$father_name = sanitize_input($_POST['father_name'] ?? '');
$adhar_no = sanitize_input($_POST['adhar_no'] ?? '');
$local_address = sanitize_input($_POST['local_address'] ?? '');
$adhar_address = sanitize_input($_POST['adhar_address'] ?? '');
$contractor_name = sanitize_input($_POST['contractor_name'] ?? '');
$contractor_address = sanitize_input($_POST['contractor_address'] ?? '');
$department = sanitize_input($_POST['department'] ?? '');

// Allowed file types
$allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
$uploads_dir = 'uploads/';

// File upload helper function
function handle_upload($file_input, $uploads_dir, $allowed_extensions, &$errors) {
    $file_name = null;
    if (isset($_FILES[$file_input]) && $_FILES[$file_input]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$file_input]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_extensions)) {
            $errors[] = "Invalid file type for $file_input. Only " . implode(', ', $allowed_extensions) . " allowed.";
        } else {
            $file_name = uniqid() . '.' . $ext;
            if (!move_uploaded_file($_FILES[$file_input]['tmp_name'], $uploads_dir . $file_name)) {
                $errors[] = "Error uploading $file_input.";
            }
        }
    }
    return $file_name;
}

// Handle file uploads
$adhar_doc = handle_upload('adhar_doc', $uploads_dir, $allowed_extensions, $errors);
$photo = handle_upload('photo', $uploads_dir, $allowed_extensions, $errors);
$police_verification = handle_upload('police_verification', $uploads_dir, $allowed_extensions, $errors);

// If there are errors, display them and exit
if (!empty($errors)) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                margin: 0;
                padding: 0;
            }
            .error {
                background: white;
                padding: 20px;
                margin: 20px;
                border: 1px solid #e0e0e0;
                border-radius: 5px;
                text-align: center;
            }
            .error p {
                color: red;
            }
        </style>
    </head>
    <body>
        <div class='error'>
            <h2>Errors Occurred</h2>";
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
    echo "    <a href='form.php'>Go Back</a>
        </div>
    </body>
    </html>";
    exit();
}

// Prepare SQL query
$sql = "INSERT INTO user_data 
    (name, passno, pass_type, mobile, dob, father_name, adhar_no, local_address, adhar_address, contractor_name, contractor_address, department, adhar_doc, photo, police_verification)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
    name = VALUES(name), 
    pass_type = VALUES(pass_type),
    mobile = VALUES(mobile), 
    dob = VALUES(dob), 
    father_name = VALUES(father_name), 
    adhar_no = VALUES(adhar_no), 
    local_address = VALUES(local_address), 
    adhar_address = VALUES(adhar_address), 
    contractor_name = VALUES(contractor_name), 
    contractor_address = VALUES(contractor_address), 
    department = VALUES(department), 
    adhar_doc = VALUES(adhar_doc), 
    photo = VALUES(photo), 
    police_verification = VALUES(police_verification)";

$stmt = $conn->prepare($sql);

// Bind parameters
$types = "sssssssssssssss";
$params = [$name, $passno, $pass_type, $mobile, $dob, $father_name, $adhar_no, $local_address, $adhar_address, $contractor_name, $contractor_address, $department, $adhar_doc, $photo, $police_verification];
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Success</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                margin: 0;
                padding: 0;
            }
            .popup {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .popup-content {
                background: white;
                padding: 20px;
                border-radius: 5px;
                text-align: center;
                position: relative;
            }
            .popup-content button {
                margin: 10px;
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            .popup-content button:hover {
                background-color: #0056b3;
            }
        </style>
        <script>
            function redirectToForm() {
                window.location.href = 'form.php';
            }
        </script>
    </head>
    <body>
        <div class='popup'>
            <div class='popup-content'>
                <h2>Profile Created Successfully</h2>
                <button onclick='redirectToForm()'>OK</button>
            </div>
        </div>
    </body>
    </html>";
} else {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                margin: 0;
                padding: 0;
            }
            .error {
                background: white;
                padding: 20px;
                margin: 20px;
                border: 1px solid #e0e0e0;
                border-radius: 5px;
                text-align: center;
            }
            .error p {
                color: red;
            }
        </style>
    </head>
    <body>
        <div class='error'>
            <h2>Error</h2>
            <p>Error updating record: " . $stmt->error . "</p>
            <a href='form.php'>Go Back</a>
        </div>
    </body>
    </html>";
}

$stmt->close();
$conn->close();
?>
