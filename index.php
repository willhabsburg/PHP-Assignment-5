<?php session_start();

/*  	William Habsburg 000869622 
		Assignment 5
        Sessions and jQuery
        index.php - the main page, for authentication
*/

    $myError = ""; // An error message to append to
    include "connect.php"; // This file connects to the database
    if($myError == "") { // if there is no issue connecting,
        $authed = isset($_SESSION['userid']); // TRUE if the user is authorized
        // These four variables determine status of request
        // status will be one of 'logout', 'login', 'register'
        // Username and password are for login and register
        // Password2 is used for registering
        $status = filter_input(INPUT_POST, "status", FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
        $password2 = filter_input(INPUT_POST, "password2", FILTER_SANITIZE_SPECIAL_CHARS);

        if($status == "logout") { // If the user clicked the logout link
            // Destroy the session
            session_unset();
            session_destroy();
            echo "success"; // echo back a success message for the JavaScript
        } else if($status == "login") { // if the user is trying to log in
            // Check the name and password for null and false
            if ($name !== null && $name !== false && $password !== null && $password !== false) {
                // Search the database for the username
                $command = "SELECT * FROM `Users` WHERE Username=?";
                $stmt = $dbh->prepare($command);
                $params = [$name];
                $success = $stmt->execute($params);
                if($success) {
                    $row = $stmt->fetch(); // if we have a matching row
                    // There will be only one row returned because the
                    // username is the PK
                    if(password_verify($password, $row["Password"])) { // if the passwords match
                        // Set the userid session variable to the username
                        $_SESSION['userid'] = $name;
                    } else {
                        // We have no match - give an error
                        $myError .= "Invalid Username or Password.";
                    }
                } else {
                    // Database lookup was unsuccessful.  Give an error
                    $myError .= "There was a problem with accessing the database.  Please try again later.";
                }
            } else {
                // username or password were null or false meaning invalid or not sent
                $myError .= "There was a problem with the user info.  Please try again.";
            }
        } else if($status == "register") { // If the user wants to register
            // Check the username and passwords, compare passwords
            if ($name !== null && $name !== false && $password !== null && $password !== false && $password2 !== null && $password2 !== false && $password == $password2) {
                // Check the database for existing user
                $command = "SELECT * FROM `Users` WHERE Username=?";
                $stmt = $dbh->prepare($command);
                $params = [$name];
                $success = $stmt->execute($params);
                if($success) {
                    if($stmt->rowCount() == 0) { // If there is NO match
                        // Add the user into the database
                        $pw_hash = password_hash($password, PASSWORD_DEFAULT);
                        $command = "INSERT INTO `Users`(`UserName`, `Password`) VALUES (?, ?);";
                        $stmt = $dbh->prepare($command);
                        $params = [$name, $pw_hash];
                        $success = $stmt->execute($params);
                        # if successful, give a message to indicate
                        if($success) {
                            $_SESSION['userid'] = $name; // We are successful, set session variable
                        } else {
                            // An error occurred.  Give a message
                            $myError .= "There was an issue adding you to the database.  Please try again later.";
                        }
                    } else {
                        // The user exists.  Give and error
                        $myError .= "That username already exists.  Please try again.";
                    }
                } else {
                    // A database error occurred.  Give an error
                    $myError .= "There was an issue getting data from the database.  Please try again later.";
                }
            } else {
                // A database error occurred.  Give an error
                $myError .= "There was a problem with the user info.  Please try again.";
            }
        }
    }
    // After all that, redo the authorization variable.
    // User may be logged in or registered
    $authed = isset($_SESSION['userid']);
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
    <script src="js/a5.js"></script>
    <link rel="stylesheet" href="css/css.css">
</head>

<body>
    <header class="container-lg bg-primary text-white pt-0">
        <div class="row">
            <div class="col-12 text-center">
                <h1>Stock Records</h1>
                <h3>William Habsburg Assignment 5 COMP 10065</h3>
            </div>
        </div>
    </header>

    <nav>
        <div class="row">
            <?php // Based on authentication, display a menu
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
            <?php // This is a message for JavaScript 
                if($authed) {
                    echo 'authorized';
                } else {
                    echo 'login';
                }
            ?>
        </p>
    </main>

    <div class="container-lg mt-3 mb-3" id="error">
        <?php // Since we have lots of opportunities for error, we have a div for that.
            if($myError != "") {
                echo $myError; // Display the error message.
            }
        ?>
    </div>

    <footer class="container-lg bg-primary text-white">
        <p>&copy; 2022, William Habsburg, Mohawk College</p>
    </footer>
</body>

</html>