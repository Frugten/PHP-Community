<?
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
//Tjekker om modulet er aktivt
if(empty ($_SESSION['aktiv_Post']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'nej' AND admin='brugermenu'");//Sp�rger efter ID
$number = mysql_num_rows($resultat);//T�ller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Post'] ="nej";
}
else{
$_SESSION['aktiv_Post'] ="ja";
}
}

if($_SESSION['aktiv_Post'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//aktiv modul tjek slut

//Al din kode herunder

$id = mysql_real_escape_string($_GET['mail']);

$resultat = mysql_query("SELECT fra, emne FROM mail_ind WHERE indid= '$id' AND til = '$bruger'");//Sp�rger efter ID
$number = mysql_num_rows($resultat);//T�ller antaller af resultater

if ($number == 1)
{
$show = mysql_fetch_array($resultat);
$fra = $show[fra];
$emne = $show[emne];

$_SESSION['emne'] = "SV $emne";

$brresultat = mysql_query("SELECT brugerid FROM brugere WHERE brugernavn= '$fra'");//Sp�rger efter ID
$brshow = mysql_fetch_array($brresultat);
$brugerid = $brshow[brugerid];

header("Location: send-ny.php?menu=$menu&bruger=$brugerid");//Sender brugeren videre

}
else
{
header("Location: indbakke.php?menu=$menu");//Sender brugeren videre
}
?>