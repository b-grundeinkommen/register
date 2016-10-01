<?php

// (c) 2005 Michael Schierl
// (c) 2005 Arnold Schiller
// (c) 2005 Mike Lischke
// #include <gpl.txt>

if (!defined('WAS_REQUIRED') )
{
	header("HTTP/1.0 403 Forbidden");
	die();
}

require_once("config.php");
require_once("ldap.php");
// Connect to database first and branch out here already if that fails.
// $dbconn = mysql_connect(DB_HOST, DB_USER, DB_PASS);
// $selected = mysql_select_db(MYSQL_DB, $dbconn);
$dbconn = new PDO('mysql:host='.DB_HOST.';dbname='.MYSQL_DB.';charset=utf8mb4', DB_USER, DB_PASS);




// if (!$selected) {
//	die("Could not select DB: ".mysql_error()." ");
//}



/*
 * Checks if mail address and name look like something useful and checks also if the given
 * email address isn't already in use.
 * @return An empty string if all is ok, otherwise an error message.
 */
function checkdata($email, $name)
{
  global $tokenTimeout, $dbconn;

  // Check first if a token entry for the given mail address exists and is outdated already.
  $time = time();
	$sql = "select count(*) from tokens where (email = \"" . addslashes($email) . "\") and (`when` + $tokenTimeout < $time)";
	// $result = mysql_query($sql, $dbconn);
	// if (!$result)
	//	die("SQL error: " . mysql_error());
	try {
	$stmt = $dbconn->query($sql); 
	} catch(PDOException $ex) {
		echo "Mist ein Fehler ist aufgetreten";
	}
	//$row = mysql_fetch_row($result);
        $row_count = $stmt->rowCount();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($row[0] > 0)
		return "Die erlaubte Wartezeit für die Bestätigung ist mittlerweile abgelaufen. Bitte erneut registrieren.";

	if ( eregi("\n",$name) || eregi("\r",$name) || eregi("\n",$email) || eregi("\r",$email))
		die("Invalid request data.");

	$mail_regex = "/^[_A-Za-z0-9+-]+(\\.[_A-Za-z0-9+-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*$/";
	// For realname we admit "normal" Names like "Alpha Centauri" but also "Sammy Davis Jr." A-Za-zÄ-Üä-üß  auf A-Za-z-äöüÄÖÜß geändert Arnold Schiller
	
	$realname_regex='/^[A-Za-z-äöüÄÖÜß]+( [A-Za-z-äöüÄÖÜß]+\.?)+$/';

	if (!preg_match( $mail_regex, $email))
		return "Bitte eine gültige Mailadresse angeben!";

	if ($name == "")
		return "Bitte einen Namen eingeben.";
	// in register ist htmlentities auf $realname angewandt
	// deswegen funktionieren umlaute nicht
	// in functions.php decode_entities eingefügt
	$name = decode_entities($name);
	if (!preg_match($realname_regex, $name))
		return "Bitte einen üblichen Namen eingeben (Vorname Nachname).";


	
	$result = ldap_account_exists($email);

	if($result === 0){
        	// echo "Account existiert nicht"; // make nothing acount does not exist
		}else{
        	return  "Account existiert bereits $result Mal";
	}


	return "validated";
}

/*
 * Generates a token and saves it in the database if there is no entry for the given email yet.
 * In case an entry exists already a counter is increased.
 * On exit the variables $howoften is set to the value how often a token was already requested
 * for the given mail address and $token is set to the generated token or returned token value.
 *
 * @param $email The mail address for which to create or retrieve a token.
 * @param $realname A real name that belongs to the given address. Only used when a new record
 *				gets inserted into the database.
 * @return A new or existing token string.
 */
function gen_save_token($email, $realname)
{

	global $howoften, $dbconn;

	$sql = "SELECT token, howoften from tokens where email= \"" . addslashes($email) . "\"";
	try{
		$dbconn->query($sql);
		$stmt = $dbconn->query($sql);
       	} catch(PDOException $ex) {
 		   echo "An Error occured! $ex"; //user friendly message
   		//	 some_logging_function($ex->getMessage());
	}

	$row_count = $stmt->rowCount();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

	//	$result = mysql_query($sql, $dbconn);
	//     if (!$result)
	//  	die("SQL error: " . mysql_error());
	///$row = mysql_fetch_row($result);

  // Insert a new row if this is the first time a user with the given mail address registers.
	if ($row_count === 0)
	{
		$token = md5(uniqid(microtime()));
		$sql = "INSERT INTO tokens (name, email, token, howoften, `when`) " .
      "VALUES (\"" . addslashes($realname) . "\", \"" . addslashes($email) . "\", \"" . $token . "\", 1, " . time() . ")";
	}
	else
	{
	  // Update registration count of that user if (s)he already registered before.
	  $token = $row['token'];
	  $howoften = $row['howoften'];
		$sql = "UPDATE tokens SET howoften = howoften + 1 where email =\"" . addslashes($email) . "\"";
	};
	try {
   		 //connect as appropriate as above	
		$dbconn->query($sql);
	} catch(PDOException $ex) {
                echo "Mist ein Fehler ist aufgetreten: $ex";
        }

	//$result = mysql_query($sql, $dbconn);
	// if (!$result)
	//	die("SQL error: " . mysql_error());

	return $token;
}

