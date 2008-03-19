<?php
session_start();
mysql_connect("localhost", "doug");
mysql_select_db("restaurants");

if(!$_SESSION["user"]) {
	$_SESSION["user"] = mysql_real_escape_string($_GET["user"]);
	
}

if(!$_SESSION["user"]) {
	$body = promptForUser();
} else if($_POST["action"] == "save") {
	$body = handleSave();
} else {
	$body = doMain();
}

?><html>
<head><title>Preference Editor</title></head>
<body><h1>Preference Editor</h1>
<?php echo $body; ?>
</body></html>

<?php

function doMain() {
	if($_GET["msg"]) {
		$result = "<p>" . $_GET["msg"] . "</p>";
	}
	$result .= "<p>Please enter a rating for the following restaurants on a scale from 1 to 100</p>";
	
	$sql = "SELECT r.ID, r.Name, a.Rating FROM restaurants r LEFT JOIN attendees a ON r.ID = a.RestaurantID AND a.User = '" . $_SESSION["user"] . "' ORDER BY r.Name";
	$results = mysql_query($sql);
	
	if(!$results) {
		echo mysql_error();
	}
	
	$result .= "<form method=\"post\">";
	while(list($id, $name, $rating) = mysql_fetch_row($results)) {
		if(!$rating) {
			$rating = "50";
		}
			
		$result .= "<div><strong>$name</strong><input type=\"text\" name=\"rating[$id]\" value=\"$rating\"/></div>";
	}
	$result .= "<button type=\"submit\" name=\"action\" value=\"save\">Save Preferences</button></form>";
	
	return $result;
}
	
function handleSave() {
	$sql = "DELETE FROM attendees WHERE User = '" . $_SESSION["user"] . "';";
	mysql_query($sql);
	
	foreach($_POST["rating"] as $key=>$value) {
		$inserts[] = "(" . mysql_real_escape_string($key) . ", '" . $_SESSION["user"] . "', " . mysql_real_escape_string($value) . ")";
	}
	
	$sql = "INSERT INTO attendees (RestaurantID, User, Rating) VALUES " . join(",", $inserts) . ";";
	$result = mysql_query($sql);
	
	if(!$result) {
		$response = "Error updating preferences." ;
		$response .= $sql;
		$response .= mysql_error();
	} else {
		$response = "Preferences Updated.";
	}
	$response = urlencode($response);
	
	forward($SERVER["PHP_SELF"] . "?msg=$response");
}

function promptForUser() {
	$result = "<form method=\"get\">Who are you? <input type=\"text\" name=\"user\"/><input type=\"submit\"></form>";
	return $result;
}

function forward($url) {
	header("Location: $url\n\n");
}
