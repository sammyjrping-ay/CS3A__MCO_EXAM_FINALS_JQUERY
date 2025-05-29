<?php 
    // Include the database connection file
    include_once('connection.php');

    // Establish a database connection
    $con = connection();

    // Check if the form has been submitted
    if (isset($_POST['submit'])) {
        // Trim whitespace from form inputs
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmpassword = $_POST['confirmpassword'];

        // Initialize profile image URL variable
        $profile_image_url = "";  

        // Check if a profile image has been uploaded without errors
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            // Get temporary file path and original file name
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = $_FILES['profile_image']['name'];

            // Extract the file extension
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Define allowed file extensions for profile images
            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Check if uploaded file has an allowed extension
            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Generate a unique file name for the uploaded image
                $newFileName = uniqid('', true) . '.' . $fileExtension;
                $uploadFileDir = 'uploads/users/';

                // Create the upload directory if it does not exist
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                // Set the destination path for the uploaded file
                $dest_path = $uploadFileDir . $newFileName;

                // Attempt to move the uploaded file to the destination path
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Save the uploaded file path to be stored in the database
                    $profile_image_url = $dest_path;  
                } else {
                    // Alert if the file upload failed
                    echo '<script>alert("There was an error uploading the profile image.");</script>';
                }
            } else {
                // Alert if file extension is not allowed
                echo '<script>alert("Only JPG, JPEG, PNG, and GIF files are allowed.");</script>';
            }
        }

        // Validate required form fields
        if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($confirmpassword)) {
            echo '<script>alert("Please fill in all required fields.");</script>';
        }
        // Validate email format
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo '<script>alert("Invalid email format.");</script>';
        }
        // Check if passwords match
        elseif ($password !== $confirmpassword) {
            echo '<script>alert("Passwords do not match.");</script>';
        }
        else {
            // Prepare a statement to check for existing username or email
            $stmt = $con->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            // Check if username or email is already taken
            if ($stmt->num_rows > 0) {
                echo '<script>alert("Username or email already taken.");</script>';
            } else {
                // Hash the password securely before storing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare the insert statement to add new user
                $insert_stmt = $con->prepare("INSERT INTO users (firstname, lastname, username, email, password, created_at, profile_image) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                $insert_stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $hashed_password, $profile_image_url);

                // Execute the insert query and check success
                if ($insert_stmt->execute()) {
                    // Redirect to login page after successful registration
                    header("Location: login.php");
                    exit();
                } else {
                    // Alert if there was an error during registration
                    echo '<script>alert("Error occurred while registering.");</script>';
                }
                // Close the insert statement
                $insert_stmt->close();
            }
            // Close the select statement
            $stmt->close();
        }
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
	<link rel="stylesheet" href="css/signup.css">
	<title>Sign Up</title>
</head>
<body class="bg-gray">
	<div class="hero-container">

		<div class="hero bg-gray txt-light">
			<img class="logo" src="./resources/logo-name-hero.png" alt="">
			<p>
			A 100% anonymous social platform where you can freely share thoughts, vent, and seek advice without fear of judgment. It's your safe space to express yourself, like a digital freedom wall.
			</p>
		</div>

		<div class="sign-up bg-black txt-light">
			<p class="txt-white">Create<br>new account</p>
			
			<form action="" method="POST" enctype="multipart/form-data">
                <label for="firstname">First Name</label><br>
                <input class="bg-gray txt-light" type="text" id="firstname" name="firstname" required><br>

                <label for="lastname">Last Name</label><br>
                <input class="bg-gray txt-light" type="text" id="lastname" name="lastname" required><br>

                <label for="username">Username</label><br>
                <input class="bg-gray txt-light" type="text" id="username" name="username" required><br>

                <label for="email">Email</label><br>
                <input class="bg-gray txt-light" type="email" id="email" name="email" required><br>

                <label for="password">Password</label><br>
                <input class="bg-gray txt-light" type="password" id="password" name="password" required><br>

                <label for="confirmpassword">Confirm Password</label><br>
                <input class="bg-gray txt-light" type="password" id="confirmpassword" name="confirmpassword" required><br>

                <div class="file-input-wrapper">
                    <label for="profile_image" class="file-input-label bg-gray">Upload Profile Image</label>
                    <span class="file-name bg-gray" id="file-name">No file chosen</span>
                    <input type="file" id="profile_image" class="bg-gray" name="profile_image" accept="image/*" />
                </div>

                <button class="btn bg-gold" type="submit" name="submit">Create Account</button>
            </form>



			<p>Already a member? <a class="txt-gold" href="login.php">Log in</a></p>
		</div>

	</div>

    <script>
        const input = document.getElementById('profile_image');
        const fileName = document.getElementById('file-name');

        input.addEventListener('change', function () {
            fileName.textContent = this.files.length > 0 ? this.files[0].name : 'No file chosen';
        });
    </script>
</body>
</html>