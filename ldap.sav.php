<?php
// (c) 2005 Michael Schierl
// (c) 2005 Arnold Schiller
// #include <gpl.txt>

if (!defined('WAS_REQUIRED') )
{
	header("HTTP/1.0 403 Forbidden");
	die();
}
require_once("config.php");
require_once("functions.php");
/**
 * Creates an account entry in the LDAP database for the given email address.
 *
 * @param $name A real name used to customize messages.
 * @param $email The email address for which the LDAP account is to be created.
 */
 /**
 * Mi Okt 12 08:15:41 2005
 * SERVERNAME  gegegen $WEBMASTERMAIL ausgetauscht
 * Reply-to auf $SUPPORTMAIL gesetzt.
 * Arnold Schiller
 */

if (!function_exists('mk_ldap_account'))
{

function mk_ldap_account($name, $email)
{
	global $ldapserver, $ldapport, $admindn, $adminsecret;
	$ldapserver = LDAP_SERVER;
	$ldapport = LDAP_PORT;
	$admindn = LDAP_ADMIN;
	$adminsecret = LDAP_PASS;
	$basedn = LDAP_BASEDN;
	$name = decode_entities($name);
        $name = utf8_encode($name);
	$errormsg="";

	$mail = $email;
	$passwort = passwortgen(10);

	$sn = str_replace(" ","", $name);
	$userprincipalname = str_replace(" ",".",$name)."@".DOMAIN_CURRENT_SITE;
	$mailsn = "SN: ".$sn;
	$mailname = "\nNAME: " . $name;
	$description = "register ".mktime();
	$sendmailpasswort = $passwort;
	$words = explode(" ",$name);
	$initials = "";
	foreach ($words as $w) {
  		$initials .= $w[0];
	}
        $unicodePwd = base64_encode(iconv("UTF-8","UTF16LE",$passwort));

	$ds = ldap_connect($ldapserver);
	// echo "connect result is " . $ds . "<br />";
	echo "Hallo $name!";
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	$r = ldap_bind($ds, $admindn, $adminsecret);

	if ($ds && $r)
	{
	  // Encrypt the password.
	  //	$enc_type = "crypt";
	  //http://www.openldap.org/faq/data/cache/347.html
   	   //$passwort = "{SHA}" . sha1( $passwort, TRUE ); 
  	   // Daten vorbereiten
	/*  
		Active Directory Style

	$adduserAD["cn"][0] = $name;
	$adduserAD["instancetype"][0] = 4;
	$adduserAD["userAccountControl"][0] = 512;
	$adduserAD["accountExpires"][0] = 0;
	$adduserAD["uidNumber"][0] = 65534;
	$adduserAD["gidNumber"][0] = 65534; 
	$adduserAD["sAMAccountName"][0] = strtolower($sn);
	$adduserAD["objectclass"][0] = "top";
	$adduserAD["objectclass"][1] = "person";
	$adduserAD["objectclass"][2] = "organizationalPerson";
	$adduserAD["objectclass"][3] = "user";
	$adduserAD["displayname"][0] = $name;
	$adduserAD["name"][0] = $name;
	$adduserAD["givenname"][0] = $name;
	$adduserAD["sn"][0] = $sn;
	$adduserAD["title"][0] = "register";
	$adduserAD["description"][0] = $description;
	$adduserAD["mail"][0] = $mail;
	$adduserAD["initials"][0] = $initials; 
	$adduserAD["samaccountname"][0] = $sn;
	$adduserAD["userprincipalname"][0] = $sn.'@'.DOMAIN_CURRENT_SITE;
	$adduserAD["manager"][0] = "uid=" . $mail . ",$basedn";
	$adduserAD["unicodePwd"][0] = $unicodePwd;
	/*
          X500 Style Standard  
	*/

  	$info["cn"] = $name;
	$info["givenName"] = $name;
	$info["objectclass"][0] = "top";
	$info["objectclass"][1] = "person";
	$info["objectclass"][2] = "organizationalPerson";
	$info["objectclass"][3] = "inetOrgPerson";
	$info["ou"] = "People";
	$info["sn"] = $sn;
	$info["uid"] = $mail;
	$info["mail"] = $mail;
	$info["userPassword"] = $passwort;
	$info["description"] = $description;
		  
		$dn = "uid=" . $mail . ",$basedn";
		
		// Add the data to the directory.
		$check = "uid=". $mail;
		$sr = ldap_search($ds, $basedn, $check);
		$count = ldap_count_entries($ds,$sr);

		// The user exists already if count is > 0.
		if ($count === 0)
		{
  		$r = ldap_add($ds, $dn, $info); //X500
		// $r = ldap_add($ds, $dn, $adduserAD); //Microsoft
      // Send a confirmation mail if the user could be added to the LDAP database.
			if ($r === TRUE)
			{
				echo "Herzlichen Glückwunsch. Ihr ".SITENAME." Zugang wurde erstellt.<br />";
				$message = "Hallo " . utf8_decode($name) . ",\n\n";
				$message .= "herzlich willkommen beim ".SITENAME."\n \n";
				$message .= "Sie sind mir Ihrem Namen (" . utf8_decode($name) . ") und folgenden Benutzerdaten freigeschaltet worden: \n";
				$message .= "Die Benutzeridentifikation (der Anmeldename) lautet: " . $mail . "\n";
				$message .= "Das Passwort ist: " . $sendmailpasswort . "\n\n";
				$message .= "Wenn Sie Fragen haben, dann schauen Sie in der Gruppe opennews.admin oder auf https://".DOMAIN_CURRENT_SITE."/ vorbei.\n";
				$message .= "Diese Mail wurde automatisch generiert. Bei falscher Zustellung oder Irrtum senden Sie bitte eine E-Mail an ".ABUSEMAIL."\n\n";
				$message .= "Das ".SITENAME." team";
				$gesendet = mail($mail,"".SITENAME." Zugang wurde eingerichtet", $message, "From: ".WEBMASTERMAIL."\r\n" . "Reply-To:".SUPPORTMAIL."\r\n" . "X-Mailer: PHP/" . phpversion());
				if ($gesendet === TRUE)
				{
					echo "Eine Willkommensnachricht mit Logindetails wurde an " . $mail . " gesandt.";      $errormsg = "";
					return $errormsg;
				}
				else
					echo "Allerdings gab es Probleme beim Versand der Logindetails. Bitte wenden Sie sich an <a href=\"mailto:".SUPPORTMAIL."\">den ".SITENAME." Support</a>";
			}
			else
			{
				// ldapadd failed.
				$errormsg = "Der Eintrag in die Datenbank ist leider fehlgeschlagen. Bitte laden Sie die Seite neu.";
			};
		}
		else
		{
		  // There is already an entry.
 			$errormsg =  "Eintrag unter der E-Mail Adresse " . $mail . " existiert schon.";
		};
	}
	else
	{
	  // bind failed.
		$errormsg =  "Es konnte keine Verbindung zum Anmeldeserver hergestellt werden. Bitter versuchen Sie es später wieder noch einmal";
	};

	ldap_close($ds);

	return $errormsg;
}
}

