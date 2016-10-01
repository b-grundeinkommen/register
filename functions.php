<?php

/**
 * Some useful functions copied from phpLDAPadmin.
 *
 * @author The phpLDAPadmin
 *
 **/

/**
 * Hashes a password and returns the hash based on the specified enc_type.
 *
 * @param string $password_clear The password to hash in clear text.
 * @param string $enc_type Standard LDAP encryption type which must be one of
 *        crypt, ext_des, md5crypt, blowfish, md5, sha, smd5, ssha, or clear.
 * @return string The hashed password.
 */
/* no longer needed
if (!function_exists('password_hash'))
{

function password_hash( $password_clear, $enc_type ) {
	global $lang;

	$enc_type = strtolower( $enc_type );

	switch( $enc_type ) {
	case 'crypt':
			$new_value = '{CRYPT}' . crypt( $password_clear, random_salt(2) );
			break;

	case 'ext_des':
			// extended des crypt. see OpenBSD crypt man page.
			if ( ! defined( 'CRYPT_EXT_DES' ) || CRYPT_EXT_DES == 0 )
				pla_error( $lang['install_not_support_ext_des'] );

			$new_value = '{CRYPT}' . crypt( $password_clear, '_' . random_salt(8) );
			break;

	case 'md5crypt':
			if( ! defined( 'CRYPT_MD5' ) || CRYPT_MD5 == 0 )
				pla_error( $lang['install_not_support_md5crypt'] );

			$new_value = '{CRYPT}' . crypt( $password_clear , '$1$' . random_salt(9) );
			break;

	case 'blowfish':
			if( ! defined( 'CRYPT_BLOWFISH' ) || CRYPT_BLOWFISH == 0 )
				pla_error( $lang['install_not_support_blowfish'] );

			// hardcoded to second blowfish version and set number of rounds
			$new_value = '{CRYPT}' . crypt( $password_clear , '$2a$12$' . random_salt(13) );
			break;

	case 'md5':
			$new_value = '{MD5}' . base64_encode( pack( 'H*' , md5( $password_clear) ) );
			break;

	case 'sha':
			if( function_exists('sha1') ) {
				// use php 4.3.0+ sha1 function, if it is available.
				$new_value = '{SHA}' . base64_encode( pack( 'H*' , sha1( $password_clear) ) );

} elseif( function_exists( 'mhash' ) ) {
				$new_value = '{SHA}' . base64_encode( mhash( MHASH_SHA1, $password_clear) );

} else {
				pla_error( $lang['install_no_mash'] );
}
			break;

	case 'ssha':
			if( function_exists( 'mhash' ) && function_exists( 'mhash_keygen_s2k' ) ) {
				mt_srand( (double) microtime() * 1000000 );
				$salt = mhash_keygen_s2k( MHASH_SHA1, $password_clear, substr( pack( "h*", md5( mt_rand() ) ), 0, 8 ), 4 );
				$new_value = "{SSHA}".base64_encode( mhash( MHASH_SHA1, $password_clear.$salt ).$salt );

} else {
				pla_error( $lang['install_no_mash'] );
}
			break;

	case 'smd5':
			if( function_exists( 'mhash' ) && function_exists( 'mhash_keygen_s2k' ) ) {
				mt_srand( (double) microtime() * 1000000 );
				$salt = mhash_keygen_s2k( MHASH_MD5, $password_clear, substr( pack( "h*", md5( mt_rand() ) ), 0, 8 ), 4 );
				$new_value = "{SMD5}".base64_encode( mhash( MHASH_MD5, $password_clear.$salt ).$salt );

} else {
				pla_error( $lang['install_no_mash'] );
}
			break;

	case 'clear':
	default:
			$new_value = $password_clear;
}

	return $new_value;
}

}
*/
/**
 * Given a clear-text password and a hash, this function determines if the clear-text password
 * is the password that was used to generate the hash. This is handy to verify a user's password
 * when all that is given is the hash and a "guess".
 * @param String $hash The hash.
 * @param String $clear The password in clear text to test.
 * @return Boolean True if the clear password matches the hash, and false otherwise.
 */
