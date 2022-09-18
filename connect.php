<?php
/* 	    William Habsburg 000869622 
		Assignment 4
        connect.php
        This file provides a connectioin to my database
*/
// Try to connect to the database
try {
    $dbh = new PDO(
        "mysql:host=localhost;dbname=dbname",
        "username", "password"
    );
    // If successfull, myError is a blank string
    $myError = "";
} catch (Exception $e) {
    // If there is an error, put the error into the message
    $myError = $e->getMessage();
}
?>