/**
 * Sends a mail to the given address, which is used for confirmation.
 *
 * @param $email The target mail address.
 * @param $realname The name of the user to create a personalized greeting.
 * @param $token A token string used to identify the user.
 * @return True if sending the mail was successful, otherwise false.
 */
/**
* Mai 2016
* defines from config.php 
* Arnold Schiller
*
*/
function send_mail($email, $realname, $token)
{
	$realname = decode_entities($realname);
  // Construct the target script path for confirmation first.
	$parts = preg_split("/\//i", $_SERVER["PHP_SELF"], -1, PREG_SPLIT_NO_EMPTY);
	$count = count($parts);
	$parts[$count - 1] = "confirm.php";
  $targetScript = implode("/", $parts);

	$amessage = "Hallo " . $realname . "," . "\n\n";
	$amessage .= "Zum Abschluss der Anmeldung klicken Sie bitte auf den folgenden Link:\n ";
	$amessage .= "https://" .  $_SERVER['SERVER_NAME'] . "/" . $targetScript . "?token=" . $token . "\n\n";
	$amessage .= "Denken Sie bitte daran, dass Sie mit dem Aufruf dieses Links unsere Nutzungsbestimmungen (https://".DOMAIN_CURRENT_SITE."/Nutzungsbedingungen) anerkennen.";
	$amessage .= "\n\nDiese Mail wurde von automatisch generiert. Bei falscher Zustellung oder Irrtum bitte wir Sie ";
	$amessage .= "eine Mail an ".SUPPORTMAIL." zu senden.\n\n";
	$gesendet = mail($email, SITE." Anmeldung", $amessage, "From: ".WEBMASTERMAIL."\r\n" .
	  "Reply-To:".SUPPORTMAIL."\r\n" . "X-Mailer: PHP/" . phpversion());
	if($gesendet === TRUE){
		return true;
	}
	return false;
}

function send_confirm_passwort($email, $realname, $token)
{
	$realname = decode_entities($realname);
  // Construct the target script path for confirmation first.
	$parts = preg_split("/\//i", $_SERVER["PHP_SELF"], -1, PREG_SPLIT_NO_EMPTY);
	$count = count($parts);
	$parts[$count - 1] = "confirmpasswort.php";
  $targetScript = implode("/", $parts);

	$amessage = "Hallo " . $realname . "," . "\n\n";
	$amessage .= "Zum Abschluss der Passwortänderung klicken Sie bitte auf den folgenden Link:\n ";
	$amessage .= "https://" .  $_SERVER['SERVER_NAME'] . "/" . $targetScript . "?token=" . $token . "\n\n";
	$amessage .= "Denken Sie bitte daran, dass Sie mit dem Aufruf dieses Links unsere Nutzungsbestimmungen (https://".DOMAIN_CURRENT_SITE."/Nutzungsbedingungen) anerkennen.";
	$amessage .= "\n\nDiese Mail wurde automatisch generiert. Bei falscher Zustellung oder Irrtum bitte wir Sie ";
	$amessage .= "eine Mail an abuse@open-news-network.org zu senden.\n\n";
	$gesendet = mail($email,  SITE." Anmeldung", $amessage, "From: ".WEBMASTERMAIL."\r\n" .
	  "Reply-To:".SUPPORTMAIL."\r\n" . "X-Mailer: PHP/" . phpversion());
	if($gesendet === TRUE){
		return true;
}
	return false;
}



/**
 * Deletes all entries form the token table, which are older than $tokenTimeout.
 */
function eraseExpiredEntries()
{
  global $tokenTimeout, $dbconn;

  $time = time();
	$sql = "DELETE from tokens where `when` + $tokenTimeout < $time";

	try {
		$dbconn->query($sql);
	}  catch(PDOException $ex) {
                echo "eraseExpire fehlgeschlagen";
        }

	
}

/**
 * Decode htmlentities string to string example: &auml; to ä
 *
 * @param string $str The string to analyze.
 * @return string
 *    htmlentities decode
 */

function decode_entities($zeichen) 
{ 
   $tabelle = get_html_translation_table (HTML_ENTITIES); 
   $tabelle = array_flip($tabelle); 
   return strtr($zeichen, $tabelle); 
}  
?>
