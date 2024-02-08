<?php

$servername = "localhost";
$username = "eds5997";
$password = "Records7^muraenidae";
$dbname = "eds5997";

require_once("DB.class.php"); // Assuming this file exists and includes your database class

$conn = new mysqli($servername, $username, $password, $dbname);
$db = new DB();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>

<tab-container>
        <!-- TAB CONTROLS -->
        <input type="radio" id="tabToggle01" name="tabs" value="1" checked />
        <label for="tabToggle01" checked="checked">Events</label>
        <input type="radio" id="tabToggle02" name="tabs" value="2" />
        <label for="tabToggle02">Sessions</label>
        <input type="radio" id="tabToggle03" name="tabs" value="3" />
        <label for="tabToggle03">Venues</label>
        <input type="radio" id="tabToggle04" name="tabs" value="4" />
        <label for="tabToggle04">Attendees</label>
        
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
                    $sessions = $db->getAllSessions();
                    if ($events) {
                        foreach ($sessions as $sessions) {
                            echo "<div class='ag-courses_item'>
                                    <a href='#' class='ag-courses-item_link'>
                                        <div class='ag-courses-item_bg'></div>
                                        <div class='ag-courses-item_title'>
                                            {$sessions['name']}
                                            
                                        </div>
  
                                        <div class='ag-courses-item_date-box'>
                                            Start:
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
                    $venues = $db->getAllVenues();
                    if ($venues) {
                        foreach ($venues as $venues) {
                            echo "<div class='ag-courses_item'>
                                    <a href='#' class='ag-courses-item_link'>
                                        <div class='ag-courses-item_bg'></div>
                                        <div class='ag-courses-item_title'>
                                            {$venues['name']}
                                        </div>
                                        
                                        <div class='ag-courses-item_date-box'>
                                            Start:
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
                    $users = $db->getAllusers();
                    if ($users) {
                        foreach ($users as $users) {
                            echo "<div class='ag-courses_item'>
                                    <a href='#' class='ag-courses-item_link'>
                                        <div class='ag-courses-item_bg'></div>
                                        <div class='ag-courses-item_title'>
                                            {$users['name']}
                                        </div>
                                        <div class='ag-courses-item_date-box'>
                                            Start:
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

