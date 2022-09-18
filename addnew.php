<?php session_start();
/*  	William Habsburg 000869622 
		Assignment 5
        Sessions and jQuery
        addnew.php - this program adds new data into the database
*/


    $myError = ""; // Create an error message to append to
    include "connect.php"; // This connects to the database
    $authed = isset($_SESSION['userid']); // userid will be set if the user is authorized
    // If this is a POST request with this variable,
    // jsondata will hold the data to be added
    $array=json_decode($_POST['jsondata'], true);
    if($array !== null) {
        // If we get here, this is a POST request with json data to add to the database
        try {
            foreach($array as $ln) { // for each record in the jsondata
                // INSERT the data into the database.
                // UpdateDateTime is auto set by the database
                $command = "INSERT INTO `StockUpdates`(`StockId`, `StockName`, `CurrentPrice`) VALUES (?, ?, ?);";
                $stmt = $dbh->prepare($command);
                $params = [$ln['StockId'], $ln['StockName'], $ln['CurrentPrice']];
                $success = $stmt->execute($params);
            }
            // exit with a success message
            exit("The item(s) were successfully added to the database.");
        } catch (Exception $e) {
            // if there is an error, give the error message
            exit("There was an error adding to the database.");
        }
    }
// If we reach here, this was a regular request, send the HTML page
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
    <script src="js/addnew.js"></script>
    <link rel="stylesheet" href="css/css.css">
</head>

<body>
    <header class="container-lg bg-primary text-white pt-0">
        <div class="row">
            <div class="col-12 text-center">
                <h1>Add Stock Records</h1>
                <h3>William Habsburg Assignment 5 COMP 10065</h3>
            </div>
        </div>
    </header>

    <nav>
        <div class="row">
            <?php // Display the menu based on authentication status
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
            <?php // a message passed to JavaScript to give authentication status.
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