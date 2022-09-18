<?php session_start();
/*  	William Habsburg 000869622 
		Assignment 5
        newest.php - This file displays the newest stock records
        If the session is new, display all, else display from last update
        Sessions and jQuery
*/

# Some debugging for my system
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

$myError = ""; // Initialize error message so we can append to it
include "connect.php"; // Connects to the database
include "record.php"; // This file contains the StRec class

$authed = isset($_SESSION['userid']); // if user is authorized, userid will have their name

// If there is no session variable named lastupdate, set it to zero
// This is used to determine what data to send
if(!isset($_SESSION['lastupdate'])) {
    $_SESSION['lastupdate'] = 0;
}

// The update variable will be sent only on POST requests asking for updates
$update = filter_input(INPUT_POST, "update", FILTER_SANITIZE_SPECIAL_CHARS);
// If the update variable exists and the user is authorized, then send JSON data
if($update !== null && $update !== false && $update == 'true' && $authed) {
    // If we get here, we have a POST update request
    try {
        $stockList = []; // create an empty array for data
        // Command gets all data where id > lastupdate session variable
        $command = "SELECT * FROM `StockUpdates` WHERE `id` > ? ORDER BY `id` DESC";
        $stmt = $dbh->prepare($command);
        $params = [$_SESSION['lastupdate']];
        $success = $stmt->execute($params);
        while ($row = $stmt->fetch()) { // While there is data,
            // Create a new StRec instance
            $stock = new StRec($row["StockId"], $row["StockName"], $row["CurrentPrice"], $row["UpdateDateTime"]);
            // Add it to the array
            array_push($stockList, $stock);
            // If the id of the record is greater than the session variable lastupdate
            if($_SESSION['lastupdate'] < $row['id']) {
                // Update the session variable
                $_SESSION['lastupdate'] = $row['id'];
            }
        }
    } catch (Exception $e) {
        // if there is an error, give the error message
        $stock = new StRec('error', 'There was an error accessing the database', '', '');
        array_push($stockList, $stock);
    }
    // Exit with a message which is the JSON data
    exit(json_encode($stockList));
}
// If we get here, we have a normal, non-update request
?><!doctype html>
<html lang="en">

<head>
    <title>Assignment 5</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/newest.js"></script>
    <link rel="stylesheet" href="css/css.css">
</head>

<body>
    <header class="container-lg bg-primary text-white pt-0">
        <div class="row">
            <div class="col-12 text-center">
                <h1>View Newest Stock Records</h1>
                <h3>William Habsburg Assignment 5 COMP 10065</h3>
            </div>
        </div>
    </header>

    <nav>
        <div class="row">
            <?php // this determines what menu to display
                if($authed) {
                    echo '<div class="col-4 text-center"><h3 class="command" id="view">View Records</h3></div>' .
                    '<div class="col-4 text-center"><h3 class="command" id="addrec">Add Records</h3></div>' .
                    '<div class="col-4 text-center"><h3 class="command" id="logout">Log Out</h3></div>';
                } else {
                    echo '<div class="col-6 text-center"><h3 class="command" id="login">Log In</h3></div>' .
                        '<div class="col-6 text-center"><h3 class="command" id="register">Sign up</h3></div>';
                }
            ?>
        </div>
    </nav>
    
    <main id="main" class="container-lg mt-3 mb-3">
        <p id="status">
            <?php // passing the status of the authorization for JavaScript
                if($authed) {
                    echo 'authorized';
                } else {
                    echo 'login';
                }
            ?>
        </p>
    </main>
    
    <div id='message' class="container-lg mt-3 mb-3">
    </div>

    <footer class="container-lg bg-primary text-white">
        <p>&copy; 2022, William Habsburg, Mohawk College</p>
    </footer>
</body>

</html>