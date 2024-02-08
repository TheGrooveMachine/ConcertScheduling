<?php
    $servername = "localhost";
    $username = "eds5997";
    $password = "Records7^muraenidae";
    $dbname = "eds5997";

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>