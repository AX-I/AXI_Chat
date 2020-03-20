<?php
    $client = $_POST["User"];
    $sessID = $_COOKIE["IDHash"];
    $clientID = $_COOKIE["UserID"];
    $ip = (string)$_SERVER["REMOTE_ADDR"];
    $validlogin = False;

    $client = str_replace(array("\\", "\"", "<", ">", "/"), "", $client);
    if (ctype_space($client)) {
	header("Location: /ChatLogin.php?result=invalidname");
	exit("Invalid username");
    }

    if (($client == NULL) && ($clientID == NULL)) {
	header("Location: /ChatLogin.php");
	exit("Please login");
    }
    if ($sessID == NULL) {
	include "ChatValidate.php";
	exit();
    }
?>
<?php
	$servername = "localhost";
	$username = "User111";
	$password = "Password111";

	$conn = new mysqli($servername, $username, $password);
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	$clientID = $conn->real_escape_string($clientID);

	$sql = "USE ChatData;";
	$conn->query($sql);
	$ipvalidate = "X";
	$sessvalidate = "X";
	$sql = "SELECT IP, SessionID FROM ChatRef WHERE Username=\"$clientID\" ORDER BY eID DESC;";
	$refresult = $conn->query($sql);
	if ($refresult->num_rows > 0) {
		$row = $refresult->fetch_assoc();
		$ipvalidate = $row["IP"];
		$sessvalidate = $row["SessionID"];

		if ($ip == $ipvalidate) {
			if ($sessID == $sessvalidate) {
				$validlogin = True;
			}
			else {
				setcookie("IDHash", "", time()-100);
				setcookie("UserID", "", time()-100);
				header("Location: /ChatLogin.php?result=invalidsess");
				exit("Please login again.");
			}
		}
		else {
			setcookie("IDHash", "", time()-100);
			header("Location: /ChatLogin.php?result=invalidip");
			exit("Please login again.");
		}
	}
	else {
		setcookie("UserID", "", time()-100);
		header("Location: /ChatLogin.php?result=noid");
		exit("Please login again.");
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
  <title>AXI Chat WebApp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="../Appstyle.css" type="text/css" media="all">
  <link rel="shortcut icon" href="../Logo.PNG">
  <script src="AXIEncrypt.js"></script>
  <script src="Chatrequests.js"></script>
  
  <style>
    #Header {border:3px dashed blue; padding:5px}
    #Manlogoff {float:right;}
    #Console {text-align:center}
    #Control {text-align:center; margin:4px}
    #Conversation {text-align:center; clear:both;}
    #Conversation table {margin:auto; width:60%; background-color:#EDC}
    .num {width:5em;}
    
    #iu {height:4em; float:left;}
    #extInfo {float:left; padding-left:5px; width:16em;}
    #clear {clear:both;}
    .f {display:flex; justify-content:center;}
    
    @media only screen and (max-width:768px) {
      #Conversation table {margin:0; width:98%; max-width:100%;}
      #Conversation table tbody tr td {word-wrap:break-word}
      #User {width:20%}
      #Message {width:80%}
      #extInfo {clear:both;}
      .f {display:block;}
    }
    th, td {border-bottom:1px solid black; max-width:80vw; overflow-wrap:break-word;}
    .Info {background-color:#CCE}
    .Self {background-color:#DCB}
    #Addoptions {display:block; margin:4px auto; width:80%; background-color:#EDC;}
    .Main {border:3px ridge #CBA; width:100%;}
  </style>
  <script>
    function toggleoptions() {
      optionmenu = document.getElementById("Addoptions");
      if (optionmenu.style.display != "block") optionmenu.style.display = "block";
      else optionmenu.style.display = "none";
    }
    function enterHandler(eID) {
      document.getElementById(eID).addEventListener("keypress", function (k) {
        if (k.keyCode == 13) sendmsg();
      });
    }
    function unload() {
      ;      
    }
    function setupIU() {
        var a = document.getElementById("iu");
        a.onload = function() {
            var q = this.contentDocument.body.innerText;
            if (q.substring(0, 8) == "The file") {
                sendmsg("{{i " + q.substring(q.indexOf("[") + 1, q.indexOf("]")) + "}}");
            }
        };
    }
  </script>
</head>
<body onload="enterHandler('Send'); autorefresh(); getUsername(); setupIU(); " onbeforeunload="unload();">
  <div id="Header">
    Logged in as <span id="Thisuser"><?php echo $clientID; ?></span><br>
    Session lasts 1 hr<span id="ch">.</span>
    <button id="Manlogoff" onclick="logoff(true);">Log off</button>
  </div>
  <div id="Console">
    Javascript is required to use the WebApp.
  </div>
  <script>
    document.getElementById("Console").innerHTML = "Welcome.";
  </script>
  <div id="Control">
    Message:
    <input type="text" id="Send" />
    <button onclick="sendmsg();">Send</button>
    <button onclick="refreshmsgs();">Refresh</button>
    <input type="checkbox" id="Autoref" onclick="autorefresh();" checked />Automatically refresh<br>
    <button onclick="toggleoptions();">Show/hide additional options</button><br>
    <div id="Addoptions" class="Main">
      Maximum messages to show: <input type="number" class="num" id="Msglimit" value="80" min="1" max="200" />
      Start from: <input type="number" class="num" id="Msgoffset" value="0" min="0" max="1000" />
      <button onclick="refreshmsglimit();">Update limit</button>
      <!--<button onclick="toggledates();">Toggle timestamp</button>-->
      <br>
      ...
      <br>
      <div class="f">
          <iframe id="iu" src="Image.php"></iframe>
          <div id="extInfo">To insert an external image use <br>{{e https://image.png}}</div>
      </div>
      <div id="clear"></div>
    </div>
  </div>
  <div id="Conversation">
    Press the Refresh button to get started.
  </div>
</body>
</html>
