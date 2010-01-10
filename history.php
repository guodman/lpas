<?php
/**
 * Copyright (C) 2008 Stephen Fluin stephen.fluin@gmail.com
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

if($_POST["action"] == "new") {
	$body = newHistory();
} else if($_GET["action"] == "delete") {
	$body = deleteHistory();
}
$body .= main();


print showHeader("History");
echo $body;
print showFooter();


function main() {
	$body .= '
	<script><!--
	$(document).ready(function(){
	$("#visit").datepicker({
		firstDay: 1
});
  });
-->
	</script>
<fieldset><legend>Add a new visit</legend><form method="post">
		<label>Restaurant <select name="restaurant">';
	foreach(getRestaurantList() as $id=>$name) {
		$body .= '<option value="'.$id.'">'.$name.'</option>';
	}
	$body .= '</select></label>
		<label>Date <input type="text" name="date"  id="visit" value="'.date("m/d/Y").'"/></label>
		<label>Duration (minutes)<input type="text" name="duration"/></label>
		<button type="submit" name="action" value="new">Save New Visit</button>
		</form></fieldset>';
	
	
	$db = new DB();
	$db->query("SELECT h.RestaurantID,r.Name,h.Date,h.Duration
		    FROM history h
		    	JOIN restaurants r ON h.RestaurantID = r.ID
		    ORDER BY h.Date DESC");
	if($db->size() == 0) {
		return "<span class=\"error\">No history found.</span>";
	}
	$body .= "<h2 style='margin-top:20px;'>Past Visits</h2><table><thead><tr><td>Restaurant Name</td><td>Date of Visit</td><td>Duration</td></tr></thead><tbody>";
	while(list($rid,$rName,$date,$duration) = $db->fetchrow()) {
		$body .= "<tr class=\"" . ($q++ % 2 == 0 ? "even" : "odd") . "\" id=\"hist$rid\"><td>$rName</td><td>$date</td><td>$duration minutes</td><td><a href=\"?action=delete&id=$rid&date=$date\"><img src=\"delete.png\" alt=\"Delete this entry\" style=\"width:50%\" /></a></td></tr>";
		// Undesireable slide animation:
		//onclick=\"$('#hist$rid').hide('slide',{direction:'up'},'slow');\"
	}
	
	$body .= "</tbody></table>";
	return $body;
	
}

function newHistory() {
	$db = new DB();
	$restaurant = 	$db->escape($_POST["restaurant"]);
	$date =		strtotime($db->escape($_POST["date"]));
	$duration = 	$db->escape($_POST["duration"]);
	
	$date = date("Ymd",$date);
	
	if($restaurant && $date && $duration) {
		$sql = "INSERT INTO history (RestaurantID, Date, Duration) VALUES ('$restaurant','$date','$duration');";
		$db->quiet = true;
		$db->query($sql);
		//print $sql;
		if(mysql_error()) {
			return "<span class=\"error\">There is already an entry for this day!</span>";
		}
		forward($_SERVER["PHP_SELF"]);
	} else {
		return "<span class=\"error\">Wrong data submitted</span>";
	}
}

function deleteHistory() {
	$db = new DB();
	$id = $db->escape($_GET["id"]);
	$date = $db->escape($_GET["date"]);
	if($id && $date) {
		$db->query("DELETE FROM history WHERE RestaurantID = '$id' AND Date = '$date' LIMIT 1;");
		forward($_SERVER["PHP_SELF"]);
	} else {
		return "<span class=\"error\">Missing expected values.</span>";
	}
}
function getRestaurantList() {
	$db = new DB();
	$db->query("SELECT ID, Name FROM restaurants;");
	while(list($id,$name) = $db->fetchrow()) {
		$result[$id] = $name;
	}
	return $result;
}
	