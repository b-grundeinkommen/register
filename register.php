<?php

// (c) 2005 Michael Schierl
// (c) 2005 Mike Lischke
// (c) 2006 Arnold Schiller
// (c) 2016 Arnold Schiller
// #include <gpl.txt>
$title = "BGEServer Registrierung";
error_reporting(E_ALL);

session_start();
$sessionName = 'register';


// Hack protection.
define('WAS_REQUIRED', true);
require("include.php");
require('./captcha/captcha.php');
if (isset($_POST['email']))
	$email = htmlentities(stripslashes($_POST['email']));
else
	$email = "E-Mail-Adresse";
if (isset($_POST['realname']))
	$realname = htmlentities(stripslashes($_POST['realname']));
else
	$realname = "Name";
if (isset($_POST['essential']))
	$accepted = "checked";
else
	$accepted = "";
if(isset($_POST['captchaid'])) 
	$captchaid = htmlentities(stripslashes($_POST['captchaid']));
else
	$captchaid = "Code";

$domain_current_site = DOMAIN_CURRENT_SITE;
$messageColor = "red";
$finished = false;

if (isset($_POST['email']) && isset($_POST['realname']))
{  // If there was no error and mail and real name are set then check them for validity.
	$message = checkdata($email, $realname);
}else
{
	$message = "Bitte füllen Sie die Felder aus.";
	$messageColor = "black";
};

// Maintenance: erase outdated entries.
eraseExpiredEntries();

// Has the user accepted our conditions?
// if ($message == "" && !$accepted)
//  $message .= "Sie müssen die Nutzungsbedingungen akzeptieren.";

if($_SESSION[$sessionName] == trim($_POST['captchaid'])) // Stimmt die Eingabe mit dem Code überein
	$message = checkdata($email, $realname);
else
        $message = "Bitte füllen Sie alle Felder aus! (Code?)";

// Has the user accepted our conditions?
if ($message == "" && !$accepted)
   $message = "Sie müssen die Nutzungsbedingungen akzeptieren.";

 
if ($message == "validated")
{
	$finished = true;
	$message = "Anmeldung erfolgreich abgeschlossen";
	$messageColor = "green";
};

if ($finished)
{
	$token = gen_save_token($email, $realname);

	if ($howoften >= 10)
	{
		$message = "Registrierungslimit erreicht";
		$messageColor = "red";
	};
};

$self = $_SERVER["PHP_SELF"] . "?session_code=$captchaid";
include("headhtml.inc.php");
if (!$finished)
{
  // Create new captcha image.
   //	$pictureUrl = $newCaptcha->get_pic(6);
	$captcha = $_SERVER["PHP_SELF"] . "?captcha=$captcha";
 	echo <<<FORM
	<form name="registration" method="post" action="$self">
		<table width="100%" border="0" style="font-size: 13px" cellspacing="10">
			<tr>
	 	  	<td rowspan="2" width="50%">
	 	  		 
        				<img src="$captcha" alt="Captcha" />

	
	 	  	</td>
				<td>
					<b>Welcher Code wird im Bild dargestellt?</b>
				</td>
	 	  </tr>
	 	  <tr>
	  		<td>
	  				<input type="text" name="captchaid" id="captchaid" value="$captchaid"/>	
	  		</td>
	  	</tr>
	  	<tr>
	  		<td>
	  			<b>E-Mail-Adresse:</b><br />
	  			<input type="text" name="email" value="$email">
	  		</td>
	  		<td>
	  			<b>Realname:</b><br />
	  			<input type="text" name="realname" value="$realname">
	  		</td>
	  	<tr>
	  		<td colspan="2" style="font-size:90%">
	  			<input type=checkbox name="essential" value="true" $accepted> &nbsp;<b>Ich habe die <a href="http://$domain_current_site/Nutzungsbedingungen">Nutzungsbedingungen</a> gelesen und stimme diesen mit dem Speichern meiner Daten zu.</b>
	  		</td>
	  	</tr>
	  	<tr>
	  		<td colspan="2" align="right">
					<input type="submit" name="check" value="Anmelden">
				</td>
			</tr>
		</table>
	</form>
FORM;
}
else
{
  //print $message;
  if ($howoften >= 10)
  {
		echo "Sie haben sich nun schon mehr als 10 mal registrieren lassen.<br />" .
		"Es werden deshalb vorerst keine weiteren E-Mails versandt. <br />" .
		"Sollte Sie mit der Registrierung Probleme haben, dann kontaktieren Sie bitte <a href=\"mailto:$supportMail\">den $SITENAME Support</a>.";
  }
  else
  {
	 	$success = send_mail($email, $realname, $token);
		if ($success)
		{
		  echo "Eine Bestätigungs E-Mail wurde an die Adresse " . $email . " gesendet.<br />";
		}
		else
		{
			echo "Die Bestätigungs E-Mail konnte nicht gesendet werden. Bitte kontaktieren Sie <a href=\"mailto:$supportMail\">den $SITENAME Support</a><br />";
		};

		$captcha = $_SERVER["PHP_SELF"] . "?captcha=$captcha";		
 		echo <<<FORM
			<form name="registration" method="post" action="$self">
			<input type="hidden" name="captcha" value="$captchaid">
			<input type="hidden" name="email" value="$email">
			<input type="hidden" name="realname" value="$realname">
			<input type="submit" value="Bestätigungsmail erneut anfordern">
FORM;

};
?>
<?php
}

include("foothtml.inc.php");
?>

