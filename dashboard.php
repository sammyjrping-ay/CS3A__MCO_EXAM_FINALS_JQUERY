<?php
    include_once('connection.php'); // Include database connection script
    $con = connection(); // Establish database connection

    $hashtag_counts = []; // Initialize array to store hashtag counts

    // Fetch all post contents
    $sql = "SELECT content FROM posts";
    $result = $con->query($sql);

    if ($result && $result->num_rows > 0) {
        // Loop through each post
        while ($row = $result->fetch_assoc()) {
            // Find all hashtags in the content
            preg_match_all('/#\w+/', $row['content'], $matches);
            foreach ($matches[0] as $hashtag) {
                $hashtag = strtolower($hashtag); // Convert to lowercase
                // Initialize count if hashtag not seen before
                if (!isset($hashtag_counts[$hashtag])) {
                    $hashtag_counts[$hashtag] = 0;
                }
                $hashtag_counts[$hashtag]++; // Increment hashtag count
            }
        }
    }

    arsort($hashtag_counts); // Sort hashtags by count in descending order

    $top_hashtags = array_slice($hashtag_counts, 0, 20, true); // Get top 20 hashtags

    $labels = json_encode(array_keys($top_hashtags)); // Convert hashtag names to JSON
    $data = json_encode(array_values($top_hashtags)); // Convert hashtag counts to JSON
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Hashtag Frequency Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/feed.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray txt-white">
    <!-- Main container for the page -->
    <div class="fyp-container">
        <header class="bg-black">
            <!-- Logo image -->
            <img id="fyp-logo" src="resources/logo-name-hero.png" alt="">

            <!-- Navigation link to Newsfeed -->
            <nav>
                <a href="feed.php">Newsfeed</a>
            </nav>

            <!-- Logout link -->
            <a id="logout" class="txt-light" href="index.php">
                Logout <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </header>
    </div>

    <!-- Heading for the report section -->
    <h2>Hashtag Frequency Report</h2>

    <!-- Table to display hashtags and their counts -->
    <table>
        <thead>
            <tr><th>Hashtag</th><th>Count</th></tr>
        </thead>
        <tbody>
            <?php if (empty($hashtag_counts)): ?>
                <!-- Message if no hashtags are found -->
                <tr><td colspan="2" class="no-data">No hashtags found.</td></tr>
            <?php else: ?>
                <!-- Loop to display each hashtag and its count -->
                <?php foreach ($top_hashtags as $tag => $count): ?>
                    <tr>
                        <td><?= htmlspecialchars($tag) ?></td> <!-- Display hashtag -->
                        <td><?= (int)$count ?></td> <!-- Display count -->
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($top_hashtags)): ?>
    <!-- Display charts only if there are hashtags -->
    <div class="chart-container">
        <div class="chart-wrapper">
            <h3>Bar Chart</h3>
            <canvas id="barChart"></canvas> <!-- Canvas for bar chart -->
        </div>

        <div class="chart-wrapper">
            <h3>Pie Chart</h3>
            <canvas id="pieChart"></canvas> <!-- Canvas for pie chart -->
        </div>

        <div class="chart-wrapper">
            <h3>Doughnut Chart</h3>
            <canvas id="doughnutChart"></canvas> <!-- Canvas for doughnut chart -->
        </div>

        <div class="chart-wrapper">
            <h3>Line Chart</h3>
            <canvas id="lineChart"></canvas> <!-- Canvas for line chart -->
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer section with app name and developers -->
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


    <script>
    <?php if (!empty($top_hashtags)): ?>
        // Convert PHP variables to JavaScript
        const labels = <?= $labels ?>;
        const data = <?= $data ?>;

        // Define an array of color options
        const colors = [
            'rgba(255, 215, 0, 0.9)',    
            'rgba(238, 201, 0, 0.8)',    
            'rgba(255, 223, 70, 0.7)',   
            'rgba(218, 165, 32, 0.8)',   
            'rgba(255, 239, 153, 0.6)',  
            'rgba(255, 215, 0, 0.6)'     
        ];

        // Function to get a repeating set of colors based on data length
        function getColors(n) {
            const result = [];
            for(let i = 0; i < n; i++) {
                result.push(colors[i % colors.length]);
            }
            return result;
        }

        // Bar chart for hashtag counts
        new Chart(document.getElementById('barChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Hashtag Count',
                    data: data,
                    backgroundColor: getColors(data.length),
                    borderColor: 'rgba(0,0,0,0.7)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Pie chart for hashtag distribution
        new Chart(document.getElementById('pieChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: getColors(data.length)
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        // Doughnut chart for hashtag distribution
        new Chart(document.getElementById('doughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: getColors(data.length)
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        // Line chart for hashtag trends
        new Chart(document.getElementById('lineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Hashtag Count',
                    data: data,
                    fill: false,
                    borderColor: 'rgba(255, 215, 0, 1)',
                    backgroundColor: 'rgba(255, 215, 0, 0.7)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    <?php endif; ?>
</script>

</body>
</html>
