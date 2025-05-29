<?php
    // Function to establish a connection to the MySQL database
    function connection(){
        $host = 'localhost'; // Database host
        $username = 'root'; // Database username
        $password = ''; // Database password
        $database = 'whisper_db'; // Name of the database

        // Create a new MySQLi connection
        $con = new mysqli($host, $username, $password, $database);

        // Check if connection has an error
        if ($con->connect_error) {
            echo $con->connect_error; // Output connection error
        } else {
            return $con; // Return the connection object
        }
    }
?>
