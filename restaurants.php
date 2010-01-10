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
$db = new DB();

$action = $_GET["action"];
$id = $db->escape($_GET["id"]);
$name = $db->escape($_GET["name"]);

if($action == "delete" && $id) {
	$db->query("DELETE FROM restaurants WHERE id='$id' LIMIT 1");
	forward("restaurants.php");
	
} else if ($action == "add" && $name) {
	$sql = "INSERT INTO restaurants (Name) VALUES ('$name');";
	$db->query($sql);
	forward("restaurants.php");
}


$db->query("SELECT ID,Name from restaurants ORDER BY Name DESC");
$body .= "<table><thead><tr><td>Name</td><td>Delete</td></tr></thead><tbody>";
while(list($id,$rName) = $db->fetchrow()) {
	$body .= "<tr><td>$rName</td><td><a href=\"?action=delete&id=$id\"><img src=\"delete.png\" alt=\"delete\"/></a></td></tr>";
}
$body .= "</tbody></table>";

$body .= '<br/><form method="get"><input type="hidden" name="action" value="add"/>
	Add new Restaurant: <input type="text" placeholder="Restaurant Name" name="name"/><button type="submit">Create</button></form>';


// Render to Screen
print showHeader("Restaurants");
print $body;


