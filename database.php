<?php
// Content of database.php
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'hackathon2023');

if ($mysqli->connect_errno) {
    echo htmlentities("Connection Failed: " . $mysqli->connect_error);
    exit;
}

// Execute a query to get the current database name
$result = $mysqli->query("SELECT DATABASE()");

if ($result) {
    // Fetch the result as an associative array
    $row = $result->fetch_assoc();

    // Access the database name from the result
    $databaseName = $row['DATABASE()'];

    // Free the result set
    $result->free();
} else {
    // Handle the query error
    echo htmlentities("Error: " . $mysqli->error);
}
?>