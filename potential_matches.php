<?php
// Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
header("Content-Type: application/json");

// Start the session
session_start();

// Retrieve the username from the session
if (!isset($_SESSION['username'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Session username is not set"
    ));
    exit;
}

$username = $_SESSION['username'];
$matchingUsers = array();

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

    while ($row = $result2->fetch_assoc()) {
        $matchingUsers[] = $row;
    }

} else {
    // Handle the case where the query for the user's native and new languages fails
    echo json_encode(array(
        "success" => false,
        "message" => "Failed to fetch user's language information"
    ));
    exit;
}

$stmt1 = $mysqli->prepare("SELECT hobbies1, hobbies2, hobbies3, hobbies4, hobbies5, hobbies6, hobbies7, hobbies8, hobbies9, hobbies10 FROM userinfo WHERE username = ?");
if (!$stmt1) {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 1): " . $mysqli->error
    ));
    exit;
}

if ($stmt1->bind_param('s', $username) && $stmt1->execute()) {
    $stmt1->bind_result($hobbies1, $hobbies2, $hobbies3, $hobbies4, $hobbies5, $hobbies6, $hobbies7, $hobbies8, $hobbies9, $hobbies10);

    if ($stmt1->fetch()) {
        $hobbies = array($hobbies1, $hobbies2, $hobbies3, $hobbies4, $hobbies5, $hobbies6, $hobbies7, $hobbies8, $hobbies9, $hobbies10);
    }

    $stmt1->close();
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Database error (query 1): " . $stmt1->error
    ));
    exit;
}

$scores = array();
for ($i = 0; $i < count($hobbies); $i++) {
    $stmt2 = $mysqli->prepare("SELECT hobbies1, hobbies2, hobbies3, hobbies4, hobbies5, hobbies6, hobbies7, hobbies8, hobbies9, hobbies10 FROM userinfo WHERE username = ?");
    if (!$stmt2) {
        echo json_encode(array(
            "success" => false,
            "message" => "Database error (query 2): " . $mysqli->error
        ));
        exit;
    }

    if ($stmt2->bind_param('s', $matchingUsers[$i]['username']) && $stmt2->execute()) {
        $stmt2->bind_result($matched_hobbies1, $matched_hobbies2, $matched_hobbies3, $matched_hobbies4, $matched_hobbies5, $matched_hobbies6, $matched_hobbies7, $matched_hobbies8, $matched_hobbies9, $matched_hobbies10);

        if ($stmt2->fetch()) {
            $matched_user_hobbies = array($matched_hobbies1, $matched_hobbies2, $matched_hobbies3, $matched_hobbies4, $matched_hobbies5, $matched_hobbies6, $matched_hobbies7, $matched_hobbies8, $matched_hobbies9, $matched_hobbies10);
            $total = 0;
            $score = 0;
            for ($j = 0; $j < count($matched_user_hobbies); $j++) {
                if ($hobbies[$j] == 1 || $matched_user_hobbies[$j] == 1) {
                    $total++;
                    if ($hobbies[$j] == 1 && $matched_user_hobbies[$j] == 1) {
                        $score++;
                    }
                }
            }
            if ($total > 0) {
                $scores[] = $score / $total;
            } else {
                $scores[] = 0;
            }
        }

        $stmt2->close();
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Database error (query 2): " . $stmt2->error
        ));
        exit;
    }
}

echo json_encode(array(
    "success" => true,
    "user_list" => $matchingUsers,
    "hobbies" => $hobbies,
    "scores" => $scores
));
?>
