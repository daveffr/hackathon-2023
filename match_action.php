<?php
header("Content-Type: application/json"); 

// Retrieve JSON data from the request
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

session_start();
require 'database.php';

$matched_username = $json_obj['matched_username'];
$username = $_SESSION['username'];

$success = true;

$stmt = $mysqli->prepare("select COUNT(*) as match_count from matches where username=? and matched_username=?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 1): " . $mysqli->error
    ));
    exit;
}

if ($stmt->bind_param('ss', $username, $matched_username) && $stmt->execute()) {
    $stmt->bind_result($matchCount);
    $stmt->fetch();
    $stmt->close();

    if ($matchCount > 0) {
        echo json_encode(array(
            "success" => false,
            "message" => $matched_username . " is already in your matches"
        ));
        exit;
    }
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 1): " . $stmt->error
    ));
    exit;
}

$stmt = $mysqli->prepare("insert into matches (username, matched_username) values (?, ?)");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 2): " . $mysqli->error
    ));
    exit;
}

if ($stmt->bind_param('ss', $username, $matched_username) && $stmt->execute()) {
    $stmt->close();

    echo json_encode(array(
        "success" => true,
        "message" => $username. " have matched with this user: " . $matched_username
    ));
    exit;
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 2): " . $stmt->error
    ));
    exit;
}
?>