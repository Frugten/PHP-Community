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
exit;//S�rger for at resten af koden, ikke bliver udf�rt
}

//Al din kode herunder
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Forum']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Forum' AND aktiv = 'nej' AND admin='brugermenu'");//Sp�rger efter ID
$number = mysql_num_rows($resultat);//T�ller antaller af resultater
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
require("../phpmailer/class.phpmailer.php");

$kommentar = mysql_real_escape_string($_REQUEST["tekst"]);

//tjekker om der er skrevet noget i formularen
if(!empty($kommentar))
{
$traad = mysql_real_escape_string($_GET[traad]);
$id = mysql_real_escape_string($_GET[id]);
$gr = mysql_real_escape_string($_GET[gr]);
$admin = mysql_query("SELECT email FROM brugere WHERE gruppe ='admin'") or die(mysql_error());
while ( $d = mysql_fetch_array($admin))
{
$mailen = $d['email'];
$trad = mysql_query("SELECT * FROM forumtraad WHERE traadID = '$id'") or die(mysql_error());
$a = mysql_fetch_array($trad);
$tekst = $a[tekst];

	$html_mail  = "hej admin<br>";
	$html_mail .= "brugeren $bruger har anmeldt dette forum indl�g<br><br>";
	$html_mail .= "<i>$tekst</i><br><br>";
	$html_mail .= "$bruger har f�lgende kommentar til indl�gget<br><br>";
	$html_mail .= "$kommentar<br><br>";
	$html_mail .= "du kan l�se hele tr�den her<br>";
	$html_mail .= "<a href='$side/forum/laesforum.php?menu=$menu&gr=$gr&traad=$traad'>$side/forum/laesforum.php?gr=$gr&traad=$traad</a><br><br>";
	$html_mail .= "og i admin delen kan du v�lge hvad du vil g�re ved tr�den f�lg linket her<br>";
	$html_mail .= "<a href='$side/admin/forum/anmeldt.php?menu=$menu&gr=$'>$side/forum/admin/anmeldt.php</a>";
	
	$alm_mail  = "hej admin\r\n";
	$alm_mail .= "brugeren $bruger har anmeldt dette forum indl�g\r\n\r\n";
	$alm_mail .= "$tekst\r\n\r\n";
	$alm_mail .= "$bruger har f�lgende kommentar til indl�gget\r\n\r\n";
	$alm_mail .= "$kommentar\r\n\r\n";
	$alm_mail .= "du kan l�se hele tr�den her\r\n";
	$alm_mail .= "$side/forum/laesforum.php?menu=$menu&gr=$gr=$gr&traad=$traad\r\n\r\n";
	$alm_mail .= "og i admin delen kan du v�lge hvad du vil g�re ved tr�den f�lg linket her\r\n";
	$alm_mail .= "$side/admin/forum/anmeldt.php?menu=$menu&gr=$";

$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Anmeldt forum indl�g";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($mailen, $mailen);
$mail->Send();


mysql_query("UPDATE forumtraad SET anmeldt = 1 WHERE traadID='$id'") or die(mysql_error());

header("Location: $side/forum/laesforum.php?menu=$menu&gr=$gr&traad=$traad");
}}
else
{
echo"du skal skrive en grund i feltet. klik <a href='$side/forum/anmeld.php?menu=$menu&id=$id&traad=$traad'>her</a> for at komme tilbage";
}
?>