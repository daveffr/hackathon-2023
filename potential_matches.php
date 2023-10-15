<?php
// Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
header("Content-Type: application/json");

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

// Start the session
session_start();

// Retrieve the username from the session
// if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Connect to the database (ensure you have this code)
    require 'database.php';

    // Query to retrieve the native_language and new_language of the logged-in user
    $result1 = $mysqli->query("SELECT native_language, new_language FROM userinfo WHERE username = '$username'");

    if ($result1) {
        // Fetch the result
        $userRow = $result1->fetch_assoc();

        // Now, you can access the native_language and new_language of the logged-in user
        $nativeLanguage = $userRow['native_language'];
        $newLanguage = $userRow['new_language'];

        // Query to find matching users
        $result2 = $mysqli->query("SELECT * FROM userinfo WHERE native_language = '$newLanguage' AND new_language = '$nativeLanguage' AND username != '$username'");

        $matchingUsers = array();
        while ($row = $result2->fetch_assoc()) {
            $matchingUsers[] = $row;
        }

        echo json_encode(array(
            "success" => true,
            "user_list" => $matchingUsers
        ));
    } else {
        // Handle the case where the query for the user's native and new languages fails
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to fetch user's language information"
        ));
    }
// } else {
//     // Handle the case where the session username is not set
//     echo json_encode(array(
//         "success" => false,
//         "message" => "Session username is not set"
//     ));
// }
?>