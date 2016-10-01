<?php
$_SERVER['HTTP_HOST'] = "bgeserver.de";
define('WAS_REQUIRED', true);

require("config.php");
require_once("ldap.php");

$email = "schiller@babsi.de";

$result = ldap_account_exists($email);

if($result === 0){
	echo "Account existiert nicht";
}else{
	echo "Account existiert $result Mal";
}

$passwort = passwortgen(8);

echo $passwort;

?>


