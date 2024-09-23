<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    
    <style>
        /* Basic Reset */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        /* Background Image */
        body {
            background-size: cover;
            background-position: center;
            color: #fff;
            text-align: center;
        }

        /* Header Styles */
        header {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent background */
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        /* Logo Styles */
        .logo {
            height: 50px; /* Adjust as needed */
        }

        /* Navigation Styles */
        nav {
            display: flex;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            margin: 0 10px;
            font-weight: bold;
            border-radius: 5px;
            background-color: #007BFF; /* Primary button color */
            transition: background-color 0.3s, transform 0.3s;
        }

        nav a:hover {
            background-color: #0056b3; /* Darker shade on hover */
            transform: scale(1.05); /* Slight scale effect on hover */
        }

        /* Carousel Styles */
        .carousel {
            position: relative;
            width: 100%;
            max-height: 500px;
            overflow: hidden;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel img {
            width: 100%;
            height: auto;
            display: block;
            transition: opacity 1s ease-in-out;
        }

        .carousel img.hidden {
            opacity: 0;
        }

        .carousel-controls {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
        }

        .carousel-controls button {
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: #fff;
            padding: 10px;
            cursor: pointer;
            font-size: 24px;
            transition: background 0.3s;
        }

        .carousel-controls button:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Footer Styles */
        footer {
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent background */
            color: #fff;
            padding: 3px 15px; /* Reduced padding */
            text-align: center;
            position: relative;
        }

        footer p {
            margin: 5px 0;
            font-size: 12px; /* Reduced font size */
        }

        footer a {
            color: #007BFF;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const images = document.querySelectorAll('.carousel img');
            let current = 0;

            function showImage(index) {
                images[current].classList.add('hidden');
                current = (index + images.length) % images.length;
                images[current].classList.remove('hidden');
            }

            function showNextImage() {
                showImage(current + 1);
            }

            function showPrevImage() {
                showImage(current - 1);
            }

            document.getElementById('next').addEventListener('click', showNextImage);
            document.getElementById('prev').addEventListener('click', showPrevImage);
        });
    </script>
</head>
<body>
    <header>
        <img src="images/bel_logo.png" alt="Company Logo" class="logo"> <!-- Update with your logo -->
        <nav>
            <a href="user_login.php">User Login</a>
            <a href="admin_login.php">Admin Login</a>
        </nav>
    </header>

    <div class="carousel">
        <img src="images\belimg1.jpg" alt="Carousel Image 1">
        <!-- <img src="images\belimg1.jpg" alt="Carousel Image 2" class="hidden">
        <img src="images\belimg2.jpg" alt="Carousel Image 3" class="hidden"> -->
        <!-- <div class="carousel-controls">
            <button id="prev">&#10094;</button>
            <button id="next">&#10095;</button>
        </div> -->
    </div>

    <footer>
        <p>&copy; 2024 Bharat Electronics Limited (BEL). All Rights Reserved.</p>
        <p>A Public Sector Undertaking under the Ministry of Defence, Government of India</p>
        <p><a href="privacy_policy.php">Privacy Policy</a> | <a href="terms_conditions.php">Terms & Conditions</a></p>
    </footer>
</body>
</html>
