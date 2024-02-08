<?php


require_once("DB.class.php"); // Assuming this file exists and includes your database class
$db = new DB();


$userId = "";

include 'sections/db_connection.php';

include 'MyUtils.php';
$myUtils = new MyUtils();

// Call the html_header function
$header = $myUtils->html_header("Admin");
include 'sections/check_login.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["deleteEvent"])) {
        $idevent = isset($_POST["idevent"]) ? $_POST["idevent"] : '';
        $result = $db->deleteEvent($idevent);

        if ($result) {
            echo "Event successfully deleted.";
        } else {
            echo "Error: Event deletion failed.";
        }

    } elseif (isset($_POST["addEvent"])) {
        $name = $_POST['name'];
        $datestart = $_POST['datestart'];
        $dateend = $_POST["dateend"];
        $numberallowed = $_POST['numberallowed'];
        $venue = $_POST["venue"];

        $result = $db->addEvent($name, $datestart, $dateend, $numberallowed, $venue);

        if ($result) {
            echo "Your event was successfully added.";
        } else {
            echo "Error: Event addition failed.";
        }
    } elseif (isset($_POST["editEvent"])) {
        $idevent = $_POST['idevent'];
        $name = $_POST['name'];
        $datestart = $_POST['datestart'];
        $dateend = $_POST["dateend"];
        $numberallowed = $_POST['numberallowed'];
        $venue = $_POST["venue"];

        $result = $db->updateEvent($name, $datestart, $dateend, $numberallowed, $venue, $idevent);

        if ($result) {
            echo "Your event was successfully updated.";
        } else {
            echo "Error: Event update failed.";
        }
    } elseif (isset($_POST["deleteAttendee"])) {
        $idattendee = isset($_POST["idattendee"]) ? $_POST["idattendee"] : '';  // Check if 'idattendee' is set
        $idevent = isset($_POST["idevent"]) ? $_POST["idevent"] : '';  // Check if 'idattendee' is set
        $idsession = isset($_POST["idsession"]) ? $_POST["idsession"] : '';
        $result1 = $db->unregisterAttendeeSession($idsession, $userId);
        $result2 = $db->unregisterAttendeeEvent($idevent, $userId);

        if ($result1) {
            echo "Attendee successfully deleted.";
        } else {
            echo "Error: Attendee deletion failed.";
        }
        if ($result2) {
            echo "Attendee successfully deleted.";
        } else {
            echo "Error: Attendee deletion failed.";
        }

    } elseif (isset($_POST["addAttendee"])) {
        $idattendee = isset($_POST["idattendee"]) ? $_POST["idattendee"] : '';  // Check if 'idattendee' is set
        $idevent = isset($_POST["idevent"]) ? $_POST["idevent"] : '';  // Check if 'idattendee' is set
        $idsession = isset($_POST["idsession"]) ? $_POST["idsession"] : '';
        $paid = isset($_POST["idsession"]) ? $_POST["idsession"] : '';
        $result1 = $db->registerAttendeeSession($idsession, $userId);
        $result2 = $db->registerAttendeeEvent($idevent, $userId, $paid);

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
    } elseif (isset($_POST["editAttendee"])) {
        $idattendee = $_POST['idattendee'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $role = $_POST['role'];


        $result = $db->updateUser($idattendee, $name, $password, $role);

        if ($result) {
            echo "Your Attendee was successfully updated.";
        } else {
            echo "Error: Attendee update failed.";
        }
    } elseif (isset($_POST["deleteSession"])) {
        $idsession = isset($_POST["idsession"]) ? $_POST["idsession"] : '';  // Check if 'event' is set

        $result = $db->deleteSession($idsession);

        if ($result) {
            echo "Session successfully deleted.";
        } else {
            echo "Error: Session deletion failed.";
        }

    } elseif (isset($_POST["addSession"])) {
        $name = $_POST['name'];
        $numberallowed = $_POST['numberallowed'];
        $event = isset($_POST["event"]) ? $_POST["event"] : '';  // Check if 'event' is set
        $startdate = isset($_POST["startdate"]) ? $_POST["startdate"] : '';  // Check if 'startdate' is set
        $enddate = isset($_POST["enddate"]) ? $_POST["enddate"] : '';      // Check if 'enddate' is set


        $result = $db->addSession($name, $numberallowed, $event, $startdate, $enddate);

        if ($result) {
            echo "Your session was successfully added.";
        } else {
            echo "Error: session addition failed.";
        }
    } elseif (isset($_POST["editSession"])) {
        $idsession = isset($_POST["idsession"]) ? $_POST["idsession"] : '';  // Check if 'event' is set
        $name = $_POST['name'];
        $numberallowed = $_POST['numberallowed'];
        $event = $_POST["event"];
        $startdate = $_POST['startdate'];
        $enddate = $_POST["enddate"];

        $result = $db->updateSession($idsession, $name, $numberallowed, $event, $startdate, $enddate);

        if ($result) {
            echo "Your session was successfully updated.";
        } else {
            echo "Error: Session update failed.";
        }
    }
}
?>

    <?php
        echo $header
    ?>
    <tab-container>
        <!-- TAB CONTROLS -->
        <input type="radio" id="tabToggle01" name="tabs" value="1" checked />
        <label for="tabToggle01" checked="checked">Events</label>
        <input type="radio" id="tabToggle02" name="tabs" value="2" />
        <label for="tabToggle02">Sessions</label>
        <input type="radio" id="tabToggle03" name="tabs" value="3" />
        <label for="tabToggle03">Attendees</label>
        
        <tab-content>
            <?php
            echo"{$userId}";
                $events = $db->getEventByUserId($userId);
                if (!empty($events)) {
                    $eventId = $events[0]['idevent'];
                    // Rest of your code
                } else {
                    echo "No events found.";
                }                if ($events) {
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
                $sessions = $db->getSessionByEventId($eventId);
                $sessionId = $sessions[0]["idsession"];
                if ($sessions) {
                    foreach ($sessions as $session) {
                        echo "<div class='ag-courses_item'>
                                <a href='#' class='ag-courses-item_link'>
                                    <div class='ag-courses-item_bg'></div>
                                    <div class='ag-courses-item_title'>
                                        {$session['name']}
                                    </div>
                                    <div class='ag-courses-item_title'>
                                        Event: {$event['name']} 
                                    </div>
                                    <div class='ag-courses-item_date-box'>
                                        Start:
                                    </div>
                                </a>
                            </div>";
                    }
                } else {
                    echo "No sessions found.";
                }
            ?>
        </tab-content>
        <tab-content>
        <?php
                // Fetch and display attendees specific to the event manager's events
                $eventsForManager = $db->getEventByUserId($userId);

                foreach ($eventsForManager as $eventForManager) {
                    $eventId = $eventForManager['idevent'];
                    $attendeesForEvent = $db->getAttendeesByEventId($eventId);

                    foreach ($attendeesForEvent as $attendeeForEvent) {
                        echo "<div class='ag-courses_item'>
                            <a href='#' class='ag-courses-item_link'>
                                <div class='ag-courses-item_bg'></div>
                                <div class='ag-courses-item_title'>
                                    {$attendeeForEvent['name']}
                                </div>
                                <p>{$attendeeForEvent['idattendee']}</p>
                                <div class='ag-courses-item_title'>
                                    Event: {$attendeeForEvent['idattendee']}
                                </div>
                                <div class='ag-courses-item_date-box'>
                                    Start:
                                </div>
                            </a>
                        </div>";
                    }
                }
?>


        </tab-content>
    </tab-container>

    <div class="controlsContainer">
        <div class="adminControls">
            <div class="login">
                <h1>Delete an event</h1>
                <form method="post" action="">
                    <p>
                        <select name="idevent"> <!-- Use name in select tag -->
                            <?php
                            // Fetch and display events specific to the event manager
                            $eventsForManager = $db->getEventByUserId($userId);
                            if ($eventsForManager) {
                                foreach ($eventsForManager as $eventForManager) {
                                    echo "<option value=\"{$eventForManager['idevent']}\">{$eventForManager['name']}</option>";
                                }
                            } else {
                                echo "<option value=\"\">No events found</option>";
                            }
                            ?>
                        </select>
                    </p>
                    <p class="submit"><input type="submit" name="deleteEvent" value="Delete Event"></p>
                </form>
            </div>


            <div class="login">
                <h1>Edit an event</h1>
                <form method="post" action="">
                    <p>
                        <select name="idevent"> <!-- Use name in select tag -->
                            <?php
                            // Fetch and display events specific to the event manager
                            $eventsForManager = $db->getEventByUserId($userId);
                            if ($eventsForManager) {
                                foreach ($eventsForManager as $eventForManager) {
                                    echo "<option value=\"{$eventForManager['idevent']}\">{$eventForManager['name']}</option>";
                                }
                            } else {
                                echo "<option value=\"\">No events found</option>";
                            }
                            ?>
                        </select>
                    </p>
                    <p><input type="text" name="name" value="" placeholder="Event name"></p>
                    <p><input type="text" name="datestart" value="" placeholder="Start date"></p>
                    <p><input type="text"  name="dateend" value="" placeholder="End date"></p>
                    <p><input type="text" name="numberallowed" value="" placeholder="Number allowed"></p>
                    <p><input type="text" name="venue" value="" placeholder="Venue"></p>
                    <p class="submit"><input type="submit" name="editEvent" value="Edit Event"></p>
                </form>
            </div>

            <div class="login">
                <h1>Add an event</h1>
                <form method="post" action="">
                    <p><input type="text" name="name" value="" placeholder="Event name"></p>
                    <p><input type="text" name="datestart" value="" placeholder="Start date"></p>
                    <p><input type="text" name="dateend" value="" placeholder="End date"></p>
                    <p><input type="text" name="numberallowed" value="" placeholder="Number allowed"></p>
                    <p><input type="text" name="venue" value="" placeholder="Venue"></p>
                    <p class="submit"><input type="submit" name="addEvent" value="Add Event"></p>
                </form>
            </div> 
        </div>
        <div class="adminControls">
            <div class="login">
                    <h1>Add an Attendee</h1>
                    <form method="post" action="">
                        <p>
                            <select name="idevent">
                                <option value="" disabled selected>Select an event</option>
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
                            <select name="idattendee">
                                <option value="" disabled selected>Select an attendee</option>
                                <?php

                                $eventsForManager = $db->getEventByUserId($userId);

                                            foreach ($eventsForManager as $eventForManager) {
                                                $eventId = $eventForManager['idevent'];
                                                $attendeesForEvent = $db->getAttendeesByEventId($eventId);

                                                foreach ($attendeesForEvent as $attendeeForEvent) {
                                                    echo "<option value=\"{$attendeeForEvent['idattendee']}\">{$attendeeForEvent['name']}</option>";
                                                }
                                            }
                                ?>
                            </select>
                        </p>
                        <p class="submit"><input type="submit" name="addAttendee" value="Delete Attendee"></p>
                    </form>
                </div>

            <div class="login">
                <h1>Edit an Attendee</h1>
                <form method="post" action="">
                    <p>
                        <select name="idattendee"> <!-- Use name in select tag -->
                            <?php
                            // Fetch and display attendees specific to the event manager's events
                            $eventsForManager = $db->getEventByUserId($userId);

                            foreach ($eventsForManager as $eventForManager) {
                                $eventId = $eventForManager['idevent'];
                                $attendeesForEvent = $db->getAttendeesByEventId($eventId);

                                foreach ($attendeesForEvent as $attendeeForEvent) {
                                    echo "<option value=\"{$attendeeForEvent['idattendee']}\">{$attendeeForEvent['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </p>
                    <p><input type="text" name="name" value="" placeholder="Attendee name"></p>
                    <p><input type="text" name="password" value="" placeholder="password"></p>
                    <p><input type="text" name="role" value="" placeholder="role"></p>
                    <p class="submit"><input type="submit" name="editVenue" value="Edit Attendee"></p>
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


        </div>
        <div class="adminControls">
            <div class="login">
                <h1>Delete a Session</h1>
                <form method="post" action="">
                    <p>
                        <select name="idsession">
                            <?php
                            // Fetch and display sessions specific to the event manager's events
                            $eventsForManager = $db->getEventByUserId($userId);
                            foreach ($eventsForManager as $eventForManager) {
                                $eventId = $eventForManager['idevent'];
                                $sessionsForEvent = $db->getSessionByEventId($eventId);

                                foreach ($sessionsForEvent as $session) {
                                    echo "<option value=\"{$session['idsession']}\">{$session['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </p>
                    <p class="submit"><input type="submit" name="deleteSession" value="Delete Session"></p>
                </form>
            </div>



            <div class="login">
                <h1>Edit a Session</h1>
                <form method="post" action="">
                    <p>
                        <select name="sessionToDelete"> <!-- Use name in select tag -->
                            <?php
                            // Fetch and display sessions specific to the event manager's events
                            $eventsForManager = $db->getEventByUserId($userId);
                            foreach ($eventsForManager as $eventForManager) {
                                $eventId = $eventForManager['idevent'];
                                $sessionsForEvent = $db->getSessionByEventId($eventId);

                                foreach ($sessionsForEvent as $session) {
                                    echo "<option value=\"{$session['idsession']}\">{$session['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </p>
                    <p><input type="text" name="name" value="" placeholder="session name"></p>
                    <p><input type="text" name="numberallowed" value="" placeholder="number allowed"></p>
                    <p><input type="text" name="event" value="" placeholder="event"></p>
                    <p><input type="text" name="startdate" value="" placeholder="startdate"></p>
                    <p><input type="text" name="enddate" value="" placeholder="enddate"></p>
                    <p class="submit"><input type="submit" name="editSession" value="Edit Session"></p>
                </form>
            </div>

            <div class="login">
                <h1>Add a Session</h1>
                <form method="post" action="">
                    <p><input type="text" name="name" value="" placeholder="session name"></p>
                    <p><input type="text" name="numberallowed" value="" placeholder="number allowed"></p>
                    <p>
                        <select name="event" id="idevent">
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
                    <p><input type="text" name="startdate" value="" placeholder="startdate"></p>
                    <p><input type="text" name="enddate" value="" placeholder="enddate"></p>
                    <p class="submit"><input type="submit" name="addSession" value="Add Session"></p>
                </form>
            </div> 
        </div>
    </div>
    
<?php
$footer = $myUtils->html_footer("Thank you for visiting!");
  
// Output the footer
echo $footer;
?>