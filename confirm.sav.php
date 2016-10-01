<?php

// (c) 2005 Michael Schierl
// (c) 2005 Arnold Schiller
// (c) 2005 Mike Lischke
// (c) 2016 Arnold Schiller
// #include <gpl.txt>
$title = "BGEServer Registrierung";
error_reporting(E_ALL);

define('WAS_REQUIRED', true);
require("include.php");
require_once("config.php");
$messageColor = "black";
$message = "&nbsp;";
include("headhtml.inc.php");

?>
<table width="100%" border="0" style="font-size: 13px" cellspacing="10">
<tr>
<td>
<?php
if (!isset($_GET['token'])) {
	die("Bitte den kompletten Link aus der Mail anklicken!");
}

// Start with connection to the database.
	$sql = "SELECT name, email from tokens where token = \"" . addslashes($_GET['token']) . "\"";
// $result = mysql_query($sql, $dbconn);
// if (!$result)
//	echo "SQL-Fehler: " . mysql_error();

        try {
        $stmt = $dbconn->query($sql);
        } catch(PDOException $ex) {
                echo "Mist ein Fehler ist aufgetreten";
        }
        //$row = mysql_fetch_row($result);
        $row_count = $stmt->rowCount();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$row)
	echo "Fehler bei der Bestätigung. Entweder ist die Zeit für die Bestätigung schon abgelaufen oder der Zugang ist bereits eingerichtet.";
else
{
	echo "Hallo ".$row['name']",<br>";
	echo "deine ".$row['email']." Email-Adresse wird eingetragen!";
	//	echo $row['token'];

	$name = $row['name'];

	$email = $row['email'];
	//	echo ",<br>  es funktioniert noch nicht!"; 
	//	$ds=ldap_connect(LDAP_HOST);  // must be a valid LDAP server!
	// echo "connect result is " . $ds . "<br />";
	include("ldap.php");	
	$errormsg = @mk_ldap_account($name, $email);
	if ($errormsg == "")
	{
		$sql = "DELETE FROM tokens WHERE token = \"".addslashes($_GET['token']) . "\"";
		$result = mysql_query($sql, $dbconn);
		if (!$result)
			 echo "SQL-Fehler: " . mysql_error();
	}
	else
		echo $errormsg;
}

?>
</tr>
</td>
</table>
<?php

include("foothtml.inc.php"); 

?>
