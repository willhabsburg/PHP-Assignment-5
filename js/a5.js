/* William Habsburg
   000869622
   Assignment 5 JavaScript file for index.php
*/


// Contain all code in the load event listener
$(function () {
    // Status section in main - status from PHP program
    let status = $('#status').text().trim();

    // Login link click
    $("#login").click(function (event) {
        showLogin();
    });

    // View records link click
    $("#view").click(function (event) {
        document.location = 'newest.php';
    });

    // Add records link click
    $("#addrec").click(function (event) {
        document.location = 'addnew.php';
    });
    
    // Logout link click
    $("#logout").click(function (event) {
        let params = "status=logout"; // message to send to PHP file
        // Send a POST request asking to logout
        fetch("index.php", {
            method: 'POST',
            credentials: 'include',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params })
        .then(response => response.text())
        // Get a response, then call listSuccess function
        .then(logoutSuccess)
    });

    // Register link click event
    // Build a form asking for username, password, and verify password
    $("#register").click(function (event) {
        let content = "<h1>Register a new account:</h1>";
        content += "<h6>If you already have a username and password, please click the 'Log In' link</h6>";
        content += "<form id='register' name='register' action='index.php' method='post'>";
        content += "<input type='hidden' name='status' value='register' />";
        content += "<label class='form-label' for='username'>Username:</label>";
        content += "<input class='form-control' type='text' name='username' id='username' required placeholder='Enter your username' maxlength='50'>";
        content += "<label class='form-label' for='password'>Password:</label>";
        content += "<input class='form-control' type='password' name='password' id='password' required placeholder='Enter your password' maxlength='25'>";
        content += "<label class='form-label' for='password2'>Verify Your Password:</label>";
        content += "<input class='form-control' type='password' name='password2' id='password2' required placeholder='Verify your password' maxlength='25'>";
        content += "<input class='btn btn-primary' type='submit' value='Register' /></form>";
        // Put the form into the main content area
        $("#main").html(content);
        // Add an event listener for the form's submit button
        $("[type='submit']").click(registerClick);
    });

    function showLogin() {// This sets up a form to add a student
        // This is done by POST request by the form itself.
        let content = "<h1>Please Log In:</h1>";
        content += "<h6>If you don't have a username and password, please click the 'Sign up' link</h6>";
        content += "<form id='login' name='login' action='index.php' method='post'>";
        content += "<input type='hidden' name='status' value='login' />";
        content += "<label class='form-label' for='username'>Username:</label>";
        content += "<input class='form-control' type='text' name='username' id='username' required placeholder='Enter your username' maxlength='50'>";
        content += "<label class='form-label' for='password'>Password:</label>";
        content += "<input class='form-control' type='password' name='password' id='password' required placeholder='Enter your password' maxlength='25'>";
        content += "<input class='btn btn-primary' type='submit' value='Login' /></form>";
        // Put the form into the main content area
        $("#main").html(content);
    }

    // This function is called when the registration form submit button is clicked
    // Do some checking of the data before sending a registration request
    function registerClick(event) {
        let user = $('#username').val();
        let pass = $('#password').val();
        let pass2 = $('#password2').val();
        // If the passwords don't match
        if(pass != pass2) {
            event.preventDefault(); // stop the event
            // Give an error
            $('#error').html("<p>The passwords do not match.  Please try again</p>");
        }
    }

    // This function is called when the logout POST request is successful
    function logoutSuccess(resp) {
        if(resp.substr(0,7) == "success") { // if we get a success message
            status = ""; // Reset the status
            document.location = "index.php"; // Redirect the page
        } else {
            // If not successful, give an error
            $('#error').html("<p>There was an error logging out.</p>");
        }
    }

    // This is executed upon page load
    // If we are authorized, display a message, else load the login screen
    if (status == "authorized") {
        $("#main").html("<p>Please select from the above links to view or add records.</p>");
    } else {
        showLogin();
    }
});