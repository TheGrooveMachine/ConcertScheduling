<?php

class DB {
    private $conn;

    function __construct(){
        try {
            $dsn = "mysql:host={$_SERVER['DB_SERVER']};dbname={$_SERVER['DB']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
    
            $this->conn = new PDO($dsn, $_SERVER['DB_USER'], $_SERVER['DB_PASSWORD'], $options);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    

    // ************************************************************************************
    //                   Functions for Viewing/Adding/Editing/Deleting Events
    // ************************************************************************************

    //Function to get a list of all of the events
    function getAllEvents() {
        $data = [];
    
        // Prepare the SQL query
        $sql = "SELECT * FROM event";
    
        try {
            // Prepare the SQL statement
            $stmt = $this->conn->prepare($sql);
    
            // Execute the statement
            $stmt->execute();
    
            // Fetch all rows as an associative array
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Iterate through the results
            foreach ($results as $row) {
                // Sanitize data before adding it to the result array
                $idevent = filter_var($row['idevent'], FILTER_SANITIZE_NUMBER_INT);
                $name = filter_var($row['name'], FILTER_SANITIZE_STRING);
                $datestart = filter_var($row['datestart'], FILTER_SANITIZE_STRING);
                $dateend = filter_var($row['dateend'], FILTER_SANITIZE_STRING);
                $numberallowed = filter_var($row['numberallowed'], FILTER_SANITIZE_NUMBER_INT);
                $venue = filter_var($row['venue'], FILTER_SANITIZE_STRING);
    
                // Add the sanitized data to the result array
                $data[] = [
                    'idevent' => $idevent,
                    'name' => $name,
                    'datestart' => $datestart,
                    'dateend' => $dateend,
                    'numberallowed' => $numberallowed,
                    'venue' => $venue
                ];
            }
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return $data;
    }
    
    function getEventByUserId($userId) {
        $data = [];
    
        // Validate and sanitize user ID
        $userId = filter_var($userId, FILTER_VALIDATE_INT);
        if ($userId === false || $userId === null) {
            return $data; // Return empty array if the user ID is not valid
        }
    
        try {
            $sql = "SELECT event FROM manager_event WHERE manager = :userId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
    
            // Fetch all rows as an associative array
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Iterate through the results
            foreach ($results as $row) {
                // Now, let's fetch the event name for each event ID
                $eventDetails = $this->getEventDetailsById($row['event']);
                if (!empty($eventDetails)) {
                    $data[] = $eventDetails;
                }
            }
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return $data;
    }
    
    function getEventDetailsById($eventId) {
        $eventDetails = [];
    
        // Validate and sanitize event ID
        $eventId = filter_var($eventId, FILTER_VALIDATE_INT);
        if ($eventId === false || $eventId === null) {
            return $eventDetails; // Return empty array if the event ID is not valid
        }
    
        try {
            $sql = "SELECT * FROM event WHERE idevent = :eventId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
            $stmt->execute();
    
            // Fetch the result as an associative array
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Sanitize data before adding it to the result array
            $idevent = filter_var($row['idevent'], FILTER_SANITIZE_NUMBER_INT);
            $name = filter_var($row['name'], FILTER_SANITIZE_STRING);
            $datestart = filter_var($row['datestart'], FILTER_SANITIZE_STRING);
            $dateend = filter_var($row['dateend'], FILTER_SANITIZE_STRING);
            $numberallowed = filter_var($row['numberallowed'], FILTER_SANITIZE_NUMBER_INT);
            $venue = filter_var($row['venue'], FILTER_SANITIZE_STRING);
    
            // Add the sanitized data to the result array
            $eventDetails = [
                'idevent' => $idevent,
                'name' => $name,
                'datestart' => $datestart,
                'dateend' => $dateend,
                'numberallowed' => $numberallowed,
                'venue' => $venue
            ];
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return $eventDetails;
    }
    
    // Function to add an event
public function addEvent($name, $datestart, $dateend, $numberallowed, $venue) {
    // Validate and sanitize input data
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $datestart = filter_var($datestart, FILTER_SANITIZE_STRING);
    $dateend = filter_var($dateend, FILTER_SANITIZE_STRING);
    $numberallowed = filter_var($numberallowed, FILTER_VALIDATE_INT);
    $venue = filter_var($venue, FILTER_SANITIZE_STRING);

    // Check if any of the inputs are not valid
    if (empty($name) || empty($datestart) || empty($dateend) || $numberallowed === false || empty($venue)) {
        // Input data is not valid
        return false;
    }

    try {
        // Prepare the SQL query to insert the event
        $sql = "INSERT INTO event (name, datestart, dateend, numberallowed, venue) VALUES (:name, :datestart, :dateend, :numberallowed, :venue)";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Check if the statement preparation was successful
        if ($stmt === false) {
            // Handle the prepare statement error
            error_log("Prepare statement error: " . $this->conn->error);
            return false;
        }

        // Bind the parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':datestart', $datestart, PDO::PARAM_STR);
        $stmt->bindParam(':dateend', $dateend, PDO::PARAM_STR);
        $stmt->bindParam(':numberallowed', $numberallowed, PDO::PARAM_INT);
        $stmt->bindParam(':venue', $venue, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            // Event added successfully
            return true;
        } else {
            // Error occurred while adding the event
            error_log("SQL execution error: " . $stmt->error);
            // Provide a generic error message for the user
            echo "Error: Unable to add the event. Please try again later.";
            return false;
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
        return false;
    }
}

// Function to delete an event
public function deleteEvent($id) {
    // Validate and sanitize input data
    $id = filter_var($id, FILTER_VALIDATE_INT);

    // Check if the input is not valid
    if ($id === false) {
        // Input data is not valid
        return 0;
    }

    try {
        $numRows = 0;
        $query = "DELETE FROM event WHERE idevent = :id";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($query);

        // Check if the statement preparation was successful
        if ($stmt === false) {
            // Handle the prepare statement error
            error_log("Prepare statement error: " . $this->conn->error);
            return 0;
        }

        // Bind the parameters
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Get the number of affected rows
        $numRows = $stmt->rowCount();

        // Close the statement
        return $numRows;
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
        return 0;
    }
}

// Function to update an event
public function updateEvent($name, $datestart, $dateend, $numberallowed, $venue, $idevent) {
    // Validate and sanitize input data
    $idevent = filter_var($idevent, FILTER_VALIDATE_INT);

    // Check if the input is not valid
    if ($idevent === false || empty($name) || empty($datestart) || empty($dateend) || !is_numeric($numberallowed) || empty($venue)) {
        // Input data is not valid
        return false;
    }

    try {
        // Prepare the SQL query to update the event
        $sql = "UPDATE event SET name = :name, datestart = :datestart, dateend = :dateend, numberallowed = :numberallowed, venue = :venue WHERE idevent = :idevent";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Check if the statement preparation was successful
        if ($stmt === false) {
            // Handle the prepare statement error
            error_log("Prepare statement error: " . $this->conn->error);
            return false;
        }

        // Bind the parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':datestart', $datestart, PDO::PARAM_STR);
        $stmt->bindParam(':dateend', $dateend, PDO::PARAM_STR);
        $stmt->bindParam(':numberallowed', $numberallowed, PDO::PARAM_INT);
        $stmt->bindParam(':venue', $venue, PDO::PARAM_STR);
        $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Event updated successfully
            return true;
        } else {
            // Error occurred while updating the event
            error_log("SQL execution error: " . $stmt->error);
            // Provide a generic error message for the user
            echo "Error: Unable to update the event. Please try again later.";
            return false;
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
        return false;
    }
}


    
    

    // ************************************************************************************
    //                   Functions for Adding/Editing/Deleting Users
    // ************************************************************************************

    //function to get all the users
    // Function to get all the users
function getAllUsers() {
    $data = [];

    try {
        $sql = "SELECT * FROM attendee";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        // Fetch all rows as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Iterate through the results
        foreach ($results as $row) {
            // Sanitize and validate the data
            $idattendee = filter_var($row['idattendee'], FILTER_VALIDATE_INT);
            $name = filter_var($row['name'], FILTER_SANITIZE_STRING);
            $password = filter_var($row['password'], FILTER_SANITIZE_STRING);
            $role = filter_var($row['role'], FILTER_SANITIZE_STRING);

            // Check if any of the data is not valid
            if ($idattendee === false || $name === false || $password === false || $role === false) {
                // Invalid data found, skip this record
                continue;
            }

            $data[] = [
                'idattendee' => $idattendee,
                'name' => $name,
                'password' => $password,
                'role' => $role,
            ];
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    return $data;
}

// Function to get a user by name
function getUserByName($userName) {
    $data = [];

    // Validate and sanitize the input parameter
    $userName = filter_var($userName, FILTER_SANITIZE_STRING);

    // Check if the sanitized data is not valid
    if ($userName === false) {
        // Invalid data found
        return $data;
    }

    try {
        $sql = "SELECT * FROM attendee WHERE name = :userName";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch all rows as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Iterate through the results
        foreach ($results as $row) {
            $data[] = [
                'idattendee' => $row['idattendee'],
                'name' => $row['name'],
                'password' => $row['password'],
                'role' => $row['role'],
            ];
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    return $data;
}

// Function to add users
// Function to add a user
function addUser($name, $password, $role) {
    // Validate input data (you can add more specific validation as needed)
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    $role = filter_var($role, FILTER_SANITIZE_STRING);

    // Check if the sanitized data is not valid
    if (empty($name) || empty($password) || empty($role)) {
        // Input data is not valid
        return false;
    }

    try {
        // Hash the password using SHA-256
        $hashedPassword = hash('sha256', $password);

        // Prepare the SQL query to insert a new user
        $sql = "INSERT INTO attendee (name, password, role) VALUES (:name, :password, :role)";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            // User added successfully
            $stmt->closeCursor();
            return true;
        } else {
            // Error occurred while executing the query
            error_log("SQL execution error: " . $stmt->error);
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    // Error occurred while adding the user
    return false;
}


// Function to update a user
function updateUser($idattendee, $name, $password, $role) {
    // Validate input data (you can add more specific validation as needed)
    $idattendee = filter_var($idattendee, FILTER_VALIDATE_INT);
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $role = filter_var($role, FILTER_SANITIZE_STRING);

    // Check if the sanitized data is not valid
    if (empty($name) || empty($role) || $idattendee === false) {
        // Input data is not valid
        return false;
    }

    try {
        // Check if a new password is provided
        if (!empty($password)) {
            // Hash the new password using PHP's password_hash function
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the SQL query to update the user with a new password
            $sql = "UPDATE attendee SET name = :name, password = :password, role = :role WHERE idattendee = :idattendee";
        } else {
            // Prepare the SQL query to update the user without changing the password
            $sql = "UPDATE attendee SET name = :name, role = :role WHERE idattendee = :idattendee";
        }

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':idattendee', $idattendee, PDO::PARAM_INT);

        // Bind the hashed password if provided
        if (!empty($password)) {
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        }

        // Execute the query
        if ($stmt->execute()) {
            // User updated successfully
            $stmt->close();
            return true;
        } else {
            // Error occurred while executing the query
            error_log("SQL execution error: " . $stmt->error);
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    // Error occurred while updating the user
    return false;
}

// Function to delete a user
function deleteUser($idattendee) {
    // Validate input data (you can add more specific validation as needed)
    $idattendee = filter_var($idattendee, FILTER_VALIDATE_INT);

    // Check if the sanitized data is not valid
    if ($idattendee === false) {
        // Input data is not valid
        return false;
    }

    try {
        // Prepare the SQL query to delete the user
        $sql = "DELETE FROM attendee WHERE idattendee = :idattendee";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameter
        $stmt->bindParam(':idattendee', $idattendee, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // User deleted successfully
            $stmt->close();
            return true;
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    // Error occurred while deleting the user
    return false;
}


    // ************************************************************************************
    //                   Functions for Adding/Editing/Deleting Sessions
    // ************************************************************************************

    //function to get all the sessions
    // Function to get all sessions
    function getAllSessions() {
        $data = [];
    
        try {
            $sql = "SELECT * FROM session";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($result as $row) {
                $data[] = [
                    'idsession' => $row['idsession'],
                    'name' => $row['name'],
                    'numberallowed' => $row['numberallowed'],
                    'event' => $row['event'],
                    'startdate' => $row['startdate'],
                    'enddate' => $row['enddate']
                ];
            }
    
            // Set the statement to null to close it
            $stmt = null;
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return $data;
    }
    

// Function to get sessions by event ID
public function getSessionByEventId($eventId) {
    $data = [];

    // Validate input data
    $eventId = filter_var($eventId, FILTER_VALIDATE_INT);

    try {
        $sql = "SELECT * FROM session WHERE event = :eventId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $data[] = [
                'idsession' => $row['idsession'],
                'name' => $row['name'],
                'numberallowed' => $row['numberallowed'],
                'event' => $row['event'],
                'startdate' => $row['startdate'],
                'enddate' => $row['enddate']
            ];
        }

        // Set the statement to null to close it
        $stmt = null;
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    return $data;
}


// Function to get sessions and return as JSON
public function getSessions($idevent) {
    // Assuming you have a method in your class to get sessions by event ID
    $sessions = $this->getSessionByEventId($idevent);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($sessions);
    exit;
}

// Function to add a session
// Function to add a session
public function addSession($name, $numberallowed, $event, $startdate, $enddate) {
    // Sanitize and validate input data
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $numberallowed = filter_var($numberallowed, FILTER_VALIDATE_INT);
    $event = filter_var($event, FILTER_SANITIZE_STRING);
    $startdate = filter_var($startdate, FILTER_SANITIZE_STRING);
    $enddate = filter_var($enddate, FILTER_SANITIZE_STRING);

    if (empty($name) || empty($event) || empty($startdate) || empty($enddate) || $numberallowed === false || $numberallowed < 0) {
        // Input data is not valid
        return false;
    }

    try {
        // Prepare the SQL query to insert a new session
        $sql = "INSERT INTO session (name, numberallowed, event, startdate, enddate) VALUES (:name, :numberallowed, :event, :startdate, :enddate)";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':numberallowed', $numberallowed, PDO::PARAM_INT);
        $stmt->bindParam(':event', $event, PDO::PARAM_STR);
        $stmt->bindParam(':startdate', $startdate, PDO::PARAM_STR);
        $stmt->bindParam(':enddate', $enddate, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            // Session added successfully
            return true;
        } else {
            // Error occurred while executing the query
            error_log("SQL execution error: " . $stmt->error);
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    // Error occurred while adding the session
    return false;
}

// Function to update a session
public function updateSession($idsession, $name, $numberallowed, $event, $startdate, $enddate) {
    // Sanitize and validate input data
    $idsession = filter_var($idsession, FILTER_VALIDATE_INT);
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $numberallowed = filter_var($numberallowed, FILTER_VALIDATE_INT);
    $event = filter_var($event, FILTER_SANITIZE_STRING);
    $startdate = filter_var($startdate, FILTER_SANITIZE_STRING);
    $enddate = filter_var($enddate, FILTER_SANITIZE_STRING);

    // Validate input data
    if (!is_numeric($idsession) || empty($name) || empty($event) || empty($startdate) || empty($enddate) || $numberallowed === false || $numberallowed < 0) {
        // Input data is not valid
        return false;
    }

    try {
        // Prepare the SQL query to update the session
        $sql = "UPDATE session SET name = :name, numberallowed = :numberallowed, event = :event, startdate = :startdate, enddate = :enddate WHERE idsession = :idsession";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':numberallowed', $numberallowed, PDO::PARAM_INT);
        $stmt->bindParam(':event', $event, PDO::PARAM_STR);
        $stmt->bindParam(':startdate', $startdate, PDO::PARAM_STR);
        $stmt->bindParam(':enddate', $enddate, PDO::PARAM_STR);
        $stmt->bindParam(':idsession', $idsession, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Session updated successfully
            return true;
        } else {
            // Error occurred while executing the query
            error_log("SQL execution error: " . $stmt->error);
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    // Error occurred while updating the session
    return false;
}

// Function to delete a session
public function deleteSession($idsession) {
    // Sanitize and validate input data
    $idsession = filter_var($idsession, FILTER_VALIDATE_INT);

    // Validate input data
    if (!is_numeric($idsession)) {
        // Input data is not valid
        return false;
    }

    try {
        // Prepare the SQL query to delete the session
        $sql = "DELETE FROM session WHERE idsession = :idsession";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameter
        $stmt->bindParam(':idsession', $idsession, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Session deleted successfully
            return true;
        }
    } catch (PDOException $e) {
        // Handle any exceptions
        error_log("PDO Error: " . $e->getMessage());
    }

    // Error occurred while deleting the session
    return false;
}


    


    // ************************************************************************************
    //                   Functions for Adding/Editing/Deleting Venues
    // ************************************************************************************

    //function to get all of the venues
    function getAllVenues() {
        $data = [];
    
        try {
            $sql = "SELECT * FROM venue";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($result as $row) {
                $data[] = [
                    'idvenue' => $row['idvenue'],
                    'name' => $row['name'],
                    'capacity' => $row['capacity']
                ];
            }
    
            // Set the statement to null to close it
            $stmt = null;
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return $data;
    }
    
    
    // Function to add a venue
    function addVenue($name, $capacity) {
        // Sanitize and validate input data
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $capacity = filter_var($capacity, FILTER_VALIDATE_INT);
    
        // Validate input data
        if (empty($name) || $capacity === false || $capacity < 0) {
            // Input data is not valid
            return false;
        }
    
        try {
            // Prepare the SQL query to insert a new venue
            $sql = "INSERT INTO venue (name, capacity) VALUES (?, ?)";
    
            // Prepare the SQL statement
            $stmt = $this->conn->prepare($sql);
    
            // Bind the parameters
            $stmt->bindParam(1, $name, PDO::PARAM_STR);
            $stmt->bindParam(2, $capacity, PDO::PARAM_INT);
    
            // Execute the query
            if ($stmt->execute()) {
                // Venue added successfully
                $stmt->close();
                return true;
            } else {
                // Error occurred while executing the query
                error_log("SQL execution error: " . $stmt->error);
            }
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        // Error occurred while adding the venue
        return false;
    }
    
    // Function to delete a venue
    function deleteVenue($idvenue) {
        // Sanitize and validate input data
        $idvenue = filter_var($idvenue, FILTER_VALIDATE_INT);
    
        // Validate input data
        if ($idvenue === false || $idvenue < 0) {
            // Input data is not valid
            return false;
        }
    
        try {
            // Prepare the SQL query to delete the venue
            $sql = "DELETE FROM venue WHERE idvenue = ?";
    
            // Prepare the SQL statement
            $stmt = $this->conn->prepare($sql);
    
            // Bind the parameter
            $stmt->bindParam(1, $idvenue, PDO::PARAM_INT);
    
            // Execute the query
            if ($stmt->execute()) {
                // Venue deleted successfully
                $stmt->close();
                return true;
            }
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        // Error occurred while deleting the venue
        return false;
    }
    
    // Function to update a venue
    function updateVenue($idvenue, $name, $capacity) {
        // Sanitize and validate input data
        $idvenue = filter_var($idvenue, FILTER_VALIDATE_INT);
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $capacity = filter_var($capacity, FILTER_VALIDATE_INT);
    
        // Validate input data
        if (empty($name) || $idvenue === false || $capacity === false || $capacity < 0) {
            // Input data is not valid
            return false;
        }
    
        try {
            // Prepare the SQL query to update the venue
            $sql = "UPDATE venue SET name = ?, capacity = ? WHERE idvenue = ?";
    
            // Prepare the SQL statement
            $stmt = $this->conn->prepare($sql);
    
            // Bind the parameters
            $stmt->bindParam(1, $name, PDO::PARAM_STR);
            $stmt->bindParam(2, $capacity, PDO::PARAM_INT);
            $stmt->bindParam(3, $idvenue, PDO::PARAM_INT);
    
            // Execute the query
            if ($stmt->execute()) {
                // Venue updated successfully
                $stmt->close();
                return true;
            } else {
                // Error occurred while executing the query
                error_log("SQL execution error: " . $stmt->error);
            }
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        // Error occurred while updating the venue
        return false;
    }
    
    // Function to get attendees with names for a session
    function getAttendeesWithNamesForSession($sessionId) {
        $attendeesWithNames = [];
    
        try {
            if ($stmt = $this->conn->prepare("SELECT attendee FROM attendee_session WHERE session = ?")) {
                $stmt->bindParam(1, $sessionId, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($attendeeId);
    
                if ($stmt->num_rows > 0) {
                    while ($stmt->fetch()) {
                        // Now, let's fetch the event name for each event ID
                        $attendeeName = $this->getAttendeeNameById($attendeeId);
                        if (!empty($attendeeName)) {
                            $attendeesWithNames[] = [
                                'idattendee' => $attendeeId,
                                'name' => $attendeeName,
                            ];
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return $attendeesWithNames;
    }
    
    // Function to get attendee name by ID
    function getAttendeeNameById($attendeeId) {
        try {
            // Fetch attendee name from your attendees table based on the ID
            $sql = "SELECT name FROM attendee WHERE idattendee = ?";
    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(1, $attendeeId, PDO::PARAM_INT);
            $stmt->execute();
    
            $attendeeName = '';
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $attendeeName = $result['name'];
            }
    
            $stmt->close();
    
            return $attendeeName;
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return '';
    }
    
    // Function to get attendees by event ID
    public function getAttendeesByEventId($eventId) {
        try {
            // Fetch attendee IDs for a specific event from the attendee_event table
            $sqlAttendees = "SELECT attendee FROM attendee_event WHERE event = :eventId";
    
            $stmtAttendees = $this->conn->prepare($sqlAttendees);
            $stmtAttendees->bindParam(':eventId', $eventId, PDO::PARAM_INT);
            $stmtAttendees->execute();
    
            $attendeeIds = array();
            if ($stmtAttendees->rowCount() > 0) {
                while ($result = $stmtAttendees->fetch(PDO::FETCH_ASSOC)) {
                    $attendeeIds[] = $result['attendee'];
                }
            }
    
            // Close the statement
            $stmtAttendees = null;
    
            // Fetch attendee names using the attendee IDs
            $attendees = array();
            foreach ($attendeeIds as $attendeeId) {
                $sqlAttendeeName = "SELECT name FROM attendee WHERE idattendee = :attendeeId";
    
                $stmtAttendeeName = $this->conn->prepare($sqlAttendeeName);
                $stmtAttendeeName->bindParam(':attendeeId', $attendeeId, PDO::PARAM_INT);
                $stmtAttendeeName->execute();
    
                $attendeeName = '';
                if ($stmtAttendeeName->rowCount() > 0) {
                    $result = $stmtAttendeeName->fetch(PDO::FETCH_ASSOC);
                    $attendeeName = $result['name'];
                }
    
                // Close the statement
                $stmtAttendeeName = null;
    
                $attendees[] = array(
                    'idattendee' => $attendeeId,
                    'name' => $attendeeName
                );
            }
    
            return $attendees;
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return array();
    }
    
    
    function getEventNameForAttendee($attendeeId) {
        $data = [];
    
        try {
            $sql = "SELECT event FROM attendee_event WHERE attendee = :attendeeId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':attendeeId', $attendeeId, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($result as $row) {
                $eventId = $row['event'];
                // Now, let's fetch the event name for each event ID
                $eventDetails = $this->getEventDetailsById($eventId);
                if (!empty($eventDetails)) {
                    $data[] = $eventDetails;
                }
            }
    
            // Set the statement to null to close it
            $stmt = null;
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return $data;
    }
    
    
    // Function to register attendee for a session
    function registerAttendeeSession($idsession, $idattendee) {
        try {
            // Prepare the SQL query for session
            $sqlSession = "INSERT INTO attendee_session (session, attendee) VALUES (?, ?)";
    
            // Prepare the SQL statement for session
            $stmtSession = $this->conn->prepare($sqlSession);
    
            if (!$stmtSession) {
                // Error occurred while preparing the session statement
                return false;
            }
    
            // Bind the parameters for session
            $stmtSession->bindParam(1, $idsession, PDO::PARAM_INT);
            $stmtSession->bindParam(2, $idattendee, PDO::PARAM_INT);
    
            // Execute the session query
            $stmtSession->execute();
    
            // Check if the query executed successfully
            if ($stmtSession->rowCount() > 0) {
                // User registered successfully for session
                $stmtSession->close();
                return true;
            }
    
            // Error occurred while registering user for session
            $stmtSession->close();
            return false;
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return false;
    }
    
    // Function to register attendee for an event
    function registerAttendeeEvent($idevent, $idattendee, $paid) {
        try {
            // Prepare the SQL query for event
            $sqlEvent = "INSERT INTO attendee_event (event, attendee, paid) VALUES (?, ?, ?)";
    
            // Prepare the SQL statement for event
            $stmtEvent = $this->conn->prepare($sqlEvent);
    
            if (!$stmtEvent) {
                // Error occurred while preparing the event statement
                return false;
            }
    
            // Bind the parameters for event
            $stmtEvent->bindParam(1, $idevent, PDO::PARAM_INT);
            $stmtEvent->bindParam(2, $idattendee, PDO::PARAM_INT);
            $stmtEvent->bindParam(3, $paid, PDO::PARAM_INT);
    
            // Execute the event query
            $stmtEvent->execute();
    
            // Check if the query executed successfully
            if ($stmtEvent->rowCount() > 0) {
                // User registered successfully for event
                $stmtEvent->close();
                return true;
            }
    
            // Error occurred while registering user for event
            $stmtEvent->close();
            return false;
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return false;
    }
    
    // Function to unregister attendee from a session
    function unregisterAttendeeSession($idsession, $idattendee) {
        try {
            // Prepare the SQL query for deleting the attendee from the session
            $sqlSession = "DELETE FROM attendee_session WHERE session = ? AND attendee = ?";
    
            // Prepare the SQL statement for deleting the attendee from the session
            $stmtSession = $this->conn->prepare($sqlSession);
    
            if (!$stmtSession) {
                // Error occurred while preparing the session statement
                return false;
            }
    
            // Bind the parameters for deleting the attendee from the session
            $stmtSession->bindParam(1, $idsession, PDO::PARAM_INT);
            $stmtSession->bindParam(2, $idattendee, PDO::PARAM_INT);
    
            // Execute the session deletion query
            $stmtSession->execute();
    
            // Check if the query executed successfully
            if ($stmtSession->rowCount() > 0) {
                // User unregistered successfully from the session
                $stmtSession->close();
                return true;
            }
    
            // Error occurred while unregistering user from the session
            $stmtSession->close();
            return false;
        } catch (PDOException $e) {
            // Handle any exceptions
            error_log("PDO Error: " . $e->getMessage());
        }
    
        return false;
    }
    
    function unregisterAttendeeEvent($idevent, $idattendee) {
        // Prepare the SQL query for deleting the attendee from the event
        $sqlEvent = "DELETE FROM attendee_event WHERE event = ? AND attendee = ?";
        
        // Prepare the SQL statement for deleting the attendee from the event
        $stmtEvent = $this->conn->prepare($sqlEvent);
        
        if (!$stmtEvent) {
            // Error occurred while preparing the event statement
            return false;
        }
        
        // Bind the parameters for deleting the attendee from the event
        $stmtEvent->bind_param("ii", $idevent, $idattendee);
        
        // Execute the event deletion query
        $stmtEvent->execute();
        
        // Check if the query executed successfully
        if ($stmtEvent->affected_rows > 0) {
            // User unregistered successfully from the event
            $stmtEvent->close();
            return true;
        }
        
        // Error occurred while unregistering user from the event
        $stmtEvent->close();
        return false;
    }
    
}
?>
