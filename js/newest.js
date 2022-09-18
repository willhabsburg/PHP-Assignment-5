/* William Habsburg
   000869622
   Assignment 5 JavaScript file for newest.php
*/


// Contain all code in the load event listener
$(function () {
    // status is from PHP page
    let status = $('#status').text().trim();

    // Login link click
    $("#login").click(function (event) {
        document.location = 'index.php';
    });

    // Register link click
    $("#register").click(function (event) {
        document.location = 'index.php';
    });

    // View Data link clicked
    $("#view").click(function (event) {
        document.location = 'newest.php';
    });

    // Add Records link clicked
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

    // this function is called by the timer set below
    function updateStock() {
        $('#message').html("<p>Retrieving Data...</p>"); // Display a message
        params = "update=true"; // Indicate this is an update
        fetch("newest.php", { // POST request to get stock data
            method: 'POST',
            credentials: 'include',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params })
        // Convert response to JSON
        .then(response => response.json())
        // Call the success method
        .then(updateSuccess);
    }

    // This function is called when the POST request to update stock data is successful.
    function updateSuccess(jsonData) {
        $('#message').html("<p>Data Retrieved Successfully</p>"); // Display a message
        // Start at the end of the list to get the items in proper order
        for(let i = jsonData.length - 1; i >= 0; i--) { // for each item in the list
            let el = $('<tr>'); // create a table row element
            // Add content to it - the stock data
            content = '<td>' + jsonData[i].StockId + "</td><td>" + jsonData[i].StockName + "</td><td class='pushRight'>$" +
                parseFloat(jsonData[i].CurrentPrice).toFixed(2) + "</td><td>" +
                jsonData[i].UpdateDateTime + "</td>";
            el.html(content); // Add the content to the tr element
            $('tbody').prepend(el); // Add it to the top of the table data
        }
    }

    // This is executed on page load
    if (status == "authorized") { // if the user is authorized
        // Create a timer for 5 seconds, call updateStock()
        let myTimer = setInterval(updateStock, 5000);
        // create the table to add to the <main> section
        let content = "<h3>Newest records are at the top</h3>" +
            "<table class='table table-primary table-striped'><thead class='table-info'>" +
            "<tr><th>Stock ID</th><th>Stock Name</th><th class='pushRight'>Current Price</th><th>Last Updated At</th>" +
            "</tr><tbody></tbody></table>";
        $("#main").html(content); // Display the content
        $('#message').html("<p>Retrieving Data...</p>");  // Display a message
        updateStock(); // Immediately call updateStock to get info before timer
    } else { // If not authorized, redirect to login page
        document.location = 'index.php';
    }

});