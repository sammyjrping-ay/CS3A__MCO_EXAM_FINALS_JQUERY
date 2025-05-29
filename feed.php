<?php
    session_start(); // Start the session

    include_once('connection.php'); // Include database connection
    $con = connection(); // Establish database connection

    // Query to fetch latest posts with user details
    $sql = "SELECT 
                users.username, 
                users.profile_image, 
                posts.post_image, 
                posts.content, 
                posts.likes, 
                posts.comments, 
                posts.shares, 
                posts.created_at
            FROM posts
            JOIN users ON posts.user_id = users.id
            ORDER BY posts.created_at DESC";

    $result = $con->query($sql); // Execute query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whisper</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/feed.css">
    <link rel="stylesheet" href="css/index.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .profile {
            height: 70px;
            width: 70px !important;
            object-fit: cover !important;
            border-radius: 40px;
            border: 1px solid white;
        }
        .time-container h2 {
            margin-left: 20px !important;
            text-align: left !important;
        }
    </style>

</head>
<body class="bg-gray">
	<div class="fyp-container">
		<header class="bg-black">
            <!-- Logo image -->
            <img id="fyp-logo" src="resources/logo-name-hero.png" alt="">

            <!-- Navigation link to Dashboard -->
            <nav>
                <a href="dashboard.php">Dashboard</a>
            </nav>

            <!-- Logout link with icon -->
            <a id="logout" class="txt-light" href="index.php">
                Logout <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </header>


        <form class="post-form txt-white bg-black" action="post.php" method="post" enctype="multipart/form-data">
            <!-- Textarea for post content -->
            <textarea class="bg-gray txt-white" name="content" placeholder="Write something..." required></textarea>

            <!-- File input for optional post image -->
            <input class="button" type="file" name="post_image" id="image-upload" required>

            <!-- Submit button to post content -->
            <input type="submit" name="submit" value="Post" class="submit-button .btn bg-gold">
        </form>


        <?php
            if ($result->num_rows > 0) { // Check if there are any posts
                while ($row = $result->fetch_assoc()) { // Loop through each post
                    // Sanitize output to prevent XSS
                    $username = htmlspecialchars($row['username']);
                    $profileImage = htmlspecialchars($row['profile_image']); 
                    $contentImage = htmlspecialchars($row['post_image']); 
                    $content = nl2br(htmlspecialchars($row['content'])); // Convert new lines to <br>
                    $likes = (int)$row['likes']; // Cast likes count to integer
                    $comments = (int)$row['comments']; // Cast comments count to integer
                    $shares = (int)$row['shares']; // Cast shares count to integer
                    $createdAtRaw = $row['created_at']; // Raw timestamp
                    $createdAt = date("F j, Y \\a\\t g:i A", strtotime($createdAtRaw)); // Format date

                    // Display post HTML
                    echo '
                    <div class="post txt-light">
                        <div class="post-header">
                            <img class="profile" src="' . $profileImage . '" alt="">
                            <div class="time-container">
                                <h2>' . $username . '</h2>
                                <p class="timestamp">' . $createdAt . '</p>
                            </div>
                            <i class="fa-regular fa-bookmark bookmark-icon" ></i>
                        </div>

                        <div class="content">
                            <img src="' . $contentImage . '" width="375" alt="">
                            <p>' . $content . '</p>
                        </div>

                        <div class="post-footer">
                            <div class="likes icon">
                                <i class="fa-regular fa-heart heart-icon" ></i>
                                <p>' . $likes . ' likes</p>
                            </div>

                            <div class="comments icon">
                                <i class="fa-regular fa-comment"></i>
                                <p>' . $comments . ' comments</p>
                            </div>

                            <div class="shares icon">
                                <i class="fa-regular fa-paper-plane share-icon" ></i>
                                <p>' . $shares . ' shares</p>
                            </div>
                        </div>
                    </div>
                    ';
                }
            }
        ?>


		<div id="voice" class="post txt-light">

			<div class="post-header">
                <img class="profile" src="resources/profiles/voice-profile.jpg" alt="">
                <div class="time-container">
                    <h2>Voice</h2>
                    <p class="timestamp">May 27, 2025 at 4:29 AM</p>
                </div>
				<i class="fa-regular fa-bookmark bookmark-icon" ></i>
			</div>

			<div class="content">
				<img src="resources/post/voice-post.jpg" width="375" alt="">
				<p>
				“I feel like I’m drowning in expectations, both my own and everyone else's. Every day feels like a performance, but I’ve forgotten my lines. I keep wondering if I’ll ever figure out what I truly want, or if I’m just trying to fit into someone else’s mold. The weight of pretending is heavier than anyone realizes. I just wish I could find peace in being myself.”
				</p>
			</div>

			<div class="post-footer">
				<div class="likes icon">
				<i class="fa-regular fa-heart heart-icon" ></i>
				<p>100 likes</p>
				</div>

				<div class="comments icon">
				<i class="fa-regular fa-comment"></i>
				<p>100 comments</p>
				</div>

				<div class="shares icon">
				<i class="fa-regular fa-paper-plane share-icon" ></i>
				<p>100 shares</p>
				</div>
			</div>

		</div>


		<div id="star" class="post txt-light">

			<div class="post-header">
				<img class="profile" src="resources/profiles/star-profile.jpg" alt="">
				<div class="time-container">
                    <h2>Star</h2>
                    <p class="timestamp">May 27, 2025 at 4:29 AM</p>
                </div>
				<i class="fa-regular fa-bookmark bookmark-icon" ></i>
			</div>

			<div class="content">
				<img src="resources/post/star-post.jpg" width="375" alt="">
				<p>
				“I always feel like I'm running out of time, like every moment slipping away is a chance I'll never get back. There's this constant pressure to achieve, to succeed, to be someone worth remembering. But in chasing all these goals, I've lost sight of what genuinely makes me happy. The exhaustion feels endless, but admitting it feels like failure. I just need a break from everything, even if it's only for a little while.”
				</p>
			</div>

			<div class="post-footer">
				<div class="likes icon">
				<i class="fa-regular fa-heart heart-icon" ></i>
				<p>100 likes</p>
				</div>
				<div class="comments icon">
				<i class="fa-regular fa-comment"></i>
				<p>100 comments</p>
				</div>
				<div class="shares icon">
				<i class="fa-regular fa-paper-plane share-icon" ></i>
				<p>100 shares</p>
				</div>
			</div>

		</div>


		<div id="echo" class="post txt-light">

			<div class="post-header">
				<img class="profile" src="resources/profiles/echo-profile.jpg" alt="">
				<div class="time-container">
                    <h2>Echo</h2>
                    <p class="timestamp">May 27, 2025 at 4:29 AM</p>
                </div>
				<i class="fa-regular fa-bookmark bookmark-icon" ></i>
			</div>

			<div class="content-column">
				<img src="resources/post/echo-post.jpg" width="500" alt="">
				<p>
				“It's been months since we stopped talking, but your absence still echoes through everything. I thought time would heal, but it only sharpens the emptiness. I find myself scrolling through old messages, wondering if you ever do the same. Maybe we were meant to drift apart, but it doesn't stop the hurt. Sometimes I wish I could just”
				</p>
			</div>

			<div class="post-footer">
				<div class="likes icon">
				<i class="fa-regular fa-heart heart-icon" ></i>
				<p>100 likes</p>
				</div>
				<div class="comments icon">
				<i class="fa-regular fa-comment"></i>
				<p>100 comments</p>
				</div>
				<div class="shares icon">
				<i class="fa-regular fa-paper-plane share-icon" ></i>
				<p>100 shares</p>
				</div>
			</div>

		</div>


		<div id="glimpse" class="post txt-light">

			<div class="post-header">
				<img class="profile" src="resources/profiles/glimpse-profile.jpg" alt="">
                <div class="time-container">
                    <h2>Glimpse</h2>
                    <p class="timestamp">May 27, 2025 at 4:29 AM</p>
                </div>
				<i class="fa-regular fa-bookmark bookmark-icon" ></i>
			</div>

			<div class="content-column">
				<img src="resources/post/glimpse-post.jpg" width="375" alt="">
				<p>
				“Lately, I’ve been catching fleeting moments of happiness, like sunlight slipping through the cracks. It never stays long, but when it’s there, everything feels lighter, almost bearable. I wonder if life is just a collection of these tiny fragments, stitched together by hope. Maybe I'm too focused on chasing something lasting, instead of appreciating what's right in front of me. It’s hard to let go of the longing, but I’m trying.”
				</p>
			</div>

			<div class="post-footer">
				<div class="likes icon">
				<i class="fa-regular fa-heart heart-icon" ></i>
				<p>100 likes</p>
				</div>
				<div class="comments icon">
				<i class="fa-regular fa-comment"></i>
				<p>100 comments</p>
				</div>
				<div class="shares icon">
				<i class="fa-regular fa-paper-plane share-icon" ></i>
				<p>100 shares</p>
				</div>
			</div>

		</div>
	</div>

    <footer class="bg-black txt-white">
        <div class="app-name">
            <h1>Whisper</h1>
            <p>© 2025 JQuery All rights reserved.</p>
        </div>
        <div class="names">
            <p>Brigitte Mae E. Ibeng</p>
            <p>Izza Katherine R. Dela Rosa</p>
            <p>Sammy T. Ping-ay Jr.</p>
        </div>
    </footer>

    <script defer>
    // Toggle heart icon state on click (like)
    document.querySelectorAll('.heart-icon').forEach(heart => {
        heart.addEventListener('click', () => {
            heart.classList.toggle('fa-regular'); // Toggle outlined style
            heart.classList.toggle('fa-solid');   // Toggle solid style
            heart.classList.toggle('active');     // Toggle active class
        });
    });

    // Toggle bookmark icon state on click (favorite)
    document.querySelectorAll('.bookmark-icon').forEach(bookmark => {
        bookmark.addEventListener('click', () => {
            bookmark.classList.toggle('active'); // Toggle active class
        });
    });

    // Toggle share icon state on click
    document.querySelectorAll('.share-icon').forEach(share => {
        share.addEventListener('click', () => {
            share.classList.toggle('fa-regular'); // Toggle outlined style
            share.classList.toggle('fa-solid');   // Toggle solid style
            share.classList.toggle('active');     // Toggle active class
        });
    });
</script>

</body>
</html> 