if (!function_exists('password_check'))
{


function password_check( $cryptedpassword, $plainpassword ) {

	//echo "password_check( $cryptedpassword, $plainpassword )\n";
	if( preg_match( "/{([^}]+)}(.*)/", $cryptedpassword, $cypher ) ) {
		$cryptedpassword = $cypher[2];
		$_cypher = strtolower($cypher[1]);

} else  {
		$_cypher = NULL;
}

	switch( $_cypher ) {
		// SSHA crypted passwords
	case 'ssha':
			// check php mhash support before using it
			if( function_exists( 'mhash' ) ) {
				$hash = base64_decode($cryptedpassword);
				$salt = substr($hash, -4);
				$new_hash = base64_encode( mhash( MHASH_SHA1, $plainpassword.$salt).$salt );

				if( strcmp( $cryptedpassword, $new_hash ) == 0 )
					return true;
				else
					return false;

} else {
				pla_error( $lang['install_no_mash'] );
}
			break;

		// Salted MD5
	case 'smd5':
			// check php mhash support before using it
			if( function_exists( 'mhash' ) ) {
				$hash = base64_decode($cryptedpassword);
				$salt = substr($hash, -4);
				$new_hash = base64_encode( mhash( MHASH_MD5, $plainpassword.$salt).$salt );

				if( strcmp( $cryptedpassword, $new_hash ) == 0)
					return true;
				else
					return false;

} else {
				pla_error( $lang['install_no_mash'] );
}
			break;

		// SHA crypted passwords
	case 'sha':
			if( strcasecmp( password_hash($plainpassword,'sha' ), "{SHA}".$cryptedpassword ) == 0 )
				return true;
			else
				return false;
			break;

		// MD5 crypted passwords
	case 'md5':
			if( strcasecmp( password_hash( $plainpassword,'md5' ), "{MD5}".$cryptedpassword ) == 0 )
				return true;
			else
				return false;
			break;

		// Crypt passwords
	case 'crypt':
			// Check if it's blowfish crypt
			if( preg_match("/^\\$2+/",$cryptedpassword ) ) {

				// make sure that web server supports blowfish crypt
				if( ! defined( 'CRYPT_BLOWFISH' ) || CRYPT_BLOWFISH == 0 )
					pla_error( $lang['install_not_support_blowfish'] );

				list(,$version,$rounds,$salt_hash) = explode('$',$cryptedpassword);

				if( crypt( $plainpassword, '$'. $version . '$' . $rounds . '$' .$salt_hash ) == $cryptedpassword )
					return true;
				else
					return false;
}

			// Check if it's an crypted md5
			elseif( strstr( $cryptedpassword, '$1$' ) ) {

				// make sure that web server supports md5 crypt
				if( ! defined( 'CRYPT_MD5' ) || CRYPT_MD5 == 0 )
					pla_error( $lang['install_not_support_md5crypt'] );

				list(,$type,$salt,$hash) = explode('$',$cryptedpassword);

				if( crypt( $plainpassword, '$1$' .$salt ) == $cryptedpassword )
					return true;
				else
					return false;
}

			// Check if it's extended des crypt
			elseif (strstr( $cryptedpassword, '_' ) ) {

				// make sure that web server supports ext_des
				if ( ! defined( 'CRYPT_EXT_DES' ) || CRYPT_EXT_DES == 0 )
					pla_error( $lang['install_not_support_ext_des'] );

				if( crypt($plainpassword, $cryptedpassword ) == $cryptedpassword )
					return true;
				else
					return false;
}

			// Password is plain crypt
			else {

				if( crypt($plainpassword, $cryptedpassword ) == $cryptedpassword )
					return true;
				else
					return false;
}
			break;

		// No crypt is given assume plaintext passwords are used
	default:
			if( $plainpassword == $cryptedpassword )
				return true;
			else
				return false;

			break;
	}
	}
}

if (!function_exists('get_enc_type'))
{


function get_enc_type( $user_password ) {
	/* Capture the stuff in the { } to determine if this is crypt, md5, etc. */
	$enc_type = null;

	if( preg_match( "/{([^}]+)}/", $user_password, $enc_type) )
		$enc_type = strtolower( $enc_type[1] );
	else
		return null;

	/* handle crypt types */
	if( strcasecmp( $enc_type, 'crypt') == 0 ) {

		if( preg_match( "/{[^}]+}\\$1\\$+/", $user_password) ) {
			$enc_type = "md5crypt";

		} elseif ( preg_match( "/{[^}]+}\\$2+/", $user_password) ) {
			$enc_type = "blowfish";

		} elseif ( preg_match( "/{[^}]+}_+/", $user_password) ) {
			$enc_type = "ext_des";
		}

		/*
 * No need to check for standard crypt, 
 * because enc_type is already equal to 'crypt'.
		 */
		}
	return $enc_type;
}

}




