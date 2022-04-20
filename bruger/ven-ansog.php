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
//Al din kode herunder

$id = mysql_real_escape_string($_GET['id']);
if(!empty ($id))
{

$resultat = mysql_query("SELECT brugernavn,email FROM brugere WHERE brugerid = '$id'");//Spørger efter ID
$show = mysql_fetch_array($resultat);

$brugeren = $show[brugernavn];
$email = $show[email];

$bresultat = mysql_query("SELECT brugerid FROM brugere WHERE brugernavn = '$bruger'");//Spørger efter ID
$bshow = mysql_fetch_array($bresultat);

$ven_id = $bshow[brugerid];

$vresultat = mysql_query("SELECT ven_ansogid FROM ven_ansog WHERE bruger = '$brugeren' AND ven='$bruger'");//Spørger efter ID
$vnumber = mysql_num_rows($vresultat);//Tæller antaller af resultater

if($vnumber == 0)
{
$tal = rand(0, 1000000);
$ntal = md5($tal);
$ntal = substr($ntal, 0, 150);

mysql_query("INSERT INTO ven_ansog (bruger, bruger_id, ven, ven_id, ansog_id)
values('$brugeren', '$id', '$bruger', '$ven_id', '$ntal')") or die(mysql_error());
}
else
{

}
require("../phpmailer/class.phpmailer.php");


    $html_mail  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES<br>";
    $html_mail .= "$bruger har ansøgt at komme på din venneliste på $side. ";
    $html_mail .= "Log ind på din bruger for at godkende eller afvise ansøgningen";

    $alm_mail  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES\r\n";
    $alm_mail .= "$bruger har ansøgt at komme på din venneliste på $side. ";
    $alm_mail .= "Log ind på din bruger for at godkende eller afvise ansøgningen";

$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Vennelist ansøgning";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($email, $brugeren);
$mail->Send();

 header("Location: $side/bruger/profil.php?menu=$menu&id=$id");
}
else
{
 header("Location: $side/bruger/profil.php?menu=$menu&id=$id");
}
?>