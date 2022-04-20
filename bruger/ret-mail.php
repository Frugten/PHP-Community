<?php
session_start();
$_SESSION['page'] = $_SERVER['REQUEST_URI'];
chdir('../layout/');
include '../settings/connect.php';
include '../settings/settings.php';

if(!$_SESSION['logget_in'] == 1 OR $_SESSION['logget_in'] == "ikke") 
{
$_SESSION['ikke_log'] = 1;
header("Location: $side");//Sender brugeren videre
exit;//Sørger for at resten af koden, ikke bliver udført
}
$_SESSION['menu'] = "profil";
//Al din kode herunder

if(!empty($_POST["email"]))
    {
   	$email = mysql_real_escape_string( $_POST["email"] );

    $tael = mysql_query("SELECT * FROM brugere WHERE brugernavn = '$brugernavn' OR email = '$email'") or die(mysql_error());
    $antal = mysql_num_rows($tael);

if($antal == 0)
{
$tal = rand(0, 1000000);
$ntal = md5($tal);
$ntal = substr($ntal, 0, 150); 
mysql_query("UPDATE brugere SET mailkode='$ntal' WHERE brugernavn='$bruger'") or die(mysql_error());

    $header = "From: $fra";
   	$emne    = "Aktiver ny email";

require("../phpmailer/class.phpmailer.php");


    $html_mail  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES<br>";
    $html_mail .= "Du har rettet din email på $side.<br>";
    $html_mail .= "før ændringen træder i kraft skal du klikke på dette link for at aktivere din mail<br><br>";
    $html_mail .= "<a href='$side/bruger/email-aktiver.php?menu=$menu&bruger=$bruger&email=$email&aktivkode=$ntal'>";
    $html_mail .= "$side/bruger-opret/email-aktiver.php?bruger=$bruger&email=$email&aktivkode=$ntal</a><br><br>";
    $html_mail .= "hilsen<br><br>webmaster";

    $alm_mail  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES\r\n";
    $alm_mail .= "Du har rettet din email på $side.\r\n";
    $alm_mail .= "før ændringen træder i kraft skal du klikke på dette link for at aktivere din mail\r\n\r\n";
    $alm_mail .= "$side/bruger-opret/email-aktiver.php?menu=$menu&bruger=$bruger&email=$email&aktivkode=$ntal\r\n\r\n";
    $alm_mail .= "hilsen\r\n\r\nwebmaster";

$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Aktiver din nye email";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($email, $email);
$mail->Send();

$_SESSION['aktiv_mail'] = 1;
 
  header("Location: profil.php?menu=$menu");

}
else
{
echo"Email adressen findes allerede i databsen";
}
}
else
{
echo"Email feltet skal være udfyldt";
}
?>