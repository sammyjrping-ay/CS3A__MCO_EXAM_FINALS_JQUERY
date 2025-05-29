<?php
    session_start(); // Start session to track user login

    include_once('connection.php');
    $con = connection(); // Create database connection

    if (isset($_POST['submit'])) { // Check if login form was submitted
        $username = $_POST['username']; // Get username from form
        $password = $_POST['password']; // Get password from form

        // Prepare SQL statement to prevent SQL injection
        $stmt = $con->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username); // Bind username parameter
        $stmt->execute(); // Execute query
        $result = $stmt->get_result(); // Get query result

        if ($result->num_rows === 1) { // Check if user exists
            $row = $result->fetch_assoc();

            // Verify password hash
            if (password_verify($password, $row['password'])) {
                // Store user info in session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                header("Location: email-verify/send_otp.php"); // Redirect after successful login
                exit();
            } else {
                echo '<script>alert("Incorrect password.");</script>'; // Password mismatch alert
            }
        } else {
            echo '<script>alert("No user found!");</script>'; // No user with that username
        }
        $stmt->close(); // Close statement
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
	<title>Login</title>
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
			<p class="txt-white">Speak freely,<br>anonymously</p>

			<form action="" method="post">
                <label for="username">Username</label><br>
                <input class="bg-gray txt-light" type="text" id="username" name="username"><br>

                <label for="password">Password</label><br>
                <input class="bg-gray txt-light" type="password" id="password" name="password"><br>

                <button class="btn bg-gold" type="submit" name="submit">Login</button>
			</form>

			<p>Already a member? <a class="txt-gold" href="signup.php">Create account</a></p>
		</div>

	</div>
</body>
</html>