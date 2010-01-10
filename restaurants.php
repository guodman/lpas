<?php
/**
*	Manage restaurants table.
*/

require("functions.inc.php");
$db = new DB();

$action = $_GET["action"];
$id = $db->escape($_GET["action"]);
$name = $db->escape($_GET["name"]);

if($action == "delete" && $id) {
	$db->query("DELETE FROM restaurants WHERE id='$id' LIMIT 1");
	forward("restaurants.php");
	
} else if ($action == "add" && $name) {

}


$db->query("SELECT ID,Name from restaurants ORDER BY Name DESC");
$body .= "<table><thead><tr><td>Name</td><td>Delete</td></tr></thead><tbody>";
while(list($id,$rName) = $db->fetchrow()) {
	$body .= "<tr><td>$rName</td><td><a href=\"?action=delete&id=$id\"><img src=\"delete.png\" alt=\"delete\"/></a></td></tr>";
}
$body .= "</tbody></table>";


// Render to Screen
print showHeader("Ratings");
print $body;



function forward($url) {
	header("Location: $url\n\n");
	exit;
}