<?php
session_start(); // Session initialisieren

/**
 * generate captcha
 *
 * @param   _SESSIONNAME        session name
 * @return  void
 */
function generateCaptcha($_SESSIONNAME) {
    
    if(trim($_SESSIONNAME) == '') // Überprüfen ob der Sessionname leer ist
        die('Sessionname ist leer!'); // Script beenden und String ausgeben
    
    $font = './XFILES.TTF'; // TTF Schriftart für Captcha
    
    $image = imagecreate(125, 30); // Bild erstellen mit 125 Pixel Breite und 30 Pixel Höhe
    imagecolorallocate($image, 255, 255, 255); // Bild weis färben, RGB
    
    $left = 5; // Initialwert, von links 5 Pixel
    $signs = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789';
        // Alle Buchstaben und Zahlen
    $string = ''; // Der später per Zufall generierte Code
    
    for($i = 1;$i <= 6;$i++) // 6 Zeichen
    {
        $sign = $signs{rand(0, strlen($signs) - 1)};
                    /*
                        Zufälliges Zeichen aus den oben aufgelisteten
                        strlen($signs) = Zählen aller Zeichen
                        rand = Zufällig zwischen 0 und der Länge der Zeichen - 1
                        
                        Grund für diese Rechnung:
                        Mit den Geschweiften Klammern kann man auf ein Zeichen zugreifen
                        allerdings fängt man dort genauso wie im Array mit 0 an zu zählen
                        
                    */
        $string .= $sign; // Das Zeichen an den gesamten Code anhängen
        imagettftext($image, 20, rand(-10, 10), $left + (($i == 1?5:15) * $i), 25, imagecolorallocate($image, 200, 200, 200), $font, $sign);
                // Das gerade herausgesuchte Zeichen dem Bild hinzufügen
        imagettftext($image, 16, rand(-15, 15), $left + (($i == 1?5:15) * $i), 25, imagecolorallocate($image, 69, 103, 137), $font, $sign);
                // Das Zeichen noch einmal hinzufügen, damit es für einen Bot nicht zu leicht lesbar ist
    }
    
    $_SESSION[$_SESSIONNAME] = $string; // Den Code in die Session mit dem Sessionname speichern für die Überprüfung
    
    header("Content-type: image/png"); // Header für ein PNG Bild setzen
    imagepng($image); // Ausgaben des Bildes
    imagedestroy($image); // Bild zerstören
    
}

// $sessionName = 'captchacode'; // Sessionname

if(isset($_GET['captcha']))
{
    generateCaptcha($sessionName); // Funktionsaufruf, erste Parameter ist der Name für die Session
    exit(); // Script beenden, es soll keine weitere Ausgabe stattfinden
}
/*
if(isset($_POST['check'])) // Wurde das Formular abgeschickt
{
    if($_SESSION[$sessionName] == trim($_POST['captcha'])) // Stimmt die Eingabe mit dem Code überein
        $finished = true;
	$message="";
    else
        $message="Der Code ist falsch";
}
*/
?>
