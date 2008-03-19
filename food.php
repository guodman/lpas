<html>
<title>
FOOD!!
</title>

<body>
<?php

$names = array();
$condition = "";
if (array_key_exists("users", $_GET)) {
   $names = split(",", $_GET["users"]);
   $condition = "(a.User='" . join("' OR a.User='",$names) . "')";
}

$all_usernames = "SELECT DISTINCT User
FROM attendees";

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

@mysql_connect("localhost", "doug") or die("Could not connect to the database");
@mysql_select_db("restaurants") or die("Could not select database");

print("<b><u>Registered Users:</u></b><br>");
$all_usernames_result = mysql_query($all_usernames);
for ($count = 0; $count < mysql_numrows($all_usernames_result); $count++) {
			  print(mysql_result($all_usernames_result, $count)."<br>\n");
}

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

mysql_close();
?>
</body>
</html>
