<?php
require("settings.inc.php");

function showError($msg) {
	print "Error: $msg<br/>\n";
	exit;
}

function showHeader($title) {
	print '<html>
	<link href="lpas.css" rel="stylesheet" type="text/css" />
	
	<title>
	LPAS - Lunch Preference Aggregation System
	</title>
	
	<body>
	<div align=center style="font-size:25">
	<a href="food.php">Ratings</a> |
	<a href="preferences.php">Preferences</a> |
	<del>History</del>
	</div>';
	print '<body><div id="main"><h1>' . $title . '</h1>';
}
function showFooter() {
	print "</div></body></html>";
}

class DB {
	var $conn;

	function DB() {
		if(!$conn) {
			$this->conn = @mysql_connect($GLOBALS["dbHost"], $GLOBALS["dbUser"], $GLOBALS["dbPass"]) or die("Could not connect to the database (" . mysql_error() . ")");
			@mysql_select_db($GLOBALS["dbDatabase"],$this->conn) or die("Could not select database");
			// print "DB Connected";
		}
	}
	
	function query($query = "") {
		$results = mysql_query($query,$this->conn );
		if(!$results) {
			print "Server Error: (" . mysql_error($this->conn) . ") '$query'.";
		}
		return $results;
	}
}
