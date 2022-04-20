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

	$titel = mysql_real_escape_string($_REQUEST["titel"]);
	$beskrivelse = mysql_real_escape_string($_REQUEST["beskrivelse"]);
	$artikel = mysql_real_escape_string($_REQUEST["message"]);
	$pris = mysql_real_escape_string($_REQUEST["pris"]);

$gr = mysql_real_escape_string($_GET['gr']);

if (!empty($titel) && !empty($beskrivelse) && !empty($artikel))
{
    mysql_query("INSERT INTO artikel (artikelid, grid, brugernavn, dato, titel, beskrivelse, artikel, aktiv, pris)
    values(0, '$gr', '$bruger', NOW(), '$titel', '$beskrivelse', '$artikel', 'nej', '$pris')") or die(mysql_error());
$artikelid = mysql_result(mysql_query("SELECT LAST_INSERT_ID()"),0);


$_SESSION['titel'] = "";
$_SESSION['beskrivelse'] = "";
$_SESSION['artikel'] = "";
 
    header("Location: $side/artikler/laes-ikke-aktiv.php?menu=$menu&gr=$gr&id=$artikelid");
}
else
{
$_SESSION['ikke'] = 1; 

$_SESSION['titel'] = $titel;
$_SESSION['beskrivelse'] = $beskrivelse;
$_SESSION['artikel'] = $artikel;

header("Location: $side/artikler/opret-artikel.php?menu=$menu&gr=$gr");
}
?>