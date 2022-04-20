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
$ven = mysql_real_escape_string($_GET['ven']);
$aid = mysql_real_escape_string($_GET['aid']);
$god = mysql_real_escape_string($_GET['god']);

require("../phpmailer/class.phpmailer.php");

if(!empty ($id) && !empty ($ven) && !empty ($aid) && !empty ($god))
{
$resultat = mysql_query("SELECT ven_ansogid FROM ven_ansog WHERE ven_ansogid= '$id' AND ven_id='$ven' AND ansog_id='$aid'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater

if($god == "godkend" && $number == 1)
{
//godkend
$resultat = mysql_query("SELECT bruger, bruger_id, ven, ven_id FROM ven_ansog WHERE ven_ansogid= '$id' AND ven_id='$ven' AND ansog_id='$aid'");//Spørger efter ID
$show = mysql_fetch_array($resultat);

$brugeren = $show[bruger];
$brugeren_id = $show[bruger_id];
$vennen = $show[ven];
$ven_id = $show[ven_id];

$eresultat = mysql_query("SELECT email FROM brugere WHERE brugernavn= '$vennen'");//Spørger efter ID
$eshow = mysql_fetch_array($eresultat);
$email = $eshow[email];

mysql_query("INSERT INTO vennelister (bruger, bruger_id, ven, ven_id)
VALUES('$brugeren', '$brugeren_id', '$vennen', '$ven_id')") or die(mysql_error());

mysql_query("INSERT INTO vennelister (bruger, bruger_id, ven, ven_id)
VALUES('$vennen', '$ven_id', '$brugeren', '$brugeren_id')") or die(mysql_error());

    $html_mail  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES<br>";
    $html_mail .= "hej $vennen <br>";
    $html_mail .= "$brugeren har godkendt din ansøgning om at komme på hans/hendes venneliste på $side. ";

    $alm_mail  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES\r\n";
    $alm_mail .= "hej $vennen\r\n";
    $alm_mail .= "$brugeren har godkendt din ansøgning om at komme på hans/hendes venneliste på $side. ";

$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Vennelist ansøgning godkendt";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($email, $vennen);
$mail->Send();

mysql_query("DELETE FROM ven_ansog WHERE ven_ansogid= '$id' AND ven_id='$ven' AND ansog_id='$aid'") or die(mysql_error());

header("Location: ansogninger.php?menu=$menu");
}
else if ($god == "afvis" && $number == 1)
{
//afvis
$resultat = mysql_query("SELECT ven FROM ven_ansog WHERE ven_ansogid= '$id' AND ven_id='$ven' AND ansog_id='$aid'");//Spørger efter ID
$show = mysql_fetch_array($resultat);

$vennen = $show[ven];

$eresultat = mysql_query("SELECT email FROM brugere WHERE brugernavn= '$vennen'");//Spørger efter ID
$eshow = mysql_fetch_array($eresultat);
$email = $eshow[email];


    $html_mail  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES<br>";
    $html_mail .= "hej $vennen <br>";
    $html_mail .= "$brugeren har afvist din ansøgning om at komme på hans/hendes venneliste på $side. ";

    $alm_mail  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES\r\n";
    $alm_mail .= "hej $vennen\r\n";
    $alm_mail .= "$brugeren har afvist din ansøgning om at komme på hans/hendes venneliste på $side. ";

$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Vennelist ansøgning afvist";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($email, $ven);
$mail->Send();

mysql_query("DELETE FROM ven_ansog WHERE ven_ansogid= '$id' AND ven_id='$ven' AND ansog_id='$aid'") or die(mysql_error());

header("Location: ansogninger.php?menu=$menu");

}
else
{
header("Location: ansogninger.php?menu=$menu");
}

}
else
{
header("Location: ansogninger.php?menu=$menu");
}