if (!function_exists('mod_ldap_passwort'))
{

function mod_ldap_passwort($name, $email)
{
	global $ldapserver, $ldapport, $admindn, $adminsecret;
        $ldapserver = LDAP_SERVER;
        $ldapport = LDAP_PORT;
        $admindn = LDAP_ADMIN;
        $adminsecret = LDAP_PASS;
	$basedn = LDAP_BASEDN;

	$name = decode_entities($name);
        $name = utf8_encode($name);
	$errormsg="Fehlgeschlagen";
	$description = "changepw ".time();
	$mail = $email;
	$passwort = passwortgen(10);

	$mailname = "\nNAME: " . $name;

	$sendmailpasswort = $passwort;

	$ds = ldap_connect($ldapserver, $ldapport);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	$r = ldap_bind($ds, $admindn, $adminsecret);
	
	if ($ds && $r){
	  // Encrypt the password.
	  //	$enc_type = "crypt";
   	//$passwort = password_hash($passwort, CRYPT_MD5);
	    //http://www.openldap.org/faq/data/cache/347.html
        // $passwort = "{SHA}" . sha1( $passwort, TRUE );

  	// Daten vorbereiten
  	$dn = "uid=" . $mail . ",$basedn";
  	$info["userPassword"] = $passwort;
  	$info["description"] = $description;

		$check = "uid=". $mail;
		$sr = ldap_search($ds, $basedn, $check);
		$count = ldap_count_entries($ds,$sr);

		// The user exists already if count is === 0.
		if ($count === 0){
		$errormsg = "Keinen Eintrag gefunden Passwort&auml;nderung kann nicht durchgef&uumlhrt werden";		
		
		}else{
			if($count === 1) {
				$r = ldap_mod_replace($ds, $dn, $info);
				if($r === true){
				echo "Herzlichen Glückwunsch. Ihr ".SITENAME." Passwort wurde ge&auml;ndert.<br />";
				$message = "Hallo " . utf8_decode($name) . ",\n\n";
				$message .= "herzlich willkommen beim ".SITENAME."\n \n";
				$message .= "Sie sind mir Ihrem Namen (" . utf8_decode($name) . ") und folgenden Benutzerdaten wieder freigeschaltet worden: \n";
				$message .= "Die Benutzeridentifikation (der Anmeldename) lautet: " . $mail . "\n";
				$message .= "Das Passwort ist: " . $sendmailpasswort . "\n\n";
				$gesendet = mail($mail," ".SITENAME." Passwort wurde geändert", $message, "From: ".WEBMASTERMAIL."\r\n" . "Reply-To:".SUPPORTMAIL."\r\n" . "X-Mailer: PHP/" . phpversion());
					if ($gesendet === TRUE){
					echo "Das neue Passwort  mit Logindetails wurde an " . $mail . " gesandt.";
					$errormsg = "";
					}else{
					echo "Allerdings gab es Probleme beim Versand der Logindetails. Bitte wenden Sie sich an <a href=\"mailto:".SUPPORTMAIL."\">den ".SITENAME." Support</a>";
					$errormsg = "$mail"."$dn";
					}
				}else{
				$errormsg = "Passwort&auml;nderung fehlgeschlagen";
				}
			}else{
				$errormsg = "Fehler! Mehrere Eintr&auml;ge: ".$count;
			}
		}	
	}
	ldap_close($ds);

	return $errormsg;
}

}

/**
 * Checks if the given mail address is already in use.
 *
 * @param $email The email address for which the LDAP account is checked.
 * @return True if the mail is already in use, otherwise false.
 */

if (!function_exists('ldap_account_exists'))
{

function ldap_account_exists($email)
{
	global $ldapserver, $ldapport, $admindn, $adminsecret;
        $ldapserver = LDAP_SERVER;
        $ldapport = LDAP_PORT;
        $admindn = LDAP_ADMIN;
        $adminsecret = LDAP_PASS;
	$basedn = LDAP_BASEDN;
        $result = false;

	$ds = ldap_connect($ldapserver);
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	$r = ldap_bind($ds, $admindn, $adminsecret);

	if ($ds && $r)
	{
		// Search directory.
		$sr = ldap_search($ds, "$basedn", "uid=". $email);
		$count = ldap_count_entries($ds, $sr);
		return $count;
		// The user exists already if count is > 0.
		// if ($count === 0)
		//  $result = false;
	}
	else
	{
	  // bind failed.
          //		return "Fehler Verbindung!";
		die("Es konnte keine Verbindung zum Anmeldeserver hergestellt werden. Bitter versuchen Sie es später wieder noch einmal");
	};

	ldap_close($ds);

	return $result;
}

}


?>
