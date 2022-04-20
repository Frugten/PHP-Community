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

require("../phpmailer/class.phpmailer.php");

$besked = $_REQUEST["message"];
$id = mysql_real_escape_string($_GET['id']);

if (isset($_POST['prove']) && !empty($besked) && !empty($id))
{
$_SESSION['kommentar'] = $besked;
$_SESSION['prove'] = 1;

 header("Location: profil.php?menu=$menu&id=$id#svar");

}

else if(isset($_POST['send']) && !empty($besked) && !empty($id)) 
{

$besked = mysql_real_escape_string($besked);
$hent = mysql_query("SELECT brugernavn FROM brugere WHERE brugerid = '$id'") or die(mysql_error());
$show = mysql_fetch_array($hent);
$brugerbog = $show[brugernavn];

//indsæt data i databasen
    mysql_query("INSERT INTO gaestbog (brugerbog, bruger, dato, besked)
    values('$brugerbog', '$bruger', NOW(), '$besked')") or die(mysql_error());

$smail = mysql_query("SELECT bruger FROM mail_abb WHERE bruger ='$brugerbog' AND gruppe ='gaestebog'") or die(mysql_error());
$bnumber = mysql_num_rows($smail);//Tæller antaller af resultater

if($bnumber == 1)
{
$smail = mysql_query("SELECT email FROM brugere WHERE brugernavn = '$brugerbog'") or die(mysql_error());
while ( $t = mysql_fetch_array($smail))
{
$startmail = $t['email'];

	$html_mail = "hej $brugerbog<br>";
	$html_mail .= "Der er skrevet en besked i din gæstebog på $side<br><br>";
	$html_mail .= "Log ind for at læse bekskeden<br>";
	
	$alm_mail = "hej $brugerbog<br>";
	$alm_mail .= "Der er skrevet en besked i din gæstebog på $side<br><br>";
	$alm_mail .= "Log ind for at læse bekskeden<br>";

$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Ny besked i gæstebog";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($startmail, $brugerbog);
$mail->Send();

}
}    
$_SESSION['kommentar'] ="";
$_SESSION['tekst'] = "";
$_SESSION['prove'] = "";
$_SESSION['test_overskrift'] = "";
$_SESSION['test_tekst'] = "";

 header("Location: profil.php?menu=$menu&id=$id#svar");
}
else
echo"Du skal udfylde feltet, for at komme tilbage klik <a href='$side/forum/svar-traad.php?menu=$menu&gr=&traad=$id'>her</a>";

?>
