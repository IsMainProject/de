<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Basic Reset */
        body, h1, nav, a {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Set font and base styles */
        body {
            font-family: 'Roboto', sans-serif;
    line-height: 1.6;
    color: #333;
    background: url('images/belimg.jpg') no-repeat center center fixed;
    background-size: contain;
    background-attachment: fixed;
    background-position: center;
    padding: 0;
    margin: 0;
}

        

        /* Header and Navbar Styling */
        header {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent black */
            color: #fff;
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            font-weight: bold;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        nav a:hover {
            background-color: #007bff;
            color: #fff;
            transform: scale(1.1);
        }

        /* Main Content Styling */
        main {
            flex-grow: 1;
            padding: 120px 20px 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        h1 {
            font-size: 48px;
            color: #fff;
            margin-top: 20px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            animation: fadeIn 2s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Footer Styling */
        footer {
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav a {
                font-size: 14px;
                padding: 8px 15px;
            }

            h1 {
                font-size: 36px;
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
            <a href="change_password_user.php">Change Password</a>
            <a href="index.php">Logout</a>
        </nav>
    </header>
    <main>
        <h1>Welcome, Bharat Electronics Limited!</h1>
    </main>
    <footer>
        &copy; 2024 Bharat Electronics Limited. All rights reserved.
    </footer>
</body>
</html>
