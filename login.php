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
$valid = true;

$username = $mysqli->real_escape_string($username);
$password = $mysqli->real_escape_string($password);

$stmt = $mysqli->prepare("select password from users where username=?");
if(!$stmt){
    $valid = false;
}

if ($valid && !$stmt->bind_param('s', $username)) {
    $valid = false;
}

if($valid && !$stmt->execute()) {
    $valid = false;
}


if($valid) {
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if(!password_verify($password, $hashed_password)) {  
        $valid = false;
    }
}

$stmt->close();

if($valid){
	session_start();
	$_SESSION['username'] = $username;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 

	echo json_encode(array(
		"success" => true
	));
	exit;
}else{
	echo json_encode(array(
		"success" => false,
		"message" => "Incorrect Username or Password"
	));
	exit;
}
?>