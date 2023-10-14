<?php

// Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
header("Content-Type: application/json"); 

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$username = $json_obj['username'];
$password = $json_obj['password'];
//This is equivalent to what you previously did with $_POST['username'] and $_POST['password']

// Check to see if the username and password are valid.  (You learned how to do this in Module 3.)

require 'database.php';

//create account
$username = $mysqli->real_escape_string($username);
$password = $mysqli->real_escape_string($password);
$password = password_hash($password, PASSWORD_DEFAULT);

$success = true;

$stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
if(!$stmt){
    $success = false;
}

if ($success && !$stmt->bind_param('ss', $username, $password)) {
    $success = false;
}

if($success && !$stmt->execute()) {
    $success = false;
}

if($success && !$stmt->close()) {
    $success = false;
}

if($success) {
    echo json_encode(array(
        "success" => true,
    ));
    exit;
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "You F-ed up"
    ));
    exit;
}

?>
