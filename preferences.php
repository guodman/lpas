<?php
/**
 * Copyright (C) 2007 Doug Reeves dreeves@bluebottle.com
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 *
 * see http://www.gnu.org/licenses/gpl.html for more information
 */

require("functions.inc.php");

session_start();
$db = new DB();

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
	$results = $db->query($sql);
	
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
	$db->query($sql);
	
	foreach($_POST["rating"] as $key=>$value) {
		$inserts[] = "(" . mysql_real_escape_string($key) . ", '" . $_SESSION["user"] . "', " . mysql_real_escape_string($value) . ")";
	}
	
	$sql = "INSERT INTO attendees (RestaurantID, User, Rating) VALUES " . join(",", $inserts) . ";";
	$result = $db->query($sql);
	
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
