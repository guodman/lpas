<?php
/**
* Functions.inc.php
* Provides useful display abstraction, as well as database objects and connectivity.
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
 
require("settings.inc.php");
session_start();

function showError($msg) {
	print "Error: $msg<br/>\n";
	exit;
}

function showHeader($title) {
	print '<!DOCTYPE HTML>
	<html lang="en">
	<link href="lpas.css" rel="stylesheet" type="text/css" />
	<script src="jquery-latest.js"></script>
<script src="ui.datepicker.js"></script>
<link rel="stylesheet" href="flora.datepicker.css" type="text/css" media="screen" title="Flora (Default)">

	<title>
	LPAS - Lunch Preference Aggregation System
	</title>
	
	<body>
	<div align=center style="font-size:25">
	<a href="food.php">Ratings</a> |
	<a href="preferences.php">Preferences</a> |
	<a href="history.php">History</a> |
	<a href="restaurants.php">Restaurants</a>
	</div>';
	print '<body><div id="main"><h1>' . $title . '</h1>';
}
function showFooter() {
	print "</div><div id=\"footer\"><a href=\"logout.php\">Logout</a><br/><em>Lunch Preference Aggregation System</em> - Brought to you by Doug and Stephen</body></html>";
}
function display($title,$content) {
	print showHeader($title);
	print $content;
	print showFooter();
	exit;
}


	

function requireUser() {
	if(!$_SESSION["user"]) {
		if(!$_POST["user"]) {
			display("Valid User Required",promptForUser());
		} else {
			$_SESSION["user"] = $_POST["user"];
		}
	}
}

function promptForUser() {
	$result = "<form method=\"post\">Who are you? <input type=\"text\" name=\"user\"/><input type=\"submit\"></form>";
	return $result;
}

function forward($url) {
	header("Location: $url\n\n");
}

class DB {
	var $conn, $quiet;

	function DB() {
		if(!$conn) {
			$this->conn = @mysql_connect($GLOBALS["dbHost"], $GLOBALS["dbUser"], $GLOBALS["dbPass"]) or die("Could not connect to the database (" . mysql_error() . ")");
			@mysql_select_db($GLOBALS["dbDatabase"],$this->conn) or die("Could not select database");
			// print "DB Connected";
		}
	}
	
	function query($query = "") {
		$this->results = mysql_query($query,$this->conn );
		if(!$this->results && !$this->quiet) {
			print "Server Error: (" . mysql_error($this->conn) . ") '$query'.";
		}
		return $this->results;
	}
	function size() {
                return mysql_num_rows($this->results);
        }
        function fetchrow() {
                return mysql_fetch_array( $this->results , MYSQL_NUM );
        }
	function escape($string) {
		return mysql_real_escape_string($string);
	}
}
