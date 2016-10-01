<?php
$_SERVER['HTTP_HOST'] = "bgeserver.de";
define('WAS_REQUIRED', true);
$_POST['email'] = "schiller@babsi.de";
$_POST['realname'] = "Arnold Schiller";
$_GET['token'] =  "028a66570e41e578823ceabb237c15f0";

# include("confirm.php");
require("ldap.php");
require("functions.php");
$enc_type = "md5crypt";
$passwort = passwortgen(10);
echo $passwort;
echo "\r\n";
$passwort = password_hash($passwort, CRYPT_MD5 );
echo $passwort;
?>


