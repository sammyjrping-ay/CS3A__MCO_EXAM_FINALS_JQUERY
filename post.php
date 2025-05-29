<?php
session_start(); // Start session to access user info
include_once('connection.php');
$con = connection(); // Create database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<script>alert("Please log in to post."); window.location = "index.php";</script>';
    exit();
}

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id']; // Get logged-in user ID
    $content = trim($_POST['content']); // Get post content and trim whitespace
    $post_image = null;

    // Handle image upload if provided
    if (!empty($_FILES['post_image']['name'])) {
        $upload_dir = "uploads/posts/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create upload directory if not exists
        }

        $original_name = basename($_FILES['post_image']['name']);
        // Sanitize filename and prepend timestamp for uniqueness
        $unique_name = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $original_name);
        $target_file = $upload_dir . $unique_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
                $post_image = $target_file; // Save image path if upload succeeds
            } else {
                echo '<script>alert("Failed to upload image.");</script>';
                exit();
            }
        } else {
            echo '<script>alert("Invalid image file type.");</script>';
            exit();
        }
    }

    // Generate random initial counts for likes, comments, and shares
    $likes = rand(0, 500);
    $comments = rand(0, 100);
    $shares = rand(0, 50);

    // Insert new post into database
    $stmt = $con->prepare("
        INSERT INTO posts (user_id, content, post_image, likes, comments, shares, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issiii", $user_id, $content, $post_image, $likes, $comments, $shares);

    if ($stmt->execute()) {
        header("Location: feed.php"); // Redirect to feed after successful post
        exit();
    } else {
        echo '<script>alert("Failed to create post.");</script>';
    }

    $stmt->close();
}
?>
