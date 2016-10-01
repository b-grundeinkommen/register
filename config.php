<?php
define('DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST']);
define('WEBMASTER_CURRENT_SITE', 'webmaster@'.DOMAIN_CURRENT_SITE);
define('SUPPORT_CURRENT_SITE', 'support@'.DOMAIN_CURRENT_SITE);
define('SITENAME', "BGE Server");
define('SITE', "bgeserver");
define('SUPPORTMAIL', "admin@bgeserver.de");
define('WEBMASTERMAIL', "webmaster@bgeserver.de");
define('ABUSEMAIL', 'abuse@'.DOMAIN_CURRENT_SITE);

define('DB_HOST',"localhost");
define('DB_USER',"register");
define('DB_PASS', "einblödeslangeskompliziertesPasswort");
define('MYSQL_DB',"register");

$supportMail = 'SUPPORTMAIL';
$tokenTimeout = 172800; // 48h

define('LDAP_ADMIN',"cn=registernutzer,dc=bgeserver,dc=de"); // cn=manager,dc=example,dc=com
define('LDAP_PASS',"nochsoeinblödeslangespasswort");		// top secret
define('LDAP_SERVER',"ldap1.bgeserver.de");	// ldap1.example.com
define('LDAP_PORT', "389");
define('LDAP_BASEDN', "ou=accounts,dc=bgeserver,dc=de");    // dc=example,dc=com


?>
