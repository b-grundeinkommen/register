<?php

// (c) 2005 Michael Schierl
// (c) 2005 Mike Lischke
// (c) 2006 Arnold Schiller
// #include <gpl.txt>
define('HEADERINDEX', 3);
$title = "SITENAME Anmeldung";
$pageTitle = "Anmeldung";

error_reporting(E_ALL);

// Hack protection.
define('WAS_REQUIRED', true);
require("include.php");

if (isset($_POST['email']))
	$email = htmlentities(stripslashes($_POST['email']));
else
	$email = "'email'";
if (isset($_POST['realname']))
	$realname = htmlentities(stripslashes($_POST['realname']));
else
	$realname = "'realname'";
// if (isset($_POST['ccode']))
//	$ccode = $_POST['ccode'];
//else
//	$ccode = "";
if (isset($_POST['essential']))
	$accepted = "checked";
else
	$accepted = "";
if (isset($_POST['vorname']))
	$vorname = htmlentities(stripslashes($_POST['vorname']));
else 
	$vorname =  'vorname'; 	
if (isset($_POST['comments']))
	$comments = htmlentities(stripslashes($_POST['comments']));
else
	$comments = 'comments'; 

$messageColor = "red";
$finished = false;

$name = $vorname." ".$realname;

if (isset($_POST['email']) && isset($_POST['realname']))
  // If there was no error and mail and real name are set then check them for validity.
	$message = checkdata($email, $name);
else
{
	$message = "Bitte füllen Sie die Felder aus.";
	$messageColor = "black";
};

// Maintenance: erase outdated entries.
eraseExpiredEntries();

// require_once('captcha/class.captcha.php');

if (empty($_GET['session_code']))
  $session_code = md5(round(rand(0, 40000)));
else
	$session_code = $_GET['session_code'];

// $newCaptcha = new captcha($session_code, '/home/htdocs/web21/phptmp', 'captcha');

// Check that the captcha code is valid.
// if ($message == "" && !$newCaptcha->verify($ccode))
//  $message = "Der Code ist falsch oder fehlt völlig.";

// Has the user accepted our conditions?
if ($message == "" && !$accepted)
  $message = "Sie müssen die Nutzungsbedingungen akzeptieren.";

if ($message == "")
{
	$finished = true;
	$message = "Anmeldeantrag erfolgreich eingereicht ";
	$messageColor = "green";
};

if ($finished)
{
	$name = "$vorname $realname";
	$token = gen_save_token($email, $name);

	if ($howoften >= 10)
	{
		$message = "Registrierungslimit erreicht";
		$messageColor = "red";
	};
};

