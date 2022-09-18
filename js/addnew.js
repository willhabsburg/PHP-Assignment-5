/* William Habsburg
   000869622
   Assignment 5 JavaScript file for addnew.php
*/


// Contain all code in the load event listener
$(function () {
    let status = $('#status').text().trim(); // Status from the PHP file
    let recordNum = 0; // The number of records the user want's to add - auto incremented
    // Stock symbols and names for auto adding data
    let stockSymb = ['AAPL', 'AMAZ', 'QQQ', 'MSFT', 'AMR', 'VET', 'SJT', 'ASC', 'CLFD', 'SGML'];
    let stockName = ['Apple', 'Amazon', 'Invesco QQQ Trust', 'Microsoft', 'Alpha Metallurgical', 'Vermision Energy', 'San Juan Basin Royalty Trust', 'Ardmore Shipping Corp', 'Clearfield', 'Sigma Lithium Corp'];

    // The login link event
    $("#login").click(function (event) {
        document.location = 'index.php';
    });

    // The register link event handler
    $("#register").click(function (event) {
        document.location = 'index.php';
    });

    // The view records link event handler
    $("#view").click(function (event) {
        document.location = 'newest.php';
    });

    // The add record link event handler
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

    // This function appends a blank row for adding stock data
    function addRow() {
        el = $("<tr>"); // Create an element
        // Content is table row data including record number to separate names
        content = "<td><input type='text' required max-length='10' id='id" + recordNum + "' name='id" + recordNum + "' />";
        content += "<td><input type='text' required max-length='49' id='name" + recordNum + "' name='name" + recordNum + "' />";
        content += "<td><input type='number' step='0.01' required max-length='10' id='price" + recordNum + "' name='price" + recordNum + "' />";
        el.html(content); // Add the content to the element
        recordNum++; // Increment the record number
        $('tbody').append(el); // append the element to the end of the table
    }

    // This function is called when the Add Random button is clicked
    function addRandom(event) {
        resetPage(); // Clear the page
        for(let i = 0; i < 10; i++) {
            addRow(); // Add a table row
            // id, name, price are filled in with arrays and random price
            // recordNum - 1 because recordNum is always 1 ahead of last item
            $('#id' + (recordNum - 1)).val(stockSymb[i]);
            $('#name' + (recordNum - 1)).val(stockName[i]);
            $('#price' + (recordNum - 1)).val((Math.random() * 100).toFixed(2));
        }
    }

    // This function submits the table data to the database
    function submitItems(event) {
        $('#message').text(""); // Clear the message text
        event.preventDefault(); // Stop the event from propagating
        toPass = []; // A blank array to hold the info
        for(let i = 0; i < recordNum; i++) { // For every table row
            let thisItem = { // create a dictionary of that row
                'StockId': $('#id' + i).val(),
                'StockName': $('#name' + i).val(),
                'CurrentPrice': $('#price' + i).val()
            }
            toPass.push(thisItem); // Push it to the array
        }
        params = "jsondata=" + JSON.stringify(toPass); // Create some JSON data from the array
        fetch("addnew.php", { // A POST request to send the data
            method: 'POST',
            credentials: 'include',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params })
        .then(response => response.text())
        // If successful, call function submitSuccess
        .then(submitSuccess);
    }

    // This function is called when submitting data is successful
    function submitSuccess(text) {
        $('#message').text(text); // Display message from PHP page
    }

    // This function clears the <main> section when the user presses 'clear all'
    function clearAll(event) {
        resetPage(); // Reset the Table data
        addRow(); // Add a blank row to the table
    }

    // This function resets the table to blank, creates some buttons
    // And sets the button handlers
    function resetPage() {
        recordNum = 0;
        content = "<h3>Add Records</h3><form id='addnew' name='addnew'>" +
            "<table class='table table-primary table-striped'>" + 
            "<thead class='table-info'><tr><th>Stock ID</th><th>Stock Name</th>" +
            "<th>Current Price</th></tr></thead><tbody></tbody></table>" +
            "<input type='submit' class='btn btn-primary' id='submit' value='Submit to Database'>" +
            "<button type='button' class='btn btn-primary' id='addanother'>Add Another Row</button>" +
            "<button type='button' class='btn btn-primary' id='random'>Add 10 Random Items</button>" +
            "<button type='button' class='btn btn-primary' id='clearall'>Clear All Items</button>" +
            "</form>";
        $('#main').html(content);
        $('#addanother').click(addRow);
        $('#submit').click(submitItems);
        $('#random').click(addRandom);
        $('#clearall').click(clearAll);
    }


    // This is called after the page is loaded
    if (status == "authorized") { // if we are authorized
        resetPage(); // Reset the page
        addRow(); // Add a data row
    } else { // else show login page
        document.location = 'index.php';
    }

});