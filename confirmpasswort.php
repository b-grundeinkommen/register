<?php

// (c) 2005 Michael Schierl
// (c) 2005 Arnold Schiller
// (c) 2005 Mike Lischke
// (c) 2016 Arnold Schiller
// #include <gpl.txt>
$title = "Passwortbest&auml;tigung";
error_reporting(E_ALL);

define('WAS_REQUIRED', true);
require("include.php");

$messageColor = "black";
$message = "&nbsp;";
include("headhtml.inc.php");

if (!isset($_GET['token'])) {
	die("Bitte den kompletten Link aus der Mail anklicken!");
}

// Start with connection to the database.
$sql = "SELECT name, email from tokens where token = \"" . addslashes($_GET['token']) . "\"";

try {
    //connect as appropriate as above
	$stmt = $dbconn->query($sql);
} catch(PDOException $ex) {
                echo "Mist ein Fehler ist aufgetreten confirmpassword";
}

// $result = mysql_query($sql, $dbconn);
// if (!$result)
//	echo "SQL-Fehler: " . mysql_error();

//$row = mysql_fetch_row($result);

$row_count = $stmt->rowCount();
$row = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$row)
	echo "Fehler bei der Bestätigung. Entweder ist die Zeit für die Bestätigung schon abgelaufen oder der Zugang ist bereits eingerichtet.";
else
{
	$name = $row['name'];
	$email = $row['email'];

	include("ldap.php");
	
	$errormsg = mod_ldap_passwort($name, $email);

	if ($errormsg == "")
	{
		$sql = "DELETE FROM tokens WHERE token = \"".addslashes($_GET['token']) . "\"";
		try {
		        $dbconn->query($sql);
		}  catch(PDOException $ex) {
                echo "Mist ein Fehler ist aufgetreten Delete tokens";
	        }
		/*
		$result = mysql_query($sql, $dbconn);
		if (!$result)
			 echo "SQL-Fehler: " . mysql_error();
		*/
	}
	else
		echo $errormsg;
}

include("foothtml.inc.php");

?>
