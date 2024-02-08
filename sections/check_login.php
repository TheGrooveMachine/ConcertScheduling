<?php
    // Start the session
    session_name('login');
    session_start();

    // Check if the user is logged in
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
        // Access the username from the session and display it in an <h1> tag
        $username = $_SESSION['username'];
        $userData = $db->getUserByName($username);

        if (!empty($userData)) {
            $userId = $userData[0]['idattendee'];
        } else {
            echo "User data not found.";
        }
    } else {
        // Handle the case where the user is not logged in
        header('Location: login.php');
        session_unset();    // Unset all session variables
        session_destroy();  // Destroy the session data
        echo "You are not logged in.";
        exit;
    }
?>