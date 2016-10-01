<?php
$_SERVER['HTTP_HOST'] = "bgeserver.de";
include("config.php");
require_once("functions.php");
include("ldap.php");
$ldapserver = LDAP_SERVER;
$ldapport = LDAP_PORT;
$admindn = LDAP_ADMIN;
$adminsecret = LDAP_PASS;
$basedn = LDAP_BASEDN;



$name = "Arnold Pirat";
$email = "handy@babsi.de";

$result = mk_ldap_account($name, $email);
echo $result;
/*



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
		print "bisher alles gut";
		$dn = "uid=" . $mail . ",$basedn";
	        $info["userPassword"] = $passwort;
        	$info["description"] = $description;

                $check = "uid=". $mail;
                $sr = ldap_search($ds, $basedn, $check);
                $count = ldap_count_entries($ds,$sr);
		print $count;
		print "und";
		if($count === 1) {
                                $r = ldap_mod_replace($ds, $dn, $info);
				print "jetzt wollen wir die Mail versenden";
		}
        }
*/
# foreach(array_keys($adduserAD) as $key) {dmindn = LDAP_ADMIN;
# 	print $key.": "; 
#	print $adduserAD[$key][0]."\r\n";
#	if($key === "objectclass"){
#	print $adduserAD[$key][1]."\r\n";
#	print $adduserAD[$key][2]."\r\n";
#	print $adduserAD[$key][3]."\r\n";
#	}	
#}
// print_r ($adduserAD);
// echo $initials;
// echo $userprincipalname;
ldap_close($ds);
?>