include("headhtml.inc.php");
$self = $_SERVER["PHP_SELF"] . "?session_code=$session_code";
if (!$finished)
{


?>
<FORM METHOD="post" ACTION="<?php echo $self?>">
<P>
<!-- Ausfuellen des E-Mails -->
<!-- CGI-TO ist die Mail-Adresse an die das Form-Ergebins gesandt werden soll -->
<INPUT TYPE="HIDDEN" NAME="CGI-TO" VALUE="abuse@open-news-network.org">
<INPUT TYPE="HIDDEN" NAME="CGI-SUBJECT" VALUE="Kontaktformular">
<input type="hidden" name="email" value="<?php echo $email?>">
<input type="hidden" name="vorname" value="<?php echo $vorname?>">
<input type="hidden" name="realname" value="<?php echo $realname?>">
<input type="hidden" name="comments" value="<?php echo $comments?>">



<p> Wer uns unterst&uuml;tzen m&ouml;chte, werfe einen Blick auf:
<li> <a href="/wiki/Responsibles">Responsibles</a> </p>
<TABLE WIDTH="600" BORDER="0" CELLPADDING="0" CELLSPACING="0">

<TR><TD colspan="2">
Newsserver Anmeldung<br>
Bitte tragen Sie hier Ihre Kontaktdaten ein:

<BR><BR>

</TD></TR>

<TR><TD>
</TD>
<TD>
        <input type=checkbox name="essential" value="true" > &nbsp;<b>Ich habe die <a href="https://"<?php echo DOMAIN_CURRENT_SITE ?>"/"<?php echo $Nutzungsbedingungen ?>">Nutzungsbedingungen</a> gelesen und stimme diesen mit dem Speichern meiner Daten zu.</b>
I  have read the <a href="https://<?php DOMAIN_CURRENT_SITE ?>/TermsOfUse">conditions</a> and agree with them.

<BR><BR>
</TD>
</TR>

<TR><TD>
Anrede:
</TD>
<TD>
<INPUT TYPE="RADIO" NAME="Anrede" VALUE="Herr"> Herr
<INPUT TYPE="RADIO" NAME="Anrede" VALUE="Frau"> Frau
<BR><BR>
</TD>
</TR>

<TR><TD>
Vorname:
</TD>
<TD>
<INPUT TYPE="TEXT" NAME="vorname" SIZE="30" value="<?php echo $vorname?>">
<BR>
</TD>
</TR>

<TR><TD>
Name:
</TD>
<TD>
<INPUT TYPE="TEXT" NAME="realname" SIZE="30" value="<?php echo $realname?>">
<BR>
</TD>
</TR>




<TR><TD>
E-Mail:
</TD>
<TD>
<INPUT TYPE="TEXT" NAME="email" SIZE="30" value="<?php echo $email?>">
<BR>
</TD>
</TR>



<tr>
<td colspan="2">
Bitte begr&uuml;nde kurz in 2 S&auml;tzen deine Beweggr&uuml;nde:
</td>
</tr>
<tr>
<td colspan="2">

<BR> <BR>

<TEXTAREA NAME="comments" COLS="40" ROWS="6" value="<?php echo $comments?>"><?php echo $comments?></TEXTAREA>

<BR><BR><BR>
</td>
</tr>
<tr>
<td>
<INPUT TYPE="SUBMIT" NAME="Abschicken" VALUE="Abschicken"></td><td> <INPUT TYPE="RESET" VALUE="Formular l&ouml;schen !">
</td>
</tr>


</table>
</FORM>


<?
  // Create new captcha image.
	// $pictureUrl = $newCaptcha->get_pic(6);
/**
 	echo <<<FORM
	<form name="registration" method="post" action="$self">
		<table width="100%" border="0" style="font-size: 13px" cellspacing="10">
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
	  			<input type=checkbox name="essential" value="true" $accepted> &nbsp;<b>Ich habe die <a href="http://DOMAIN_CURRENT_SITE/wiki/Nutzungsbedingungen">Nutzungsbedingungen</a> gelesen und stimme diesen mit dem Speichern meiner Daten zu.</b>
	  		</td>
	  	</tr>
		<tr>
			 <td colspan="2" style="font-size:90%">
				<textarea name="comments" cols="40" rows="6" value="$comments "></textarea>
			</td>
		</tr>
	  	<tr>
	  		<td colspan="2" align="right">
					<input type="submit" value="Anmelden">
				</td>
			</tr>
		</table>
	</form>
FORM;
**/
}
else
{
  if ($howoften >= 10)
  {
		echo "Sie haben sich nun schon mehr als 10 mal registrieren lassen.<br />" .
		"Es werden deshalb vorerst keine weiteren E-Mails versandt. <br />" .
		"Sollte Sie mit der Registrierung Probleme haben, dann kontaktieren Sie bitte <a href=\"mailto:$supportMail\">den SITENAME Support</a>.";
  }
  else
  {
		$name = "$vorname $realname";		
	 	$success = send_mail_kontakt($email, $name, $token, $comments);
		if ($success)
		{
		  echo "Die Anmeldung   wurde an die Adresse $SUPPORTMAIL gesendet.<br />";
		  echo "Name:".$realname."\n";
		  echo "Email:".$email."\n"; 	
		  echo "Kommentar:".$comments."\n";
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
			<input type="hidden" name="comments" value="$comments">
			<input type="submit" value="Bestätigungsmail erneut anfordern">
// FORM;
		*/
		// $newCaptcha->removeImage();
	};
?>
<?php
}
include("foothtml.inc.php");
?>
