<?php
// (c) 2005 Michael Schierl
// (c) 2005 Mike Lischke
//
// #include <gpl.txt>
$title = "Neues Passwort";
error_reporting(E_ALL);

// Hack protection.
define('WAS_REQUIRED', true);
require("include.php");

if (isset($_POST['email']))
	$email = htmlentities(stripslashes($_POST['email']));
else
	$email = "E-Mail-Adresse";
if (isset($_POST['realname']))
	$realname = htmlentities(stripslashes($_POST['realname']));
else
	$realname = "Name";
if (isset($_POST['ccode']))
	$ccode = $_POST['ccode'];
else
	$ccode = "";

$messageColor = "red";
$finished = false;

if (isset($_POST['email']) && isset($_POST['realname']))
  // If there was no error and mail and real name are set then check them for validity.
	$message = checkdata($email, $realname);
else
{
	$message = "Bitte füllen Sie die Felder aus. <br> Die bei der Anmeldung angegebene Mailadresse und den bei der Anmeldung angegebenen Realnamen.";
	$messageColor = "black";
};

// Maintenance: erase outdated entries.
eraseExpiredEntries();

require_once('captcha/class.captcha.php');

if (empty($_GET['session_code']))
  $session_code = md5(round(rand(0, 40000)));
else
	$session_code = $_GET['session_code'];

$newCaptcha = new captcha($session_code, 'captcha/__TEMP__', 'captcha');

// Check that the captcha code is valid.
if ($message == "" && !$newCaptcha->verify($ccode))
  $message = "Der Code ist falsch oder fehlt völlig.";

// Has the user accepted our conditions?

if ($message == "Account existiert bereits 1 Mal")
{
	$finished = true;
	$message = "Sie erhalten eine Mail, nach Klicken des Links wird ein neues Passwort vergeben";
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
include("headhtml.inc.php");
$self = $_SERVER["PHP_SELF"] . "?session_code=$session_code";
if (!$finished){
  // Create new captcha image.
	$pictureUrl = $newCaptcha->get_pic(6);
 	echo <<<FORM
 	<form name="newpasswort" method="post" action="$self">
		<table width="100%" border="0" style="font-size: 13px" cellspacing="10">
			<tr>
	 	  	<td rowspan="2" width="50%">
	 	  		<img src="captcha/captcha_image.php?img=$pictureUrl">
	 	  	</td>
				<td>
					<b>Welcher Code wird im Bild dargestellt?</b>
				</td>
	 	  </tr>
	 	  <tr>
	  		<td>
	  			<input type="text" name="ccode" value="$ccode">
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
	  		<td colspan="2" align="right">
					<input type="submit" value="Anfordern">
				</td>
			</tr>
		</table>
	</form>


FORM;
}
else
{
  if ($howoften >= 10)
{
		echo "Sie haben sich nun schon mehr als 10 mal Passwort &auml;ndern lassen.<br />" .
		"Es werden deshalb vorerst keine weiteren E-Mails versandt. <br />" .
		"Sollte Sie mit der &Auml;nderung Probleme haben, dann kontaktieren Sie bitte <a href=\"mailto:$supportMail\">den SITENAME Support</a>.";
}
  else
{
	 	$success = send_confirm_passwort($email, $realname, $token);
		if ($success)
{
		  echo "Eine Bestätigungs E-Mail wurde an die Adresse " . $email . " gesendet.<br />";
}
		else
{
			echo "Die Bestätigungs E-Mail konnte nicht gesendet werden. Bitte kontaktieren Sie <a href=\"mailto:$supportMail\">den SITENAME Support</a><br />";
};

		/*
 		echo <<<FORM
			<form name="registration" method="post" action="$self">
			<input type="hidden" name="ccode" value="$ccode">
			<input type="hidden" name="email" value="$email">
			<input type="hidden" name="realname" value="$realname">
			<input type="submit" value="Bestätigungsmail erneut anfordern">
FORM;
		 */
		$newCaptcha->removeImage();
};
?>
<?php
}


include("foothtml.inc.php");
?>
