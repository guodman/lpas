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

print showHeader("Ratings");

$db = new DB();

$all_usernames = "SELECT DISTINCT User FROM attendees";

$names = array();
$condition = "";

//Assemble a list of all names in the system.
$all_usernames_result = $db->query($all_usernames);
for ($count = 0; $count < mysql_numrows($all_usernames_result); $count++) {
   $all_names[$count] = mysql_result($all_usernames_result, $count);
}

// check if users are specified.  If they are, just use them. Otherwise, use all users.
if (array_key_exists("users", $_GET)) {
   $names = split(",", $_GET["users"]);
} else {
   // assume that the checkboxes were used.
   $count = 0;
   foreach($_GET as $n => $on) {
      if (in_array($n, $all_names)) {
         $names[$count] = $n;
         $count++;
      }
   }
}
// make sure that the list of users is not empty.
if (empty($names)) {
   $names = $all_names;
}

$condition = "(a.User='" . join("' OR a.User='",$names) . "')";

//print_r($names);


$all_ratings = "SELECT Name, User, Rating
FROM attendees a, restaurants r
WHERE a.RestaurantID = r.ID $condition";

//print("Ratings Statement: $all_ratings<br>\n\n");

$all_average_rating = "SELECT r.Name, SUM( a.Rating ) / COUNT( a.Rating ) AS Average_Rating
FROM attendees a, restaurants r
WHERE $condition AND r.ID = a.RestaurantID
GROUP BY r.Name ORDER BY Average_Rating DESC";
//print("$all_average_rating<br>\n\n");

$most_recent_visit = "SELECT Name, MAX( Date ) AS Last_Visit
FROM history h, restaurants r
WHERE h.RestaurantID = r.ID
GROUP BY RestaurantID
ORDER BY Last_Visit";

print("<h2>Registered Users:</h2>");
print("<form class=\"manyColumns\">");
$all_usernames_result = $db->query($all_usernames);
for ($count = 0; $count < mysql_numrows($all_usernames_result); $count++) {
//			  print(mysql_result($all_usernames_result, $count)."<br>\n");
   $curname = mysql_result($all_usernames_result, $count);
   if (in_array($curname, $names)) {
      print "<div><label class=\"inline\"><input type=\"checkbox\" name=\"$curname\" checked=\"checked\" /> " . $curname . "</label></div>\n";
   } else {
      print "<div><label class=\"inline\"><input type=\"checkbox\" name=\"$curname\" /> " . $curname . "</div></label>\n";
   }
}
print("<input type=submit value=submit \>");
print("</form>");

print("<h2>Rated Restaurants:</h2>
<table><thead>
  <tr><td><strong>Restaurant Name</strong></td><td><strong>Average Rating</strong></td></tr></thead><tbody>\n");
$result = $db->query($all_average_rating);

if(!$result) { print mysql_error(); }

$data = array();
while(list($name, $rating) = mysql_fetch_row($result)) {
  $data[$name] = $rating;
}

foreach($data as $key=>$value) {
    $value = round($value,1);
    print("  <tr><td>$key</td><td>$value</tr>\n");
}

print("</tbody></table>\n");


print("<br>
<h2>Recent Visits:</h2>
<table>
  <thead><tr><td><strong>Restaurant Name</strong></td><td><strong>Date of Last Visit</strong></td></tr></thead><tbody>\n");
$recent_result = $db->query($most_recent_visit);
$history = array();
while(list($name, $date) = mysql_fetch_row($recent_result)) {
		  $history[$name] = $date;
}
foreach($history as $key=>$value) {
    print("  <tr><td>$key</td><td>$value</tr>\n");
}

print("</tbody></table>\n");

print '<div class="suggestions">';
// Suggestion 1
print '<div class="suggestion">';
	$count = 0;
	foreach($history as $key=>$value) {
		$count++;
		$data[$key] = round($data[$key]/$count,1);
	}
	arsort($data);

	print "<h2>Suggestions #1<br/>(score = average rating / t):</h2>
	<table><thead>
	<tr><td>Restaurant Name</td><td><strong>Score</strong></td></tr></thead><tbody>\n";
	foreach($data as $key=>$value) {
		print "  <tr class=\"" . ($q++ % 2 == 0 ? "even" : "odd") . "\"><td>$key</td><td>$value</tr>\n";
	}
	print "</tbody></table>\n";



	
print '</div>';


// Suggestion 2
print '<div class="suggestion">';
	$count = 0;
	foreach($history as $key=>$value) {
		$count++;
		$data[$key] = round($data[$key]/$count,1);
	}
	arsort($data);
	print "<h2>Suggestions #2<br/>(score = average rating / t^2):</h2>
	<table><thead>
	<tr><td>Restaurant Name</td><td>Score</td></tr></thead><tbody>\n" ;
	foreach($data as $key=>$value) {
	print "  <tr class=\"" . ($q++ % 2 == 0 ? "even" : "odd") . "\"><td>$key</td><td>$value</tr>\n";
	}
	print("</tbody></table>\n");
print '</div>';





// Suggestion 3
print '<div class="suggestion">';
	$result = $db->query($all_average_rating);

	if(!$result) { print mysql_error(); }

	$data = array();
	while(list($name, $rating) = mysql_fetch_row($result)) {
	$data[$name] = $rating;
	}

	$count = 0;
	foreach($history as $key=>$value) {
	$count++;
	$data[$key] = round($data[$key]/($count+5),1);
	}
	arsort($data);

	print "<h2>Suggestions #3<br/> (score = average rating / (t+5)):</h2>
	<table>
	<thead><tr><td><strong>Restaurant Name</strong></td><td><strong>Score</strong></td></tr></thead><tbody>\n";
	
	foreach($data as $key=>$value) {
	print("  <tr class=\"" . ($q++ % 2 == 0 ? "even" : "odd") . "\"><td>$key</td><td>$value</tr>\n");
	}
	print "</tbody></table>\n";
print '</div>';
print '</div>';


print showFooter();
