<?php
require_once("DB.class.php");
$db = new DB();
include 'sections/db_connection.php';
include 'MyUtils.php';

$myUtils = new MyUtils();
$header = $myUtils->html_header("Register");

// Process the registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"]; // Added role input

    // Validate input data (you can add more specific validation as needed)
    if (empty($username) || empty($password) || empty($role)) {
        echo "Please fill out all fields.";
    } else {
        // Check if the username is already taken
        $existingUser = $db->getUserByName($username);
        if ($existingUser) {
            echo "Username already taken. Please choose a different one.";
        } else {
            // Insert the new user into the database
            $success = $db->addUser($username, $password, $role);

            if ($success) {
                // Redirect to the login page after successful registration
                header("Location: login.php");
                exit();
            } else {
                echo "Error occurred during registration.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="assets/styles.css" />
    <title>Login</title>
</head>
<body>
    <div class="login">
        <h1>Register an account</h1>
        <form method="post" action="submit">
            <p><input type="text" name="username" value="" placeholder="Username or Email"></p>
            <p><input type="password" name="password" value="" placeholder="Password"></p>
            <p>
                <select name="role">
                    <option value="" selected>Select a Role</option>
                    <option value="1">Admin</option>
                    <option value="2">Attendee</option>
                    <option value="3">Event Manager</option>
                </select>
            </p>
            <p class="submit"><input type="submit" name="commit" value="Register"></p>
        </form>
        <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>
</body>
</html>
