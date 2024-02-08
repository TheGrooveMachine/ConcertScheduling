<?php
    // Start the session
    session_start();
    //Unset the Session
    unset($_SESSION['loggedIn']);


    // Redirect to login.php
    header("Location: login.php");
    exit;
?>
