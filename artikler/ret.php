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
if(empty ($_SESSION['aktiv_Artikler']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Artikler' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Artikler'] ="nej";
}
else{
$_SESSION['aktiv_Artikler'] ="ja";
}
}

if($_SESSION['aktiv_Artikler'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

$id = mysql_real_escape_string($_GET['id']);
$titel = mysql_real_escape_string($_REQUEST["titel"]);
	$beskrivelse = mysql_real_escape_string($_REQUEST["beskrivelse"]);
	$artikel = mysql_real_escape_string($_REQUEST["message"]);
	$pris = mysql_real_escape_string($_REQUEST["pris"]);

if (!empty($id) && !empty($titel) && !empty($beskrivelse) && !empty($artikel))
{
mysql_query("UPDATE artikel SET titel='$titel', beskrivelse='$beskrivelse', artikel='$artikel', pris='$pris' WHERE artikelid='$id'") or die(mysql_error());
$_SESSION['ret'] = 1;

     header("Location: $side/artikler/rette.php?menu=$menu&id=$id");
}
else
{
$_SESSION['fra'] = 1; 
header("Location: $side/artikler/rette.php?menu=$menu&id=$id");
}
?>