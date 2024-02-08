<?php
    
    require_once("DB.class.php"); // Assuming this file exists and includes your database class
    $db = new DB();
    include 'sections/check_login.php';


    $userId = ""; // You need to set $userId based on your application logic

    include 'sections/db_connection.php';

    include 'MyUtils.php';

    $myUtils = new MyUtils();

    // Call the html_header function
    $header = $myUtils->html_header("Admin");



    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["registerAttendee"])) {
            // Check if the required fields are set
            $idevent = isset($_POST["idevent"]) ? $_POST["idevent"] : '';
            $idsession = isset($_POST["idsession"]) ? $_POST["idsession"] : '';
            $paid = isset($_POST["paid"]) ? $_POST["paid"] : '';

            $result1 = $db->registerAttendeeSession($idsession, $userId);
            $result2 = $db->registerAttendeeEvent($idevent, $userId, $paid);

            if ($result1) {
                echo "Registration successful.";
            } else {
                echo "Error: Registration1 failed.";
            }
            if ($result2) {
                echo "Registration successful.";
            } else {
                echo "Error: Registration2 failed.";
            }
        } elseif (isset($_POST["unregisterAttendee"])) {
            $idattendee = isset($_POST["idattendee"]) ? $_POST["idattendee"] : '';  // Check if 'idattendee' is set
            $idevent = isset($_POST["idevent"]) ? $_POST["idevent"] : '';  // Check if 'idattendee' is set
            $idsession = isset($_POST["idsession"]) ? $_POST["idsession"] : '';
            $paid = isset($_POST["idsession"]) ? $_POST["idsession"] : '';
            $result1 = $db->unregisterAttendeeSession($idsession, $userId);
            $result2 = $db->unregisterAttendeeEvent($idevent, $userId);
    
            if ($result1) {
                echo "Your Attendee was successfully added.";
            } else {
                echo "Error: Attendee addition failed.";
            }
            if ($result2) {
                echo "Your Attendee was successfully added.";
            } else {
                echo "Error: Attendee addition failed.";
            }
        }
    }
?>

        <?php
            echo $header;
        ?>
        <tab-container>
            <!-- TAB CONTROLS -->
            <input type="radio" id="tabToggle01" name="tabs" value="1" checked />
            <label for="tabToggle01" checked="checked">Events</label>
            <input type="radio" id="tabToggle02" name="tabs" value="2" />
            <label for="tabToggle02">Registrations</label>
            
            <tab-content>
                <?php
                $events = $db->getAllEvents();
                if ($events) {
                    foreach ($events as $event) {
                        echo "<div class='ag-courses_item'>
                                <a href='#' class='ag-courses-item_link'>
                                    <div class='ag-courses-item_bg'></div>
                                    <div class='ag-courses-item_title'>
                                        {$event['name']}
                                    </div>
                                    <div class='ag-courses-item_date-box'>
                                        Start:
                                        <span class='ag-courses-item_date'>
                                            {$event['datestart']}
                                        </span>
                                    </div>
                                </a>
                            </div>";
                    }
                } else {
                    echo "No events found.";
                }
                ?>
            </tab-content>
            <tab-content>
                <?php
                $events = $db->getEventNameForAttendee($userId);
                if (is_array($events) && !empty($events)) {
                    foreach ($events as $event) {
                        echo "<div class='ag-courses_item'>
                                <a href='#' class='ag-courses-item_link'>
                                    <div class='ag-courses-item_bg'></div>
                                    <div class='ag-courses-item_title'>
                                        {$event['name']}
                                    </div>
                                    <div class='ag-courses-item_date-box'>
                                        Start:
                                        <span class='ag-courses-item_date'>
                                            {$event['datestart']}
                                        </span>
                                    </div>
                                </a>
                            </div>";
                    }
                } else {
                    echo "No events found.";
                }
                ?>
            </tab-content>
        </tab-container>

        <div class="login">
            <h1>Register</h1>
            <form method="post" action="">
                <p>
                    <select name="idevent"> <!-- Use name in select tag -->
                        <?php
                        // Fetch and display all events
                        $allEvents = $db->getAllEvents();

                        foreach ($allEvents as $event) {
                            echo "<option value=\"{$event['idevent']}\">{$event['name']}</option>";
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <select name="idsession"> <!-- Use name in select tag -->
                        <?php
                        // Fetch and display all sessions
                        $allSessions = $db->getAllSessions();

                        foreach ($allSessions as $session) {
                            echo "<option value=\"{$session['idsession']}\">{$session['name']}</option>";
                        }
                        ?>
                    </select>
                </p>
                <p><input type="text" name="paid" value="" placeholder="paid?"></p>
                <p class="submit"><input type="submit" name="registerAttendee" value="Register Attendee"></p>
            </form>
        </div>
        <div class="login">
    <h1>Delete an Attendee</h1>
    <form method="post" action="" id="deleteAttendeeForm">
        <p>
            <select name="idevent" id="idevent">
                <option value="" selected>Select an event</option>
                <?php
                // Fetch and display events specific to the event manager
                $eventsForManager = $db->getEventByUserId($userId);

                foreach ($eventsForManager as $eventForManager) {
                    echo "<option value=\"{$eventForManager['idevent']}\">{$eventForManager['name']}</option>";
                }
                ?>
            </select>
        </p>
        <p>
            <select name="idsession" id="idsession">
                <option value="" selected>Select a session</option>
                <?php
                // Fetch and display events specific to the event manager
                $sessionsForManager = $db->getSessionByEventId($eventId);

                foreach ($sessionsForManager as $sessionForManager) {
                    echo "<option value=\"{$sessionForManager['idsession']}\">{$sessionForManager['name']}</option>";
                }
                ?>
            </select>
        </p>
        <p>
            <select name="idattendee">
                <option value="" selected>Select an attendee</option>
                <?php
                // Fetch and display all attendees
                $allAttendees = $db->getAllUsers();

                foreach ($allAttendees as $attendee) {
                    echo "<option value=\"{$attendee['idattendee']}\">{$attendee['name']}</option>";
                }
                ?>
            </select>
        </p>
        <p class="submit"><input type="submit" name="deleteAttendee" value="Delete Attendee"></p>
    </form>

</div>
<?php
$footer = $myUtils->html_footer("Thank you for visiting!");
  
// Output the footer
echo $footer;
?>