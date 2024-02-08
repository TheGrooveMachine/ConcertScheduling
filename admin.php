<?php
    
    require_once("DB.class.php"); // Assuming this file exists and includes your database class
    $db = new DB();
    include 'sections/check_login.php';

    $userId = "";

    include 'sections/db_connection.php';//include the db connection
    include 'MyUtils.php';

    $myUtils = new MyUtils();

    // Call the html_header function
    $header = $myUtils->html_header("Admin");
   
  

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["deleteEvent"])) {
            $idevent = $_POST["idevent"];
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
        } elseif (isset($_POST["deleteSession"])) {
            $idsession = $_POST["idsession"];
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
            $idsession = $_POST['idsession'];
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
        } elseif (isset($_POST["deleteVenue"])) {
            $idvenue = $_POST["idvenue"];
            $result = $db->deleteVenue($idvenue);

            if ($result) {
                echo "Venue successfully deleted.";
            } else {
                echo "Error: Venue deletion failed.";
            }

        } elseif (isset($_POST["addVenue"])) {
            $name = $_POST['name'];
            $capacity = $_POST['capacity'];
            $result = $db->addVenue($name, $capacity);

            if ($result) {
                echo "Your Venue was successfully added.";
            } else {
                echo "Error: Venue addition failed.";
            }
        } elseif (isset($_POST["editVenue"])) {
            $idvenue = isset($_POST["idvenue"]) ? $_POST["idvenue"] : '';
            $name = $_POST['name'];
            $capacity = isset($_POST["capacity"]) ? $_POST["capacity"] : '';


            $result = $db->updateVenue($idvenue, $name, $capacity);

            if ($result) {
                echo "Your Venue was successfully updated.";
            } else {
                echo "Error: Venue update failed.";
            }
        } elseif (isset($_POST["deleteAttendee"])) {
            $idattendee = isset($_POST["idattendee"]) ? $_POST["idattendee"] : '';  // Check if 'idattendee' is set
            $result = $db->deleteUser($idattendee);

            if ($result) {
                echo "Attendee successfully deleted.";
            } else {
                echo "Error: Attendee deletion failed.";
            }

        } elseif (isset($_POST["addAttendee"])) {
            $name = $_POST['name'];
            $password = $_POST['password'];
            $role = $_POST['role'];
            $result = $db->addUser($name, $password, $role);

            if ($result) {
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
        }
        
    }
?>


    <!-- Header -->
    <?php 
        echo $header;
        include 'sections/adminTools.php';
    ?>
        <div class="controlsContainer">
            <div class="adminControls">
                <div class="login">
                    <h1>Delete an event</h1>
                    <form method="post" action="">
                        <p><input type="text" name="idevent" value="" placeholder="id of event to delete"></p>
                        <p class="submit"><input type="submit" name="deleteEvent" value="Delete Event"></p>
                    </form>
                </div> 

                <div class="login">
                    <h1>Edit an event</h1>
                    <form method="post" action="">
                        <p><input type="text" name="idevent" value="" placeholder="Id of event you wish to edit"></p>
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
                    <h1>Delete a Session</h1>
                    <form method="post" action="">
                        <p><input type="text" name="idsession" value="" placeholder="id of session to delete"></p>
                        <p class="submit"><input type="submit" name="deleteSession" value="Delete Session"></p>
                    </form>
                </div> 

                <div class="login">
                    <h1>Edit a Session</h1>
                    <form method="post" action="">
                        <p><input type="text" name="idsession" value="" placeholder="Id of session you wish to edit"></p>
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
                        <p><input type="text" name="event" value="" placeholder="event"></p>
                        <p><input type="text" name="startdate" value="" placeholder="startdate"></p>
                        <p><input type="text" name="enddate" value="" placeholder="enddate"></p>
                        <p class="submit"><input type="submit" name="addSession" value="Add Session"></p>
                    </form>
                </div> 
            </div>
            <div class="adminControls">
                <div class="login">
                    <h1>Delete a Venue</h1>
                    <form method="post" action="">
                        <p><input type="text" name="idvenue" value="" placeholder="id of venue to delete"></p>
                        <p class="submit"><input type="submit" name="deleteVenue" value="Delete Venue"></p>
                    </form>
                </div> 

                <div class="login">
                    <h1>Edit a Venue</h1>
                    <form method="post" action="">
                        <p><input type="text" name="idvenue" value="" placeholder="Id of Venue you wish to edit"></p>
                        <p><input type="text" name="name" value="" placeholder="venue name"></p>
                        <p><input type="text" name="capacity" value="" placeholder="capacity"></p>
                        <p class="submit"><input type="submit" name="editVenue" value="Edit Venue"></p>
                    </form>
                </div>

                <div class="login">
                    <h1>Add a Venue</h1>
                    <form method="post" action="">
                        <p><input type="text" name="name" value="" placeholder="Venue name"></p>
                        <p><input type="text" name="capacity" value="" placeholder="capacity"></p>
                        <p class="submit"><input type="submit" name="addVenue" value="Add venue"></p>
                    </form>
                </div> 
            </div>
            <div class="adminControls">
                <div class="login">
                    <h1>Delete an Attendee</h1>
                    <form method="post" action="">
                        <p><input type="text" name="idattendee" value="" placeholder="id of Attendee to delete"></p>
                        <p class="submit"><input type="submit" name="deleteAttendee" value="Delete Attendee"></p>
                    </form>
                </div> 

                <div class="login">
                    <h1>Edit an Attendee</h1>
                    <form method="post" action="">
                        <p><input type="text" name="idattendee" value="" placeholder="Id of Attendee you wish to edit"></p>
                        <p><input type="text" name="name" value="" placeholder="Attendee name"></p>
                        <p><input type="text" name="password" value="" placeholder="password"></p>
                        <p><input type="text" name="role" value="" placeholder="role"></p>
                        <p class="submit"><input type="submit" name="editVenue" value="Edit Attendee"></p>
                    </form>
                </div>

                <div class="login">
                    <h1>Add a Attendee</h1>
                    <form method="post" action="">
                        <p><input type="text" name="name" value="" placeholder="Attendee name"></p>
                        <p><input type="text" name="password" value="" placeholder="password"></p>
                        <p><input type="text" name="role" value="" placeholder="role"></p>
                        <p class="submit"><input type="submit" name="addAttendee" value="Add Attendee"></p>
                    </form>
                </div> 
            </div>
        </div>

<?php
    $footer = $myUtils->html_footer("Thank you for visiting!");

    // Output the footer
    echo $footer;
?>