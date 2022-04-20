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


$overskrift = $_REQUEST["overskrift"];
$tekst = $_REQUEST["message"];
$gr = mysql_real_escape_string($_GET['gr']);


if(isset($_POST['prove']) && !empty($overskrift) && !empty($tekst) && !empty($gr)) 
{ 

$_SESSION['overskrift'] = $overskrift;
$_SESSION['teksten'] = $tekst;
$_SESSION['prove'] = 1;

     header("Location: $side/forum/ny-traad.php?menu=$menu&gr=$gr");

}
else if(isset($_POST['send']) && !empty($overskrift) && !empty($tekst) && !empty($gr)) 
{
//tjekker om der er skrevet noget i formularen

$overskrift = mysql_real_escape_string($overskrift);
$tekst = mysql_real_escape_string($tekst);
$abboner = mysql_real_escape_string($_REQUEST["abboner"]);

//indsæt data i databasen


    mysql_query("INSERT INTO forumtraad (traadID, overskrift, tekst, bruger, dato, grid, parent, spg, anmeldt)
    values(0, '$overskrift', '$tekst', '$bruger', NOW(), '$gr', 0, 0, 0)") or die(mysql_error());
    
    $traad = mysql_result(mysql_query("SELECT LAST_INSERT_ID()"),0);
    
mysql_query("UPDATE forumtraad SET parent = $traad WHERE traadID='$traad'") or die(mysql_error());

if($abboner == "ja")
{
   mysql_query("INSERT INTO forum_abb (brugernavn, forumgr, traad)
    values('$bruger', '$gr', '$traad')") or die(mysql_error());
}

$smail = mysql_query("SELECT brugernavn FROM forum_abb WHERE brugernavn != '$bruger' AND forumgr = '$gr' AND traad ='0'") or die(mysql_error());
while ( $t = mysql_fetch_array($smail))
{
$brugernavn = $t['brugernavn'];

$smail = mysql_query("SELECT email FROM brugere WHERE brugernavn = '$brugernavn'") or die(mysql_error());
while ( $t = mysql_fetch_array($smail))
{

$startmail = $t['email'];

	$html_mail = "hej $brugernavn<br>";
	$html_mail .= "Denne mail er afsendt automatisk, så den kan ikke besvares<br><br>";
	$html_mail .= "Der er oprettet en ny tråd i en gruppe du følger med i.<br>";
	$html_mail .= "Du kan læse forum tråden ved at følge dette link<br>";
	$html_mail .= "<a href='$side/forum/laesforum.php?menu=$menu&gr=$gr&traad=$traad'>$side/forum/laesforum.php?gr=$gr&traad=$traad</a><br><br>";
	$html_mail .= "Ønsker du ikke længere at modtage denne slags mails kan du klikke på linket 'Stop med at abonner på denne gruppe' ";
	$html_mail .= "under denne forum gruppe ";
	
	$alm_mail  = "hej $brugernavn\r\n";
	$alm_mail .= "Denne mail er afsendt automatisk, så den kan ikke besvares\r\n\r\n";
	$alm_mail .= "Der er oprettet en ny tråd i en gruppe du følger med i.\r\n";
	$alm_mail .= "Du kan læse forum tråden ved at følge dette link<br>";
	$alm_mail .= "$side/forum/laesforum.php?menu=$menu&gr=$gr&traad=$traad\r\n\r\n";
	$alm_mail .= "Ønsker du ikke længere at modtage denne slags mails kan du klikke på linket 'Stop med at abonner på denne gruppe' ";
	$alm_mail .= "under denne forum gruppe ";

require("../phpmailer/class.phpmailer.php");
$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Ny tråd i forum gruppe";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($startmail, $brugernavn);
$mail->Send();
}
}
$_SESSION['overskrift'] = "";
$_SESSION['tekst'] = "";


 header("Location: $side/forum/laesforum.php?menu=$menu&gr=$gr&traad=$traad");
}
else
{
$_SESSION['overskrift'] = $overskrift;
$_SESSION['tekst'] = $tekst;

echo"Du skal udfylde begge felter. For at komme tilbage klik <a href='$side/forum/ny-traad.php?menu=$menu&gr=$gr'>her</a>";
}
?>