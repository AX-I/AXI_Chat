<?php
include "ChatFunctions.php";
include "AXIEncrypt.php";

	$servername = "localhost";
	$username = "User111";
	$password = "Password111";

	$newclient = true;

	$conn = new mysqli($servername, $username, $password);
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	$sql = "USE ChatData;";
	$conn->query($sql);
	
	$client = $conn->real_escape_string($client);
	
	$browser = $conn->real_escape_string($browser);
	
	$sql = "SELECT * FROM ChatBanned WHERE IP = \"$ip\" AND Browser = \"$browser\"";
	$usrval = $conn->query($sql);
	if ($usrval->num_rows > 0) {
		header("Location: /ChatLogin.php?result=usrbanned");
		exit("You have been banned!");
	}
	$sql = "SELECT * FROM ChatRef WHERE Username = \"$client\" ORDER BY eID DESC";
	$usrval = $conn->query($sql);
	if ($usrval->num_rows > 0) {
		$usrrow = $usrval->fetch_assoc();
		if (($usrrow["IP"] == $ip) && ($usrrow["BrowserAgent"] == $browser)) {
			$newclient = false;
		}
		else {
			header("Location: /ChatLogin.php?result=usrtaken");
			exit("This username is taken.");
		}
	}
    $jms = asciiencrypt("e", " " . $client . " has joined.", 1);
	$sql = "INSERT INTO ChatMsgs (userID, msg, S) values ('ChatServer', '$jms', 1);";
	$conn->query($sql);

	$sessID = newSessionID($client);
	$sql = "INSERT INTO ChatRef (Username, IP, Sessionid, BrowserAgent, Cookie) values ";
	$sql .= "(\"$client\", \"$ip\", \"$sessID\", \"$browser\", \"$pID\");";
	if ($conn->query($sql) === TRUE) {
		$sql = "SELECT * FROM ChatUsers WHERE Username = \"$client\"";
		$usrval = $conn->query($sql);
		if ($usrval->num_rows > 0) {
			$sql = "UPDATE ChatUsers SET Online = 1 WHERE Username=\"$client\";";
		}
		else {
			$sql = "INSERT INTO ChatUsers (Username, Online) values (\"$client\", 1);";
		}
		$conn->query($sql);
	
		setcookie("IDHash", $sessID, time()+3600);
		setcookie("UserID", $client, time()+3600);
		header("Location: /ChatMain.php");
		exit("Logged in successfully<br>Please reload the page");
	}
	else {
		header("Location: /ChatLogin.php?result=error");
		exit("There was a problem; please login again");
	}
?>