/**
 * Used to generate a random salt for crypt-style passwords. Salt strings are used
 * to make pre-built hash cracking dictionaries difficult to use as the hash algorithm uses
 * not only the user's password but also a randomly generated string. The string is
 * stored as the first N characters of the hash for reference of hashing algorithms later.
 *
 * --- added 20021125 by bayu irawan <bayuir@divnet.telkom.co.id> ---
 * --- ammended 20030625 by S C Rigler <srigler@houston.rr.com> ---
 *
 * @param int $length The length of the salt string to generate.
 * @return string The generated salt string.
 */
if (!function_exists('random_salt'))
{

function random_salt( $length )
{
	$possible = '0123456789'.
		'abcdefghijklmnopqrstuvwxyz'.
		'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
		'./';
	$str = "";
	mt_srand((double)microtime() * 1000000);

        while( strlen( $str ) < $length )
                $str .= substr( $possible, ( rand() % strlen( $possible ) ), 1 );

	/**
	 * Commented out following line because of problem
	 * with crypt function in update.php
	 * --- 20030625 by S C Rigler <srigler@houston.rr.com> ---
	*/
	//$str = "\$1\$".$str."\$";
	return $str;
}
}
/**
 * Get whether a string looks like an email address (user@example.com).
 *
 * @param string $str The string to analyze.
 * @return bool Returns true if the specified string looks like
 *   an email address or false otherwise.
 */
if (!function_exists('is_mail_string'))
{



function is_mail_string( $str )
{
    $mail_regex = "/^[_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*$/";
    if( preg_match( $mail_regex, $str ) )
        return true;
    else
        return false;
}

}

if (!function_exists('decode_entities')){
/**
 * Decode htmlentities string to string example: &auml; to Ã¤
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
}


if (!function_exists('passwortgen')){
/**
 * Generates a password.
 *
 * @param $laenge The length of the password.
 * @return The new password.
 */


function passwortgen($laenge)
{
	if (!$laenge)
	{
		$laenge = 10;
	};
	if ($laenge < 3)
	{
		$laenge = 5;
	};

//Zeichen die für das Passwort verwendet werden
$zeichen=array(" ","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","!","%","&","1","2","3","4","5","6","7","8","9","0");
$gross=array(" ","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
$klein=array(" ","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
$sonder = array (" ","!","%","&","(",")","@");
$zahlen = array (" ","1","2","3","4","5","6","7","8","9","0");
mt_srand ((double)microtime()*1000000);
$max = count($zeichen);

for ($i=0; $i < $laenge; $i++) {
$zahl = mt_rand(1,($max-1)); 
$passwort[$i] = "$zeichen[$zahl]";
}

$grosskleinzahl = mt_rand(1,3);
//Ein gutes Passwort hat kein Sonderzeichen am Anfang

if($grosskleinzahl === 1){
$max = count($gross);
$auswahlgross = mt_rand(1,($max-1));
$passwort[0] =  $gross["$auswahlgross"];

}
if($grosskleinzahl === 2){
$max = count($klein);
$auswahlklein = mt_rand(1,($max-1));	
$passwort[0] =  $klein["$auswahlklein"];

}

if($grosskleinzahl === 3){
$max = count($zahlen);
$auswahlzahl = mt_rand(1,($max-1));	
$passwort[0] =  $zahlen["$auswahlzahl"];
}



$grosskleinzahl = mt_rand(1,3);

//Ein gutes Passwort hat kein Sonderzeichen am Anfang
if($grosskleinzahl === 1){
$max = count($gross);
$auswahlgross = mt_rand(1,($max-1));	
$passwort[$laenge] =  $gross["$auswahlgross"];
}
if($grosskleinzahl === 2){
$max = count($klein);
$auswahlklein = mt_rand(1,($max-1));	
$passwort[$laenge] =  $klein["$auswahlklein"];
}

if($grosskleinzahl === 3){
$max = count($zahlen);
$auswahlzahl = mt_rand(1,($max-1));	
$passwort[$laenge] =  $zahlen["$auswahlzahl"];
}
//mindestens ein Sonderzeichen aber nicht am Ende und nicht am Anfang
$sonderzeichenersetzung = mt_rand(2,($laenge-1));
$max = count($sonder);
$sonderzeichenzahl = mt_rand(1,($max-1));
//echo $sonderzeichenzahl;
$passwort[$sonderzeichenersetzung] = "$sonder[$sonderzeichenzahl]";
// und das letzte Zeichen sollte auch kein Sonderzeichen sein
//$passwortzeichen = $passwort[0];
// das Ganze in eine Stringvariable
$passwortzeichen="";
for ($i=0; $i < $laenge; $i++) {
$passwortzeichen .= $passwort[$i];

}

return $passwortzeichen;

}
}
