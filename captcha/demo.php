<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Demo für Captcha-Funktion</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php
	require_once( 'class.captcha.php' );

	if (empty($_GET['session_code']))
		{ $session_code = md5(round(rand(0,40000))); }
	else
		{ $session_code=$_GET['session_code']; }

	$my_captcha = new captcha( $session_code, '__TEMP__/' );

	

	$do = $_GET['do'];

	if ($do == 'verify')
	{
		if ($my_captcha->verify( $_POST['password'] ) )
		{
			echo "You entered the correct password!";
			exit;
		}
	}

	$pic_url = $my_captcha->get_pic( 15 );

	echo <<<FORM
	<form name="form1" method="post" action="$PHP_SELF?do=verify&session_code=$session_code">
 	  <p><img src="captcha_image.php?img=$pic_url"></p>
  	  <p>Displayed Code? <input type="text" name="password"></p>
  	  <p><input type="submit" name="Submit" value="Überprüfen"></p>
	</form>
FORM;


?>
</body>
</html>
