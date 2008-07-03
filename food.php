<!--
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
-->
<html>
<title>
FOOD!!
</title>

<body>
<div align=center style="font-size:25">
Ratings |
<a href="preferences.php">Preferences</a> |
<del>History</del>
</div>
<?php

@mysql_connect("localhost", "doug") or die("Could not connect to the database");
@mysql_select_db("restaurants") or die("Could not select database");

$all_usernames = "SELECT DISTINCT User
FROM attendees";

$names = array();
$condition = "";

//Assemble a list of all names in the system.
$all_usernames_result = mysql_query($all_usernames);
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

print("<b><u>Registered Users:</u></b><br>");
print("<form action=food.php>");
$all_usernames_result = mysql_query($all_usernames);
for ($count = 0; $count < mysql_numrows($all_usernames_result); $count++) {
//			  print(mysql_result($all_usernames_result, $count)."<br>\n");
   $curname = mysql_result($all_usernames_result, $count);
   if (in_array($curname, $names)) {
      print("<input type=checkbox name=$curname checked \> " . $curname . "<br>");
   } else {
      print("<input type=checkbox name=$curname \> " . $curname . "<br>");
   }
}
print("<input type=submit value=submit \>");
print("</form>");

print("<br>
<b><u>Rated Restaurants:</u></b><br>
<table border=1>
  <tr><td><b>Restaurant Name</b></td><td><b>Average Rating</b></td></tr>\n");
$result = mysql_query($all_average_rating);

if(!$result) { print mysql_error(); }

$data = array();
while(list($name, $rating) = mysql_fetch_row($result)) {
  $data[$name] = $rating;
}

foreach($data as $key=>$value) {
    print("  <tr><td>$key</td><td>$value</tr>\n");
}

print("</table>\n");


print("<br>
<b><u>Recent Visits:</u></b><br><TABLE BORDER=1>
  <tr><td><b>Restaurant Name</b></td><td><b>Date of Last Visit</b></td></tr>\n");
$recent_result = mysql_query($most_recent_visit);
$history = array();
while(list($name, $date) = mysql_fetch_row($recent_result)) {
		  $history[$name] = $date;
}
foreach($history as $key=>$value) {
    print("  <tr><td>$key</td><td>$value</tr>\n");
}

print("</table>\n");

print("<table><tr><td>");

$count = 0;
foreach($history as $key=>$value) {
  $count++;
  $data[$key] = $data[$key]/$count;
}
arsort($data);

print("<br>
<b><u>Suggestions (score = average rating / t):</u></b><br>
<table border=1>
  <tr><td><b>Restaurant Name</b></td><td><b>Score</b></td></tr>\n");
foreach($data as $key=>$value) {
    print("  <tr><td>$key</td><td>$value</tr>\n");
}
print("</table>\n");

print("</td><td>");

$count = 0;
foreach($history as $key=>$value) {
  $count++;
  $data[$key] = $data[$key]/$count;
}
arsort($data);

print("<br>
<b><u>Suggestions (score = average rating / t^2):</u></b><br>
<table border=1>
  <tr><td><b>Restaurant Name</b></td><td><b>Score</b></td></tr>\n");
foreach($data as $key=>$value) {
    print("  <tr><td>$key</td><td>$value</tr>\n");
}
print("</table>\n");

print("</td><td>");

$result = mysql_query($all_average_rating);

if(!$result) { print mysql_error(); }

$data = array();
while(list($name, $rating) = mysql_fetch_row($result)) {
  $data[$name] = $rating;
}

$count = 0;
foreach($history as $key=>$value) {
  $count++;
  $data[$key] = $data[$key]/($count+5);
}
arsort($data);

print("</td><td>");

print("<br>
<b><u>Suggestions (score = average rating / (t+5)):</u></b><br>
<table border=1>
  <tr><td><b>Restaurant Name</b></td><td><b>Score</b></td></tr>\n");
foreach($data as $key=>$value) {
    print("  <tr><td>$key</td><td>$value</tr>\n");
}
print("</table>\n");

print("</td></tr></table>");

mysql_close();
?>
</body>
</html>
