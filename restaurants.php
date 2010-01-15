<?php
/**
*	Manage restaurants table.
*
* Copyright (C) 2010 Stephen Fluin stephen.fluin@gmail.com
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
requireUser();
$db = new DB();

$action = $_GET["action"];
$id = $db->escape($_GET["id"]);
$name = $db->escape($_GET["name"]);
$note = $db->escape($_GET["note"]);

if($action == "delete" && $id) {
	$db->query("DELETE FROM restaurants WHERE id='$id' LIMIT 1");
	forward("restaurants.php");
	
} else if ($action == "save" && $id && $name) {
	$sql = "UPDATE restaurants SET Name = '$name', Note = '$note' WHERE id='$id' LIMIT 1;";
	$db->query($sql);
	forward("restaurants.php");
} else if ($action == "add" && $name) {
	$sql = "INSERT INTO restaurants (Name, Note) VALUES ('$name', '$note');";
	$db->query($sql);
	forward("restaurants.php");
}
if ($action == "edit" && $id) {
	$sql = "SELECT Name, Note FROM restaurants WHERE id='$id' LIMIT 1;";
	$db->query($sql);
	if($row = $db->fetchrow()) {
		list($name, $note) = $row;
		$button = "Save";
		$action = "save";
		$task = "Edit";
		$idInput = '<input type="hidden" name="id" value="' . $id . '"/>';
	}
	
}
if(!$name || !$task || !$button) {
	$button = "Create";
	$action = "add";
	$task = "Add New";
	$idInput = "";
}
	

$db->query("SELECT ID,Name,Note from restaurants ORDER BY Name DESC");
$body .= "<table><thead><tr><td>Name</td><td>Note</td><td>Delete</td></tr></thead><tbody>";
while(list($id,$rName,$rNote) = $db->fetchrow()) {
	$body .= "<tr><td><a href=\"?action=edit&id=$id\">$rName</a></td><td>$rNote</td><td><a href=\"?action=delete&id=$id\" onclick=\"return confirm('Deleting this restaurant will destroy all history and preference data stored with it. Are you sure?!')\"><img src=\"delete.png\" alt=\"delete\"/></a></td></tr>";
}
$body .= "</tbody></table>";

$body .= '<br/><form method="get"><input type="hidden" name="action" value="' . $action . '"/>' . $idInput . '
	<h2>' . $task . ' Restaurant:</h2> <input type="text" placeholder="Restaurant Name" name="name" value="'.$name.'"/><br/>Notes:<br/>
	<textarea name="note" style="width:400px;height:100px;">' . htmlentities($note) . '</textarea><br/><button type="submit">' .$button . '</button></form>';


// Render to Screen
print showHeader("Restaurants");
print $body;
print showFooter();


