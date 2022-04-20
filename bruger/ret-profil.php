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

	$city = mysql_real_escape_string( $_POST["city"] );
	$postnr = mysql_real_escape_string( $_POST["postnr"] );
    $dato = mysql_real_escape_string( $_POST["dato"] );
    $maaned = mysql_real_escape_string( $_POST["maaned"] );
    $aar = mysql_real_escape_string( $_POST["aar"] );
    $kon = mysql_real_escape_string( $_POST["kon"] );
    $web = mysql_real_escape_string( $_POST["web"] );
    $interesser = mysql_real_escape_string( $_POST["interesser"] );
    $privat = mysql_real_escape_string( $_POST["privat"] );
if($privat != "ja")
{
$privat="nej";
}
mysql_query("UPDATE brugere SET city='$city', postnr='$postnr', kon='$kon', birth='$aar-$maaned-$dato', web='$web', interesser='$interesser', privat='$privat' WHERE brugernavn='$bruger'") or die(mysql_error());

  header("Location: profil.php?menu=$menu");
?>
