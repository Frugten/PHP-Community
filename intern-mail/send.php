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
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Forum']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Forum' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Forum'] ="nej";
}
else{
$_SESSION['aktiv_Forum'] ="ja";
}
}

if($_SESSION['aktiv_Forum'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

$brugeren = mysql_real_escape_string($_GET['bruger']);

$til = $_REQUEST["til"];
$emne = $_REQUEST["emne"];
$mail =$_REQUEST["message"];


if(isset($_POST['prove']) && !empty($til) && !empty($emne) && !empty($mail)) 
{ 

$_SESSION['til'] = $til;
$_SESSION['emne'] = $emne;
$_SESSION['mail'] = $mail;
$_SESSION['prove'] = 1;

     header("Location: $side/intern-mail/send-ny.php?menu=$menu&bruger=$brugeren#eksempel");

}
else if(isset($_POST['send']) && !empty($til) && !empty($emne) && !empty($mail)) 
{
$til = mysql_real_escape_string($til);
$emne = mysql_real_escape_string($emne);
$mail = mysql_real_escape_string($mail);

//indsæt data i databasen

    mysql_query("INSERT INTO mail_ind (fra, til, emne, mail, sendt, laest)
    values('$bruger', '$til', '$emne', '$mail', NOW(), 'nej')") or die(mysql_error());
    
    $mail_id = mysql_result(mysql_query("SELECT LAST_INSERT_ID()"),0);
    
    mysql_query("INSERT INTO mail_ud (til, fra, emne, mail, sendt, indid, laest)
    values('$til', '$bruger', '$emne', '$mail', NOW(), '$mail_id', 'nej')") or die(mysql_error());

$smail = mysql_query("SELECT bruger FROM mail_abb WHERE bruger = '$til' AND gruppe = 'intern_mail'") or die(mysql_error());
while ( $t = mysql_fetch_array($smail))
{
$brugernavn = $t['bruger'];
$sgmail = mysql_query("SELECT email FROM brugere WHERE brugernavn = '$brugernavn'") or die(mysql_error());
while ( $t = mysql_fetch_array($sgmail))
{

$startmail = $t['email'];

	$html_mail = "hej $brugernavn<br>";
	$html_mail .= "Denne mail er afsendt automatisk, så den kan ikke besvares<br><br>";
	$html_mail .= "Du har modtaget en ny intern mail på $side.<br>";
	$html_mail .= "Du kan læse mailen ved at følge dette link<br>";
	$html_mail .= "<a href='$side/intern-mail/laes-mail.php?menu=$menu&id=$mail_id'>$side/intern-mail/laes-mail.php?menu=$menu&id=$mail_id</a><br><br>";
	$html_mail .= "Ønsker du ikke længere at modtage denne slags mails kan du rette det under din profil på siden";
	
	$alm_mail = "hej $brugernavn\r\n";
	$alm_mail .= "Denne mail er afsendt automatisk, så den kan ikke besvares\r\n\r\n";
	$alm_mail .= "Du har modtaget en ny intern mail på $side.\r\n";
	$alm_mail .= "Du kan læse mailen ved at følge dette link\r\n";
	$alm_mail .= "$side/intern-mail/laes-mail.php?menu=$menu&id=$mail_id\r\n\r\n";
	$alm_mail .= "Ønsker du ikke længere at modtage denne slags mails kan du rette det under din profil på siden";

require("../phpmailer/class.phpmailer.php");
$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Ny intern mail";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($startmail, $brugernavn);
$mail->Send();
}
}
$_SESSION['til'] = "";
$_SESSION['emne'] = "";
$_SESSION['mail'] = "";
$_SESSION['prove'] = "";

 header("Location: $side/intern-mail/udbakke.php?menu=$menu");
}
else
{
$_SESSION['emne'] = $emne;
$_SESSION['mail'] = $mail;

echo"Du skal udfylde alle felter. For at komme tilbage klik <a href='send-ny.php?menu=$menu&bruger=$brugeren'>her</a>";
}
?>