<?php
    // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
    header("Content-Type: application/json"); 

    //Because you are posting the data via fetch(), php has to retrieve it elsewhere.
    $json_str = file_get_contents('php://input');
    //This will store the data into an associative array
    $json_obj = json_decode($json_str, true);
    require 'database.php';

    $username = $json_obj['username'];
    $dob = $json_obj['dob'];
    $native = $json_obj['native'];
    $new = $json_obj['new'];

    $username = $mysqli->real_escape_string($username);
    $dob = $mysqli->real_escape_string($dob);
    $native = $mysqli->real_escape_string($native);
    $new = $mysqli->real_escape_string($new);

    $success = true;

    $stmt = $mysqli->prepare("insert into userinfo (username, dob, native, new) values (?, ?, ?, ?)");
    if(!$stmt){
        $success = false;
    }

    if ($success && !$stmt->bind_param('ssss', $username, $dob, $native, $new)) {
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
            "success" => true
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