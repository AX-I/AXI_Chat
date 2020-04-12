<?php
	$client = $_POST["user"];
	$msgtext = $_POST["text"];
	$msgtexts1 = $_POST["stext"];
	$msgrequest = $_POST["requestmsg"];
	$loggedoff = $_POST["logoff"];
	$browser = $_SERVER["HTTP_USER_AGENT"];
	$msglimit = $_POST["msglimit"];
	$msgoffset = (isset($_POST["msgoffset"])) ? $_POST["msgoffset"] : 0;
	$showdates = $_POST["Date"];
	
	$sessID = $_COOKIE["IDHash"];
	$ip = (string)$_SERVER["REMOTE_ADDR"];

include "ChatFunctions.php";
include "AXIEncrypt.php";

if (($client == NULL) && ($msgrequest == NULL) && ($loggedoff == NULL)) {
	header("Location: /");
	exit("Hello!");
}
else {
	
	$servername = "localhost";
	$username = "AXI_1";
	$password = "r94tni+oC^4@"; // Definitely the real password

	$conn = new mysqli($servername, $username, $password);

	$sql = "USE ChatData;";
	if ($conn->query($sql) === TRUE) {
	}
	if ($sessID != NULL) {
		$Sclient = getUser($ip, $sessID);
		if ($Sclient == "") {
			exit("Invalid session!");
		}
		if ($Sclient == "[Kicked]") {
			setcookie("IDHash", "", time()-100);
			setcookie("UserID", "", time()-100);
			exit("You have been kicked!");
		}
		if (substr($Sclient, 0, 8) == "[Banned]") {
			setcookie("IDHash", "", time()-100);
			setcookie("UserID", "", time()-100);
			exit("You have been banned!");
		}
		if ($client != NULL) {
			if ($Sclient != $client) {
				exit("Name does not match!");
			}
			$client = $conn->real_escape_string($client);
			if ($client == "ChatServer") {
			  exit("Invalid name.");
			}
			if ($msgtext != NULL) {
				$msgtext = str_replace(["\""], "", $msgtext);
				$msgtext = $conn->real_escape_string($msgtext);
				$msgtext = $msgtext;
				$sql = "INSERT INTO ChatMsgs (userID, msg) values ('$client', '$msgtext');";
			  $conn->query($sql);
			}
			if ($msgtexts1 != NULL) {
				$msgtexts1 = str_replace(["\\", "\""], "", $msgtexts1);
				$msgtexts1 = $msgtexts1;
				$msgtexts1 = $conn->real_escape_string($msgtexts1);
				$sql = "INSERT INTO ChatMsgs (userID, msg, S) values ('$client', '$msgtexts1', 1);";
				$conn->query($sql);
			}
		}
	
		if ($msgrequest != NULL) {
			if ($msgrequest == "WebAppS") {
				if ($msglimit > 200) $msglimit = 200;
				$sql = "SELECT * FROM ChatMsgs WHERE S=1 ORDER BY eID DESC LIMIT " . $msgoffset . ", " . $msglimit . ";";
				$availmsgs = $conn->query($sql);
				if ($availmsgs->num_rows > 0) {
				    echo "{\"Title\" : \"User, Message\", \"Conversation\" : [";
				    $msgcount = 0;
				    while($row = $availmsgs->fetch_assoc()) {
					$currmess = "{\"User\" : \"" . $row["userID"];
					$currmess .= "\", \"Message\" : \"" . htmlspecialchars($row["msg"], ENT_QUOTES);
					$currmess .= "\", \"S\" : " . strval($row["S"]);
					$currmess .= ", \"Msgnum\" : " . $msgcount . "}, ";
				        echo $currmess;
					$msgcount += 1;
				    }
				    echo "{\"End\" : 1}]}";
				} else {
				    echo "{\"Results\" : 0}";
				}
			}
		}
	
		if (($loggedoff == "True") || ($loggedoff == "M")) {
		    $qms = asciiencrypt("e", " " . $client . " has quit.", 1);
			$sql = "INSERT INTO ChatMsgs (userID, msg, S) values ('ChatServer', '$qms', 1);";
			$conn->query($sql);

			$sql = "UPDATE ChatUsers SET Online = 0 WHERE Username=\"$client\";";
			$conn->query($sql);

				setcookie("IDHash", "", time()-100);
				setcookie("UserID", "", time()-100);
				header("Location: /ChatLogin.php");

		}
	}
	else {
		echo "Session timeout! Please reload the page.";
	}
}
?>
