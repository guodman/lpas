<?php
require("settings.inc.php");

function showError($msg) {
	print "Error: $msg<br/>\n";
	exit;
}

class DB {
	var $conn;

	function DB() {
		if(!$conn) {
			$this->conn = @mysql_connect($GLOBALS["dbHost"], $GLOBALS["dbUser"], $GLOBALS["dbPass"]) or die("Could not connect to the database (" . mysql_error() . ")");
			@mysql_select_db("restaurant",$this->conn) or die("Could not select database");
			print "DB Connected";
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