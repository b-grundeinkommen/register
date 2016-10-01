<?php
$_SERVER['HTTP_HOST'] = "bgeserver.de";
define('WAS_REQUIRED', true);
$_POST['email'] = "handy@babsi.de";
$_POST['realname'] = "Arnold Schiller";
$_GET['token'] =  "d7b3aeb935e833dee45a89ae920492f5";

include("confirmpasswort.php");

?>


