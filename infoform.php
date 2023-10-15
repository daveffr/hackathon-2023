<?php
    // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
    header("Content-Type: application/json"); 

    //Because you are posting the data via fetch(), php has to retrieve it elsewhere.
    $json_str = file_get_contents('php://input');
    //This will store the data into an associative array
    $json_obj = json_decode($json_str, true);

    session_start();
    require 'database.php';
    
    $username = $_SESSION['username'];
    $name = $json_obj['name'];
    $dob = $json_obj['dob'];
    $native = $json_obj['native'];
    $new = $json_obj['new'];
    $hobbies = $json_obj['hobbies'];
    $email = $json_obj['email'];
    

    $username = $mysqli->real_escape_string($username);
    $name = $mysqli->real_escape_string($name);
    $dob = $mysqli->real_escape_string($dob);
    $native = $mysqli->real_escape_string($native);
    $new = $mysqli->real_escape_string($new);
    //check if userinfo is already in database for username

    $success = true;

    $stmt = $mysqli->prepare("insert into userinfo (username, name, dob, native_language, new_language, email, hobbies1, hobbies2, hobbies3, hobbies4, hobbies5, hobbies6, hobbies7, hobbies8, hobbies9, hobbies10) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if(!$stmt){
        $success = false;
    }

    if ($success && !$stmt->bind_param('ssssssiiiiiiiiii', $username, $name, $dob, $native, $new, $email, $hobbies[0], $hobbies[1], $hobbies[2], $hobbies[3], $hobbies[4], $hobbies[5], $hobbies[6], $hobbies[7], $hobbies[8], $hobbies[9])) {
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
            "username" => "$username",
            "name" => "$name",
            "dob" => "$dob",
            "native" => "$native",
            "new" => "$new",
            "message" => "you chillin"
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