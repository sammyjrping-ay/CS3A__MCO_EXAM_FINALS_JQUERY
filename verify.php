<?php
    // Start a new or resume existing session
    session_start();

    // Include database connection file
    include_once('connection.php');

    // Establish database connection
    $con = connection();

    // Check if the login form is submitted
    if (isset($_POST['submit'])) {
        // Get username and password from POST request
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare SQL statement to fetch user data based on username
        $stmt = $con->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Get the result set from the executed statement
        $result = $stmt->get_result();

        // Check if exactly one user was found with the given username
        if ($result->num_rows === 1) {
            // Fetch the user's data as an associative array
            $row = $result->fetch_assoc();

            // Verify the provided password matches the hashed password in the database
            if (password_verify($password, $row['password'])) {
                // Set session variables to keep user logged in
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email']; 

                // Redirect user to the OTP verification page
                header("Location: email-verify/send_otp.php");
                exit();
            } else {
                // Alert if the password is incorrect
                echo '<script>alert("Incorrect password.");</script>';
            }
        } else {
            // Alert if no user was found with the given username
            echo '<script>alert("No user found!");</script>';
        }
        // Close the prepared statement
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/slv.css">
	<link rel="stylesheet" href="css/verify.css">
	<title>Verify Email</title>
</head>
<body class="bg-gray">
	<div class="hero-container bg-gray txt-light ">

		<div class="hero">
			<img class="logo" src="./resources/logo-name-hero.png" alt="">
			<p>
			A 100% anonymous social platform where you can freely share thoughts, vent, and seek advice without fear of judgment. It's your safe space to express yourself, like a digital freedom wall.
			</p> 
		</div>

		<div class="login bg-black txt-light">
			<p class="txt-white">Verify<br>your OTP</p>

			<form action="email-verify/verify_otp.php" method="post">
			<label for="otp">Enter 6-digit OTP</label><br>
			<input class="bg-gray txt-white" type="text" name="otp" required><br>

            <button class="btn bg-gold" type="submit" name="submit">Verify</button>
			</form>

		</div>

	</div>
</body>
</html>