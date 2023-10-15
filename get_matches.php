<?php
header("Content-Type: application/json"); 

// Retrieve JSON data from the request
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

session_start();
require 'database.php';

$username = $_SESSION['username'];

$matchedUsernames = array();

// Get all matches with this username
$stmt = $mysqli->prepare("SELECT matched_username FROM matches WHERE username = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 1): " . $mysqli->error
    ));
    exit;
}

if ($stmt->bind_param('s', $username) && $stmt->execute()) {
    $stmt->bind_result($matched_username);
    
    while ($stmt->fetch()) {
        $matchedUsernames[] = $matched_username;
    }
    
    $stmt->close();
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 1): " . $stmt->error
    ));
    exit;
}

//check the list of matchedUsernames to see if the matchedUsername matched with the username
//if it does, then add to new array
//return new array

$backMatchedUsernames = array();

$stmt = $mysqli->prepare("SELECT username FROM matches WHERE matched_username = ?");

if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 2): " . $mysqli->error
    ));
    exit;
}

if($stmt->bind_param('s', $username) && $stmt->execute()) {
    $stmt->bind_result($matched_username);
    
    while ($stmt->fetch()) {
        if(in_array($matched_username, $matchedUsernames)){
            $backMatchedUsernames[] = $matched_username;
        }
    }
    
    $stmt->close();
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 2): " . $stmt->error
    ));
    exit;
}

$backMatchedEmails = array();
foreach ($backMatchedUsernames as $backMatchedUser){
    $stmt1 = $mysqli->prepare("SELECT email FROM userinfo WHERE username = ?");
    if (!$stmt1) {
        echo json_encode(array(
            "success" => false,
            "message" => "Database error (query 2): " . $mysqli->error
        ));
        exit;
    }
    if($stmt1->bind_param('s', $backMatchedUser) && $stmt1->execute()) {
        $stmt1->bind_result($email);
        $stmt1->fetch();
        $backMatchedEmails[] = $email;
        $stmt1->close();
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Database error (query 2): " . $stmt1->error
        ));
        exit;
    }
}

// INSERT CODE HERE
echo json_encode(array(
    "success" => true,
    "matchedUsernames" => $backMatchedUsernames,
    "matchedEmails" => $backMatchedEmails
));


?>