<?php
function newSessionID($name) {
	$cdt = getdate();
	$secstime = $cdt[0];
	$date = $cdt["yday"];
	
	$sessID = strval(($secstime * 17) % 1583) . strval($secstime % 47) . substr($name, 0, 1);
	$sessID .= strval(366 - $date) . substr($name, -1) . strval(500 - (($secstime * 23) % 101));
	return $sessID;
}

function getUser($ipaddr, $sessID) {
	$ruser = "";
	$servername = "localhost";
	$username = "AXI_1";
	$password = "r94tni+oC^4@";
	$conn = new mysqli($servername, $username, $password);
	if ($conn->connect_error) {
	    die("Connection failed.");
	}
	$sql = "USE ChatData;";
	$conn->query($sql);
	$sql = "SELECT Username FROM ChatRef WHERE IP=\"" . $ipaddr . "\" AND Sessionid=\"" . $sessID . "\";";
	$usrval = $conn->query($sql);
	if ($usrval->num_rows > 0) {
		$usrrow = $usrval->fetch_assoc();
		$ruser = $usrrow["Username"];
	}
	return $ruser;
}
?>
